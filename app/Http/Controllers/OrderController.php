<?php

namespace App\Http\Controllers;

use App\Models\User;
use Inertia\Inertia;
use App\Models\Order;
use App\Models\OrderNote;
use App\Models\ContentType;
use Illuminate\Http\Request;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Eloquent\Builder as EloquentBuilder;

class OrderController extends Controller
{
    public function index(Request $request)
    {
        return Inertia::render('CRM/Orders/OrderListing');
    }

    public function getOrders(Request $request)
    {           
        $query = Order::query();
        $query->with([
            'user:id,full_name,username,phone_number,email,country,address,site_id',
            'user.site:id,name'
        ]);

        // Handle search functionality
        $search = $request->input('search');
        if ($search) {
            $query->where(function ($query) use ($search) {
                // Search in the current model's fields
                $query->where('trade_id', 'like', '%' . $search . '%');
                
                // Search in the related 'user' fields
                $query->orWhereHas('user', function ($userQuery) use ($search) {
                    $userQuery->where('full_name', 'like', '%' . $search . '%')
                        ->orWhere('username', 'like', '%' . $search . '%')
                        ->orWhere('email', 'like', '%' . $search . '%');
                });

                // Search in the related 'user.site' fields
                $query->orWhereHas('user.site', function ($siteQuery) use ($search) {
                    $siteQuery->where('name', 'like', '%' . $search . '%');
                });
            });
        }
    
        // Handle date range filtering
        $startDate = $request->input('startDate');
        $endDate = $request->input('endDate');
    
        if ($startDate && $endDate) {
            $query->whereBetween('created_at', [$startDate, $endDate]);
        } elseif ($startDate) {
            $query->where('created_at', '>=', $startDate);
        } elseif ($endDate) {
            $query->where('created_at', '<=', $endDate);
        }
    
        // Handle sorting
        $sortField = $request->input('sortField', 'created_at'); // Default to 'created_at'
        $sortOrder = $request->input('sortOrder', -1); // 1 for ascending, -1 for descending
        $query->orderBy($sortField, $sortOrder == 1 ? 'asc' : 'desc');
    
        // Get total count before pagination
        $totalRecords = $query->count();
    
        // Handle pagination
        $rowsPerPage = $request->input('rows', 15); // Default to 15 if 'rows' not provided
        $currentPage = $request->input('page', 0) + 1; // Laravel uses 1-based page numbers, PrimeVue uses 0-based
    
        // Paginate the query
        $data = $query->simplePaginate($rowsPerPage, ['*'], 'page', $currentPage);
        
        
        $statusArray = [ 
            "Pending", "In progress", "Active", "Cancelled", "Cancelled (approved)", "Cancelled (non-authorized)", 
            "Pending allocation", "Pending payment", "Pending clearance", "Cleared", "Trade confirmation required"
        ];
        $limbStageArray  = [ 
            "ALLO", "Allo + docs", "Tt", "Cleared", "Cancelled", "Cancelled - bank block", "Cancelled - HTR", 
            "Cancelled - order drop", "Cancelled refuse trade", "Kicked", "Carry over", "Free switch" 
        ];

        foreach ($data as $order) {
            // Handle client_stage attribute
            if (!is_null($order->status) && $order->status !== '') {
                $order->status = $statusArray[$order->status - 1];
            }
        
            // Handle limb attribute
            if (!is_null($order->limb_stage) && $order->limb_stage !== '') {
                $order->limb_stage = $limbStageArray[$order->limb_stage - 1];
            }
        }
            
        return response()->json([
            'data' => $data->items(),
            'totalRecords' => $totalRecords, // Matches PrimeVue's 'totalRecords' field
        ]);
    }
    
    public function detail($id)
    {
        $order = Order::find($id);

        return Inertia::render('CRM/Orders/OrderDetails/OrderDetails', [
            'order' => $order,
        ]);
    }

    public function getOrderData(Request $request)
    {
        // Validate the input to ensure 'id' is provided
        $request->validate([
            'id' => 'required'
        ]);
        
        // Retrieve the specific order by ID
        $order = Order::with([
            'user:id,full_name,username,phone_number,email,country,address,site_id',
            'user.site:id,name'
        ])->find($request->id);

        // Define status and limb stage mappings
        $statusArray = [
            "Pending", "In progress", "Active", "Cancelled", "Cancelled (approved)", "Cancelled (non-authorized)",
            "Pending allocation", "Pending payment", "Pending clearance", "Cleared", "Trade confirmation required"
        ];
        $limbStageArray = [
            "ALLO", "Allo + docs", "Tt", "Cleared", "Cancelled", "Cancelled - bank block", "Cancelled - HTR",
            "Cancelled - order drop", "Cancelled refuse trade", "Kicked", "Carry over", "Free switch"
        ];

        // Transform the status attribute
        if (!is_null($order->status) && $order->status !== '') {
            $order->status = $statusArray[$order->status - 1] ?? $order->status; // Fallback to original if out of range
        }

        // Transform the limb_stage attribute
        if (!is_null($order->limb_stage) && $order->limb_stage !== '') {
            $order->limb_stage = $limbStageArray[$order->limb_stage - 1] ?? $order->limb_stage; // Fallback to original if out of range
        }

        return response()->json([
            'orderDetail' => $order
        ]);

    }

    public function getOrderNotes(Request $request)
    {
        $existingOrderNotes = OrderNote::where('order_id', $request->id)
                                        ->with(['orderNoteCreator:id,username,site_id', 'orderNoteCreator.site:id,name'])
                                        ->orderByDesc('id')
                                        ->get();

        foreach($existingOrderNotes as $key=>$value) {
            $existingOrderNotes[$key]->user_editable = boolval($existingOrderNotes[$key]->user_editable);
        }


        return response()->json($existingOrderNotes);
    }

    public function getOrderLogEntries(Request $request)
    {
        $contentTypeId = ContentType::with('auditLogEntries')
                                        ->where('app_label', 'core')
                                        ->where('model', 'order')
                                        ->select('id')
                                        ->get();

        $leadFrontLogEntries = [];

        foreach ($contentTypeId[0]->auditLogEntries as $key => $value) {
            if ((string)$value->object_id === $request->id){
                array_push($leadFrontLogEntries, $value);
            }
        }

        return response()->json($leadFrontLogEntries);
    }
}
