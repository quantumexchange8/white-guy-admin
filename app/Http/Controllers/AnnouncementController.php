<?php

namespace App\Http\Controllers;

use App\Models\Announcement;
use Inertia\Inertia;
use App\Models\ContentType;
use Illuminate\Http\Request;

class AnnouncementController extends Controller
{
    public function index(Request $request)
    {
        return Inertia::render('CRM/Announcements/AnnouncementListing');
    }

    public function getAnnouncements(Request $request)
    {   
        $query = Announcement::query();
        $query->with([
            'sites',
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

    public function getAnnouncementLogEntries(Request $request)
    {
        $contentTypeId = ContentType::with('auditLogEntries')
                                        ->where('app_label', 'core')
                                        ->where('model', 'announcement')
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
