<?php

namespace App\Http\Controllers;

use Inertia\Inertia;
use App\Models\Application;
use App\Models\ContentType;
use Illuminate\Http\Request;

class ApplicationController extends Controller
{
    public function index(Request $request)
    {
        return Inertia::render('CRM/Applications/ApplicationListing');
    }

    public function getApplications(Request $request)
    {   
        $query = Application::query();
        // $query->with([
        //     'sites',
        // ]);
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
        
        $acc_types = ["Individual", "Joint", "Trust", "Corporate"];

        foreach ($data as $applicant) {
            if (!is_null($applicant->status) && $applicant->status !== '') {
                $applicant->status = $statusArray[$applicant->status - 1];
            }

            if (!is_null($applicant->account_type) && $applicant->account_type !== '') {
                $applicant->account_type = $acc_types[$applicant->account_type - 1];
            }
        }

        return response()->json([
            'data' => $data->items(),
            'totalRecords' => $totalRecords, // Matches PrimeVue's 'totalRecords' field
        ]);
    }

    public function detail($id)
    {
        $application = Application::find($id);

        return Inertia::render('CRM/Applications/Details/ApplicantDetails', [
            'application' => $application,
        ]);
    }

    public function getApplicationData(Request $request)
    {
        // Validate the input to ensure 'id' is provided
        $request->validate([
            'id' => 'required'
        ]);
    
        // Fetch the application with their associated data
        $application = Application::with(['user', 'site'])
            ->find($request->input('id'));
    
        if (!$application) {
            return response()->json(['error' => 'Application not found'], 404);
        }
        
        $statusArray = [ 
            "Pending", "Approved", "Cancelled"
        ];
        
        $acc_types = ["Individual", "Joint", "Trust", "Corporate"];

        if (!is_null(value: $application->status) && $application->status !== '') {
            $application->status = $statusArray[$application->status - 1] ?? $application->status; // Fallback to original if out of range
        }

        if (!is_null(value: $application->account_type) && $application->account_type !== '') {
            $application->account_type = $acc_types[$application->account_type - 1] ?? $application->account_type; // Fallback to original if out of range
        }

        // Return the transformed application data under 'applicationDetail' key
        return response()->json([
            'applicationDetail' => $application,
        ]);
    }

    public function getApplicationLogEntries(Request $request)
    {
        $contentTypeId = ContentType::with('auditLogEntries')
                                        ->where('app_label', 'core')
                                        ->where('model', 'application')
                                        ->select('id')
                                        ->get();

        $paymentMethodLogEntries = [];

        foreach ($contentTypeId[0]->auditLogEntries as $key => $value) {
            if ((string)$value->object_id === $request->id){
                array_push($paymentMethodLogEntries, $value);
            }
        }

        return response()->json($paymentMethodLogEntries);
    }
}
