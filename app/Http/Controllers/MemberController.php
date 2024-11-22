<?php

namespace App\Http\Controllers;

use App\Models\Site;
use App\Models\User;
use Inertia\Inertia;
use App\Models\ContentType;
use Illuminate\Http\Request;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\Password;
use Illuminate\Support\Facades\Redirect;

class MemberController extends Controller
{
    public function index(Request $request)
    {
        return Inertia::render('CRM/Member/Listings/MemberListing');
    }

    public function getMemberListing(Request $request)
    {
        // Start building the query
        $query = User::query()
            ->with(['site', 'accountManager:id,username,site_id', 'accountManager.site:id,name']);
        
        // Handle search functionality
        $search = $request->input('search');
        if ($search) {
            $query->where(function ($query) use ($search) {
                $query->where('full_name', 'like', '%' . $search . '%')
                    ->orWhere('user_name', 'like', '%' . $search . '%')
                    ->orWhere('email', 'like', '%' . $search . '%')
                    ->orWhere('phone_number', 'like', '%' . $search . '%')
                    ->orWhere('country', 'like', '%' . $search . '%');
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
    
        // Transformation mappings
        $ranks = ["Normal", "VIP"];
        $client_stages = ["ALLO", "NO ALLO", "REMM", "TT", "CLEARED", "PENDING", "KICKED", "CARRIED OVER", "FREE SWITCH", "CXL", "CXL-CLIENT DROPPED"];
        $kyc_statuses = ["Not started", "Pending documents", "In progress", "Rejected", "Approved"];
        $acc_types = ["Individual", "Joint", "Trust", "Corporate"];

        // Transform the data items
        foreach ($data->items() as $user) {
            if (!is_null($user->client_stage) && $user->client_stage !== '') {
                $user->client_stage = $client_stages[$user->client_stage - 1];
            }

            if (!is_null($user->rank) && $user->rank !== '') {
                $user->rank = $ranks[$user->rank - 1];
            }

            if (!is_null($user->kyc_status) && $user->kyc_status !== '') {
                $user->kyc_status = $kyc_statuses[$user->kyc_status - 1];
            }

            if (!is_null($user->account_type) && $user->account_type !== '') {
                $user->account_type = $acc_types[$user->account_type - 1];
            }
        }

        // Prepare response data for PrimeVue DataTable
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
        $member = User::find($id);

        return Inertia::render('CRM/Member/Details/MemberDetails', [
            'member' => $member,
        ]);
    }

    public function getMemberData(Request $request)
    {
        // Validate the input to ensure 'id' is provided
        $request->validate([
            'id' => 'required'
        ]);
    
        // Fetch the user with their associated data
        $user = User::with(['site', 'accountManager:id,username,site_id', 'accountManager.site:id,name'])
            ->find($request->input('id'));
    
        if (!$user) {
            return response()->json(['error' => 'User not found'], 404);
        }
    
        // Transformation mappings
        $ranks = ["Normal", "VIP"];
        $client_stages = ["ALLO", "NO ALLO", "REMM", "TT", "CLEARED", "PENDING", "KICKED", "CARRIED OVER", "FREE SWITCH", "CXL", "CXL-CLIENT DROPPED"];
        $kyc_statuses = ["Not started", "Pending documents", "In progress", "Rejected", "Approved"];
        $acc_types = ["Individual", "Joint", "Trust", "Corporate"];
    
        // Transform the user data
        if (!is_null($user->client_stage) && $user->client_stage !== '') {
            $user->client_stage = $client_stages[$user->client_stage - 1];
        }
    
        if (!is_null($user->rank) && $user->rank !== '') {
            $user->rank = $ranks[$user->rank - 1];
        }
    
        if (!is_null($user->kyc_status) && $user->kyc_status !== '') {
            $user->kyc_status = $kyc_statuses[$user->kyc_status - 1];
        }
    
        if (!is_null($user->account_type) && $user->account_type !== '') {
            $user->account_type = $acc_types[$user->account_type - 1];
        }
    
        // Return the transformed user data under 'memberDetail' key
        return response()->json([
            'memberDetail' => $user,
        ]);
    }
    
    public function getMemberOrders(Request $request)
    {
        $orders = User::find($request->id)
                        ->orders()
                        ->latest()
                        ->get();

        $limb_stages = [ "ALLO", "Allo + docs", "TT", "CLEARED", "Cancelled", "Cancelled - bank block", "Cancelled - HTR", 
                        "Cancelled - order drop", "Cancelled refuse trade", "Kicked", "Carry over", "Free switch" ];

        foreach ($orders as $userOrders) {
            // Handle limb_stage attribute
            if (!is_null($userOrders->limb_stage) && $userOrders->limb_stage !== '') {
                $userOrders->limb_stage = $limb_stages[$userOrders->limb_stage - 1];
            }
        }

        return response()->json($orders);
    }

    public function getLatestUsersClients()
    {
        $data = User::with([
                            'site', 
                            'accountManager:id,username,site_id', 
                            'accountManager.site:id,name'
                        ])
                        ->latest()
                        ->take(5)
                        ->get();
        
        return response()->json($data);
    }

}
