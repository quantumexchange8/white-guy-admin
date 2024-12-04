<?php

namespace App\Http\Controllers;

use App\Exports\LeadsExport;
use App\Models\LeadAppointmentLabel;
use App\Models\LeadContactOutcome;
use App\Models\LeadStage;
use Carbon\Carbon;
use Inertia\Inertia;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Validator;
use App\Http\Requests\LeadFrontRequest;
use App\Http\Requests\LeadNotesRequest;
use App\Http\Requests\LeadRequest;
use App\Imports\LeadsImport;
use App\Models\AuditlogLogentry;
use App\Models\ContentType;
use App\Models\LeadFront;
use App\Models\LeadNote;
use App\Models\Lead;
use App\Models\LeadChangelog;
use App\Models\LeadDuplicated;
use App\Models\User;
use Illuminate\Support\Facades\Redirect;
use Maatwebsite\Excel\Facades\Excel;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\Cache;
use Illuminate\Database\Eloquent\Builder;

class LeadController extends Controller
{
    public function index(Request $request)
    {
        return Inertia::render('CRM/Leads/Lead');
    }

    public function getLeads(Request $request)
    {
        // Initialize the query with eager loading for related models
        $query = Lead::with([
            'leadCreator:id,username,site_id',
            'leadCreator.site:id,name',
            'assignee:id,username,site_id',
            'assignee.site:id,name',
            'leadnotes' => function ($query) {
                $query->select('id', 'lead_id', 'note', 'created_by_id');
            },
            'leadnotes.leadNoteCreator:id,username,site_id',
            'leadnotes.leadNoteCreator.site:id,name',
            'contactOutcome:id,title',
            'stage:id,title',
            'appointmentLabel:id,title'
        ])
        ->select([
            'id', 'date', 'first_name', 'last_name', 'email', 'created_at', 'assignee_id', 'country', 'vc', 'phone_number', 
            'data_source', 'contacted_at', 'give_up_at', 'data_code', 'data_type'
        ]);
    
        // Handle search functionality
        $search = $request->input('search');
        if ($search) {
            $query->where(function ($query) use ($search) {
                $query->where('first_name', 'like', '%' . $search . '%')
                    ->orWhere('last_name', 'like', '%' . $search . '%')
                    ->orWhere('email', 'like', '%' . $search . '%')
                    ->orWhere('phone_number', 'like', '%' . $search . '%')
                    ->orWhere('country', 'like', '%' . $search . '%');
            });
        }

        // Handle date range filtering
        $startDate = $request->input('startDate');
        $endDate = $request->input('endDate');

        // Apply date range filtering if both dates are present
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
        $lead = Lead::find($id);

        return Inertia::render('CRM/Leads/LeadDetails/LeadDetails', [
            'lead' => $lead,
        ]);
    }

    public function getLeadData(Request $request)
    {
        $lead = Lead::with([
            'leadCreator:id,username,site_id',
            'leadCreator.site:id,name',
            'assignee:id,username,site_id',
            'assignee.site:id,name',
            'contactOutcome:id,title',
            'stage:id,title',
            'appointmentLabel:id,title'
        ])
        ->find($request->id);

        $leadFront = Lead::find($request->id)->leadfront;
        // $userData = [
        //     'id' => $user->id,
        //     'name' => $user->first_name,
        //     'email' => $user->email,
        //     'dial_code' => $user->dial_code,
        //     'phone' => $user->phone,
        //     'upline_id' => $user->upline_id,
        //     'role' => $user->role,
        //     'id_number' => $user->id_number,
        //     'status' => $user->status,
        //     'profile_photo' => $user->getFirstMediaUrl('profile_photo'),
        //     'team_id' => $user->teamHasUser->team_id ?? null,
        //     'team_name' => $user->teamHasUser->team->name ?? null,
        //     'team_color' => $user->teamHasUser->team->color ?? null,
        //     'upline_name' => $user->upline->first_name ?? null,
        //     'total_direct_member' => $user->directChildren->where('role', 'member')->count(),
        //     'total_direct_agent' => $user->directChildren->where('role', 'agent')->count(),
        //     'kyc_verification' => $user->getFirstMedia('kyc_verification'),
        //     'kyc_approved_at' => $user->kyc_approved_at,
        // ];

        return response()->json([
            'leadDetail' => $lead,
            'leadFront' => $leadFront
        ]);
    }

    public function getLeadNotes(Request $request)
    {
        // $existingLeadNotes = LeadNote::where('linked_lead', $id)
        //                                 ->orderBy('created_at', 'desc')
        //                                 ->get()
        //                                 ->map(function ($note) {
        //                                     $note['source'] = 'lead_note';
        //                                     return $note;
        //                                 });
        $existingLeadNotes = LeadNote::where('lead_id', $request->id)
                                        ->with(['leadNoteCreator:id,username,site_id', 'leadNoteCreator.site:id,name'])
                                        ->orderByDesc('id')
                                        ->get();

        foreach($existingLeadNotes as $key=>$value) {
            $existingLeadNotes[$key]->user_editable = boolval($existingLeadNotes[$key]->user_editable);
        }


        return response()->json($existingLeadNotes);
    }

    public function getLeadFront(string $id)
    {
        return response()->json(Lead::find($id)->leadfront);
    }

    // public function getLeadChangelogs(string $id)
    // {
    //     $existingLeadChangelogs = LeadChangelog::where('lead_id', $id)
    //                                             ->orderBy('created_at', 'desc')
    //                                             ->get()
    //                                             ->map(function ($changelog) {
    //                                                 $changelog['source'] = 'lead_changelog';
    //                                                 return $changelog;
    //                                             });
    //     return response()->json($existingLeadChangelogs);
    // }

}