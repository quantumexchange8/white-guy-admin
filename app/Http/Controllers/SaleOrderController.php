<?php

namespace App\Http\Controllers;

use App\Exports\SaleOrdersExport;
use App\Http\Requests\SaleOrderItemRequest;
use App\Http\Requests\SaleOrderRequest;
use App\Models\ContentType;
use App\Models\SaleOrder;
use App\Models\SaleOrderItem;
use App\Models\Site;
use App\Models\User;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Redirect;
use Illuminate\Support\Facades\Schema;
use Inertia\Inertia;
use Illuminate\Support\Str;

class SaleOrderController extends Controller
{
    public function index(Request $request)
    {
        return Inertia::render('CRM/SaleOrders/SaleOrderListing');
    }

    public function getSaleOrders(Request $request)
    {   
        // Default fetch all on load
        $query = SaleOrder::query();
        $query->with([
                            'creator:id,username,site_id', 
                            'creator.site:id,name', 
                            'site:id,name', 
        ]);
        // Handle search functionality
        $search = $request->input('search');
        if ($search) {
            $query->where(function ($query) use ($search) {
                // Search in the current model's fields
                $query->where('registered_name', 'like', '%' . $search . '%')
                    ->orWhere('email', 'like', '%' . $search . '%');
                
                // Search in the related 'user' fields
                $query->orWhereHas('creator', function ($userQuery) use ($search) {
                    $userQuery->where('username', 'like', '%' . $search . '%')
                        ->orWhere('email', 'like', '%' . $search . '%');
                });

                // Search in the related 'user.site' fields
                $query->orWhereHas('creator.site', function ($siteQuery) use ($search) {
                    $siteQuery->where('name', 'like', '%' . $search . '%');
                });

                // Search in the related 'user.site' fields
                $query->orWhereHas('site', function ($siteQuery) use ($search) {
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
        
        return response()->json([
            'data' => $data->items(),
            'totalRecords' => $totalRecords, // Matches PrimeVue's 'totalRecords' field
            'current_page' => $data->currentPage(),
            'first_page_url' => $data->url(1),
            'next_page_url' => $data->hasMorePages() ? $data->nextPageUrl() : null,
            'prev_page_url' => $data->previousPageUrl(),
            'path' => $data->path(),
            'per_page' => $data->perPage(),
            'from' => $data->firstItem(),
            'to' => $data->lastItem(),
        ]);
    }

    public function detail($id)
    {
        $sale_order = SaleOrder::find($id);

        return Inertia::render('CRM/SaleOrders/SaleOrderDetails/SaleOrderDetails', [
            'sale_order' => $sale_order,
        ]);
    }

    public function getSaleOrderData(Request $request)
    {
        // Validate the incoming request for 'id'
        $request->validate([
            'id' => 'required',
        ]);

        $id = $request->input('id');

        // Fetch the specific SaleOrder with related data
        $saleOrder = SaleOrder::with([
            'creator:id,username,site_id',
            'creator.site:id,name',
            'site:id,name',
        ])->findOrFail($id);

        // Return the SaleOrder data as a JSON response
        return response()->json([
            'saleOrderDetail' => $saleOrder,
        ]);
    }

    public function getSaleOrderItems(Request $request)
    {
        $data = SaleOrder::find($request->id);

        return response()->json($data->saleOrderItems);
    }

    public function getSaleOrderLogEntries(Request $request)
    {
        $contentTypeId = ContentType::with('auditLogEntries')
                                        ->where('app_label', 'core')
                                        ->where('model', 'saleorder')
                                        ->select('id')
                                        ->orderByDesc('id')
                                        ->get();

        $saleOrderLogEntries = [];

        foreach ($contentTypeId[0]->auditLogEntries as $key => $value) {
            if ((string)$value->object_id === $request->id){
                array_push($saleOrderLogEntries, $value);
            }
        }

        return response()->json($saleOrderLogEntries);
    }
    
    public function getAllSites()
    {
        $sites = Site::all();

        return response()->json($sites);
    }

    public function getAllUsers() 
    {
        $data = User::getAllUsersWithRelationships()
                        ->map(function ($user) {
                            return [
                                'id' => $user->id,
                                'value' => $user->username . ' (' . $user->site->name . ')',
                            ];
                        });
        
        return response()->json($data);
    }
}
