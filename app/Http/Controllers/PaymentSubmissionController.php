<?php

namespace App\Http\Controllers;

use App\Exports\PaymentSubmissionExport;
use App\Http\Requests\PaymentSubmissionRequest;
use App\Models\ContentType;
use App\Models\PaymentMethod;
use Carbon\Carbon;
use App\Models\PaymentSubmission;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Inertia\Inertia;
use Illuminate\Database\Eloquent\Builder as EloquentBuilder;
use Illuminate\Support\Facades\Redirect;
use Illuminate\Support\Facades\Schema;

class PaymentSubmissionController extends Controller
{
    public function index(Request $request)
    {
        return Inertia::render('CRM/PaymentSubmissions/PaymentSubmissionListing');
    }

    public function getPaymentSubmissions(Request $request)
    {
        $query = PaymentSubmission::query();
        $query->with([
            'user:id,full_name,username,phone_number,email,country,address,site_id',
            'user.site:id,name',
            'paymentMethod:id,title'
        ]);
        // Handle search functionality
        $search = $request->input('search');
        if ($search) {
            $query->where(function ($query) use ($search) {
                // Search in the current model's fields
                // $query->where('registered_name', 'like', '%' . $search . '%')
                //     ->orWhere('email', 'like', '%' . $search . '%');
                
                // // Search in the related 'user' fields
                // $query->orWhereHas('creator', function ($userQuery) use ($search) {
                //     $userQuery->where('username', 'like', '%' . $search . '%')
                //         ->orWhere('email', 'like', '%' . $search . '%');
                // });

                // // Search in the related 'user.site' fields
                // $query->orWhereHas('creator.site', function ($siteQuery) use ($search) {
                //     $siteQuery->where('name', 'like', '%' . $search . '%');
                // });

                // // Search in the related 'user.site' fields
                // $query->orWhereHas('site', function ($siteQuery) use ($search) {
                //     $siteQuery->where('name', 'like', '%' . $search . '%');
                // });
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
            "Pending", "Approved", "Cancelled"
        ];
        
        foreach ($data as $paymentSubmission) {
            if (!is_null($paymentSubmission->status) && $paymentSubmission->status !== '') {
                $paymentSubmission->status = $statusArray[$paymentSubmission->status - 1];
            }
        }
    
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
    
    public function getPaymentSubmissionLogEntries(Request $request)
    {
        $contentTypeId = ContentType::with('auditLogEntries')
                                        ->where('app_label', 'core')
                                        ->where('model', 'paymentsubmission')
                                        ->select('id')
                                        ->get();

        $paymentSubmissionLogEntries = [];

        foreach ($contentTypeId[0]->auditLogEntries as $key => $value) {
            if ((string)$value->object_id === $request->id){
                array_push($paymentSubmissionLogEntries, $value);
            }
        }

        return response()->json($paymentSubmissionLogEntries);
    }

    public function approvePaymentSubmission(Request $request)
    {
        $data = $request->all();
        $paymentSubmissionData = PaymentSubmission::find($data['id']);
    
        if (isset($paymentSubmissionData)) {
            $paymentSubmissionData->update([
                'status' => 2,
                'approved_at' => preg_replace('/(\d{2})(\d{2})$/', '$1', $data['approved_at']),
            ]);
            
            $paymentSubmissionData->save();
        }

        return response()->json('You have successfully approved the payment submission!');
    }
    
}
