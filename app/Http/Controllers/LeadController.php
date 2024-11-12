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

    public function create()
    {
        return Inertia::render('CRM/Leads/Create');
    }

    public function store(LeadRequest $request)
    {
        $data = $request->all();
        
        // Additional validation based on user selection (Lead Front | Lead Notes)
        if ($data['create_lead_front']) {
            $leadFrontRequest = new LeadFrontRequest();
            $request->validate($leadFrontRequest->rules());
        }

        if (count($data['lead_notes']) > 0) {
            $leadNoteRequest = new LeadNotesRequest();
            $request->validate($leadNoteRequest->rules());
        }
        
        // Lead Notes changes
        // $leadChanges = [];
        // $leadFrontChanges = [];
        // $leadNotesChanges = [];
    
        // Insert into table
		$newLeadData = Lead::create([
            'date' => $data['date'],
            'first_name' => $data['first_name'],
            'last_name' => $data['last_name'],
            'country' => $data['country'],
            'date_of_birth' => $data['date_of_birth'],
            'occupation' => $data['occupation'],
            'vc' => $data['vc'],
            'sdm' => $data['sdm'],
            'email' => $data['email'],
            'email_alt_1' => '',
            'email_alt_2' => '',
            'email_alt_3' => '',
            'phone_number' => $data['phone_number'],
            'phone_number_alt_1' => '',
            'phone_number_alt_2' => '',
            'phone_number_alt_3' => '',
            'attachment' => '',
            'private_remark' => $data['private_remark'],
            'remark' => $data['remark'],
            'data_source' => $data['data_source'],
            'appointment_start_at' => $data['appointment_start_at'],
            'appointment_end_at' => $data['appointment_end_at'],
            'contacted_at' => $data['contacted_at'],
            'assignee_read_at' => $data['assignee_read_at'],
            'edited_at' => $data['edited_at'],
            'created_at' => $data['created_at'],
            'appointment_label_id' => $data['appointment_label_id'],
            'assignee_id' => $data['assignee_id'],
            'contact_outcome_id' => $data['contact_outcome_id'],
            'created_by_id' => $data['created_by_id'],
            'stage_id' => $data['stage_id'],
            'give_up_at' => $data['give_up_at'],
            'account_manager' => $data['account_manager'],
            'address' => $data['address'],
            'agents_book' => $data['agents_book'],
            'campaign_product' => $data['campaign_product'],
            'data_code' => $data['data_code'],
            'data_type' => $data['data_type'],
            'deleted_at' => $data['deleted_at'],
            'deleted_note' => '',
            'sort_id' => $this->generateLeadSortUuid(),
        ]);
        $newLeadData->save();

        // Add the change to the lead notes changes array
        // $leadChanges['New'] = [
        //     'id' => $newLeadData->id,
        //     'description' => 'A new lead has been created',
        // ];

        if ($data['create_lead_front']) {
            $total = $request->lead_front_quantity * $request->lead_front_price;

            // dd($data);
            // Insert into lead_front table
            $newLeadFrontData = LeadFront::create([
                'name' => $data['lead_front_name'],
                'mimo' => $data['lead_front_mimo'],
                'product' => $data['lead_front_product'],
                'quantity' => $data['lead_front_quantity'],
                'price' => $data['lead_front_price'],
                'vc' => $data['lead_front_vc'],
                'sdm' => $data['lead_front_sdm'],
                'liquid' => $data['lead_front_liquid'],
                'bank' => $data['lead_front_bank'],
                'note' => $data['lead_front_note'],
                'commission' => $data['lead_front_commission'],
                'edited_at' => $data['lead_front_edited_at'],
                'created_at' => $data['lead_front_created_at'],
                'lead_id' => $newLeadData->id,
                'email' => $data['lead_front_email'],
                'phone_number' => $data['lead_front_phone_number'],
        ]);
            $newLeadFrontData->save();

            // Add the change to the lead notes changes array
            // $leadFrontChanges['New'] = [
            //     'id' => $newLeadFrontData->id,
            //     'description' => 'A new lead front has been created',
            // ];
        }

        if (count($request->lead_notes) > 0) {
            // Loop over array to insert into table
            foreach ($request->lead_notes as $key => $value) {
                // Insert into lead_notes table
                $newLeadNoteData = LeadNote::create([
                    'note' => $value['note'],
                    // 'attachment' => '',
                    // 'edited_at' => $data['lead_front_edited_at'],
                    // 'created_at' => $data['lead_front_created_at'],
                    'created_by_id' => $value['created_by_id'],
                    'lead_id' => $newLeadData->id,
                    // 'color' => 'info',
                    'user_editable' => $value['user_editable'],
                ]);

                // Add the change to the lead notes changes array
                // $leadNotesChanges[$newLeadNoteData->id]['New'] = [
                //     'id' => $newLeadNoteData->id,
                //     'description' => 'A new lead note has been created',
                // ];
            }
            $newLeadNoteData->save();
        }

        // if (count($leadChanges) > 0 || count($leadFrontChanges) > 0 || count($leadNotesChanges) > 0) {
        //     $newLeadChangelog = new LeadChangelog;

        //     $newLeadChangelog->lead_id = $newLeadData->id;
        //     $newLeadChangelog->column_name = 'leads';
        //     $newLeadChangelog->lead_changes = $leadChanges;
        //     $newLeadChangelog->lead_front_changes = $leadFrontChanges;
        //     // The lead note's id is placed as the key at the top level
        //     $newLeadChangelog->lead_notes_changes = $leadNotesChanges;
        //     $newLeadChangelog->description = 'The lead has been successfully created';

        //     $newLeadChangelog->save();
        // }
        
        $errorMsgTitle = "You have successfully created a new lead.";
        $errorMsgType = "success";

        $errorMsg = [
            'title' => $errorMsgTitle,
            'type' => $errorMsgType,
        ];

        return Redirect::route('leads.index')
                        ->with('errorMsg', $errorMsg);
    }

    public function generateLeadSortUuid()
    {
        do {
            $newLeadUuid = Str::uuid();
        } while ($this->checkExistingLeadSortUuid($newLeadUuid));
        
        return (string) $newLeadUuid;
    }

    public function checkExistingLeadSortUuid($string)
    {
        $existingLeadUuid = Lead::where('sort_id', $string)->get();

        if (count($existingLeadUuid) > 0) {
            return true;
        }
        
        return false;
    }

    /**
     * Display the specified resource.
     */
    public function show(string $id)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(string $id)
    {
        $data = Lead::with(['leadfront', 'leadnotes'])->find($id);
        // $data->phone_number = strval($data->phone_number);

        $existingLeadFront = $data->leadfront;
        $existingLeadNotes = $data->leadnotes;
        
        // $existingLeadFront = DB::table('lead_front')
        //                         ->join('leads', 'lead_front.linked_lead', '=', 'leads.id')
        //                         ->where('lead_front.linked_lead', '=', $id)
        //                         ->where('lead_front.deleted_at', '=', null)
        //                         ->select('lead_front.*')
        //                         ->first();
        
        // $existingLeadNotes = DB::table('lead_notes')
        //                         ->join('leads', 'lead_notes.linked_lead', '=', 'leads.id')
        //                         ->where('lead_notes.linked_lead', '=', $id)
        //                         ->where('lead_notes.deleted_at', '=', null)
        //                         ->select('lead_notes.*')
        //                         ->get();

        // foreach($existingLeadNotes as $key=>$value) {
        //     $existingLeadNotes[$key]->user_editable = boolval($existingLeadNotes[$key]->user_editable);
        // }
        
        // dd($existingLeadNotes);

		return Inertia::render('CRM/Leads/Edit', [
            'data' => $data, 
            'leadFrontData' => $existingLeadFront,
            'leadNotesData' => $existingLeadNotes
        ]);
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(LeadRequest $request, string $id)
    {
        $data = $request->all();
        // dd($data);
        
        // Additional validation based on user selection (Lead Front | Lead Notes)
        if ($data['create_lead_front']) {
            $leadFrontRequest = new LeadFrontRequest();
            $this->validate($request, $leadFrontRequest->rules(), $leadFrontRequest->messages(), $leadFrontRequest->attributes());
        }
        
        if (count($data['lead_notes']) > 0) {
            $leadNoteRequest = new LeadNotesRequest();
            $this->validate($request, $leadNoteRequest->rules(), $leadNoteRequest->messages(), $leadNoteRequest->attributes());
        }
        
        // Lead changes
        // $leadChanges = [];
        // $oldLeadData = Lead::find($id);

        // Loop through existing data across lead, lead front, and lead notes and compare against the request to detect any changes
        // Only log found changes into lead changelog table
        // if (isset($oldLeadData)) {
        //     foreach ($oldLeadData->toArray() as $key => $oldValue) {
        //         if ($key === 'created_at' || $key === 'updated_at' || $key === 'deleted_at') {
        //             continue;
        //         }
                
        //         $newValue = $data[$key] ?? null;

        //         // Check if the value has changed
        //         if ($newValue !== $oldValue) {
        //             $leadChanges[$key] = [
        //                 'old' => $oldValue,
        //                 'new' => $newValue,
        //             ];
        //         }
        //     }
        // }

        // Lead Front changes
        // $leadFrontChanges = [];
        // $oldLeadFrontData = LeadFront::where('linked_lead', $id)->get();

        // Retrieve all column names from the database table
        // $leadFrontColumns = Schema::getColumnListing('lead_front');

        // // Mapping between request column names and database column names
        // $leadFrontColumnMappings = [
        //     'lead_front_name' => 'name',
        //     'lead_front_mimo' => 'mimo',
        //     'lead_front_product' => 'product',
        //     'lead_front_quantity' => 'quantity',
        //     'lead_front_price' => 'price',
        //     'lead_front_sdm' => 'sdm',
        //     'lead_front_liquid' => 'liquid',
        //     'lead_front_bank_name' => 'bank_name',
        //     'lead_front_bank_account' => 'bank_account',
        //     'lead_front_note' => 'note',
        //     'lead_front_commission' => 'commission',
        //     'lead_front_vc' => 'vc',
        //     'lead_front_edited_at' => 'edited_at',
        // ];

        // if (isset($oldLeadFrontData) && count($oldLeadFrontData) > 0) {
        //     foreach ($leadFrontColumns as $columnName) {
        //         // Skip 'created_at' and 'updated_at' columns
        //         if ($columnName === 'id' || $columnName === 'created_at' || $columnName === 'updated_at' || $columnName === 'deleted_at') {
        //             continue;
        //         }

        //         $requestColumnName = array_search($columnName, $leadFrontColumnMappings);
    
                
        //         // Get the old value from the database
        //         $oldValue = $oldLeadFrontData[0]->$columnName;
    
        //         // Get the new value from the request using the mapped column name
        //         switch($columnName) {
        //             case('quantity'):
        //             case('price'):
        //             case('commission'):
        //                 $oldValue = number_format($oldValue, 2);
        //                 $newValue = number_format($data[$requestColumnName], 2) ?? null;
        //                 break;
        //             case('sdm'):
        //             case('liquid'):
        //                 $oldValue = boolval($oldValue);
        //                 $newValue = $data[$requestColumnName] ?? null;
        //                 break;
        //             case('assignee'):
        //                 $newValue = $data['assignee'];
        //                 break;
        //             case('total'):
        //                 $oldValue = number_format($oldValue, 2);
        //                 $newValue = number_format($data['lead_front_quantity'] * $data['lead_front_price'], 2) ?? null;
        //                 break;
        //             case('linked_lead'):
        //                 $newValue = $id;
        //                 break;
        //             default:
        //                 $newValue = $data[$requestColumnName] ?? null;
        //         }
    
        //         // Check if the value has changed
        //         if ($newValue !== $oldValue) {
        //             $leadFrontChanges[$columnName] = [
        //                 'id' => $data['lead_front_id'],
        //                 'old' => $oldValue,
        //                 'new' => $newValue,
        //             ];
        //         }
        //     }
        // }

        // Lead Notes changes
        // $leadNotesChanges = [];
        // $oldLeadNotesData = LeadNote::where('linked_lead', $id)->get();

        // Check if lead notes data exists in the request
        // if (isset($data['lead_notes']) && is_array($data['lead_notes'])) {
        //     // Retrieve old lead notes data from the database
        //     $oldLeadNotesData = LeadNote::where('linked_lead', $id)->get();

        //     // Iterate over each old lead note
        //     foreach ($oldLeadNotesData as $oldLeadNote) {
        //         // Get the ID of the lead note
        //         $leadNoteId = $oldLeadNote->id;

        //         // Find the corresponding lead note data in the request
        //         $newLeadNoteData = collect($data['lead_notes'])->firstWhere('id', $leadNoteId);

        //         // If lead note data exists in the request
        //         if ($newLeadNoteData) {
        //             // Iterate over each attribute of the lead note
        //             foreach ($newLeadNoteData as $key => $newValue) {
        //                 // Skip 'id', 'linked_lead', 'created_at', 'updated_at', and 'deleted_at' columns
        //                 if (in_array($key, ['id', 'linked_lead', 'created_at'])) {
        //                     continue;
        //                 }

        //                 // Retrieve the corresponding old value from the database
        //                 $oldValue = ($key === 'user_editable') ? boolval( $oldLeadNote->{$key}) : $oldLeadNote->{$key};
        //                 // $oldValue = $oldLeadNote->{$key};

        //                 // Check if the value has changed
        //                 if ($newValue !== $oldValue) {
        //                     // Add the change to the lead notes changes array
        //                     $leadNotesChanges[$leadNoteId][$key] = [
        //                         'id' => $oldLeadNote->id,
        //                         'old' => $oldValue,
        //                         'new' => $newValue,
        //                     ];
        //                 }
        //             }
        //         }
        //     }
        // }
        
        // $updateActionCount = 0;
        
        $existingLead = Lead::find($id);
        $systemNotesArray = [];
        
        if (isset($existingLead)) {
            foreach ($existingLead->toArray() as $key => $oldValue) {
                if ($key === 'appointment_start_end' || $key === 'created_at') {
                    continue;
                }
                
                $newValue = $data[$key] ?? null;
                
                // Check if the value has changed and if so, set the respective note message along with the new value
                if ($newValue !== $oldValue) {
                    switch($key) {
                        case('stage_id'):
                            $leadStage = LeadStage::select('title')->find($newValue);
                            $note = "Moved to stage '" . $leadStage->title . "'";
                            break;
                        case('appointment_start_at'):
                            $appointmentLabel = LeadAppointmentLabel::select('title')->find($data['appointment_label_id']);
                            $note = "Appointment scheduled for " . $newValue . " - " . $data['appointment_end_at'] . " with label '" . (isset($appointmentLabel) ? $appointmentLabel->title : "None") . "'";
                            break;
                        case('contact_outcome_id'):
                            $leadContactOutcome = LeadContactOutcome::select('title')->find($newValue);
                            $note = "Marked as called with the outcome '" . $leadContactOutcome->title . "'";
                            break;
                        case('give_up_at'):
                            $note = "Gave up at " . $newValue;
                            break;
                        default:
                            $note = false;
                    }

                    if ($note){
                        array_push($systemNotesArray, $note);
                    }
                }
            }
            
            // dd($systemNotesArray);

            // Only if there are any lead changes made
            if (isset($systemNotesArray) && count($systemNotesArray) > 0) {
                if (count($systemNotesArray) > 1) {
                    // If user made more than one action then it will be combined into a different note format
                    $note = "Multiple actions taken.\r\n";
                    foreach ($systemNotesArray as $key => $value) {
                        $note .= $value . ". \r\n";
                    }
                } else {
                    $note = $systemNotesArray[0];
                }
    
                $newLeadNote = LeadNote::create([
                    'note' => '[SYSTEM] ' . $note,
                    'attachment' => '',
                    'edited_at' => preg_replace('/(\d{2})(\d{2})$/', '$1', $data['edited_at']),
                    'created_at' => preg_replace('/(\d{2})(\d{2})$/', '$1', $data['edited_at']),
                    'created_by_id' => auth()->user()->id,
                    'lead_id' => $id,
                    'color' => 'info',
                    'user_editable' => false,
                ]);
                $newLeadNote->save();
            }
        }
                
        $existingLead->update([
            'date' => $data['date'] ? preg_replace('/(\d{2})(\d{2})$/', '$1', $data['date']) : $data['date'],
            'first_name' => $data['first_name'],
            'last_name' => $data['last_name'],
            'country' => $data['country'],
            'date_of_birth' => $data['date_of_birth'],
            'occupation' => $data['occupation'],
            'vc' => $data['vc'],
            'sdm' => $data['sdm'],
            'email' => $data['email'],
            'email_alt_1' => '',
            'email_alt_2' => '',
            'email_alt_3' => '',
            'phone_number' => $data['phone_number'],
            'phone_number_alt_1' => '',
            'phone_number_alt_2' => '',
            'phone_number_alt_3' => '',
            'attachment' => '',
            'private_remark' => $data['private_remark'],
            'remark' => $data['remark'],
            'data_source' => $data['data_source'],
            'appointment_start_at' => isset($data['appointment_start_at']) ? preg_replace('/(\d{2})(\d{2})$/', '$1', $data['appointment_start_at']) : $data['appointment_start_at'],
            'appointment_end_at' => $data['appointment_end_at'] ? preg_replace('/(\d{2})(\d{2})$/', '$1', $data['appointment_end_at']) : $data['appointment_end_at'],
            'contacted_at' => $data['contacted_at'] ? preg_replace('/(\d{2})(\d{2})$/', '$1', $data['contacted_at']) : $data['contacted_at'],
            'assignee_read_at' => $data['assignee_read_at'] ? preg_replace('/(\d{2})(\d{2})$/', '$1', $data['assignee_read_at']) : $data['assignee_read_at'],
            'edited_at' => preg_replace('/(\d{2})(\d{2})$/', '$1', $data['edited_at']),
            'created_at' => preg_replace('/(\d{2})(\d{2})$/', '$1', $data['created_at']),
            'appointment_label_id' => $data['appointment_label_id'],
            'assignee_id' => $data['assignee_id'],
            'contact_outcome_id' => $data['contact_outcome_id'],
            'created_by_id' => $data['created_by_id'],
            'stage_id' => $data['stage_id'],
            'give_up_at' => $data['give_up_at'] ? preg_replace('/(\d{2})(\d{2})$/', '$1', $data['give_up_at']) : $data['give_up_at'],
            'account_manager' => $data['account_manager'],
            'address' => $data['address'],
            'agents_book' => $data['agents_book'],
            'campaign_product' => $data['campaign_product'],
            'data_code' => $data['data_code'],
            'data_type' => $data['data_type'],
            'deleted_at' => $data['deleted_at'] ? preg_replace('/(\d{2})(\d{2})$/', '$1', $data['deleted_at']) : $data['deleted_at'],
            'deleted_note' => '',
            'sort_id' => $data['sort_id'],
		]);
        // $updateActionCount++;

        // Update existing lead front or insert a new lead front
        if ($data['create_lead_front']) {
            if (isset($data['lead_front_id']) && $data['lead_front_id'] !== ''){
                $existingLeadFront = LeadFront::find($data['lead_front_id']);

                if (isset($existingLeadFront)) {
                    $existingLeadFront->update([
                        'name' => $data['lead_front_name'],
                        'mimo' => $data['lead_front_mimo'],
                        'product' => $data['lead_front_product'],
                        'quantity' => $data['lead_front_quantity'],
                        'price' => $data['lead_front_price'],
                        'vc' => $data['lead_front_vc'],
                        'sdm' => $data['lead_front_sdm'],
                        'liquid' => $data['lead_front_liquid'],
                        'bank' => $data['lead_front_bank'],
                        'note' => $data['lead_front_note'],
                        'commission' => $data['lead_front_commission'],
                        'edited_at' => preg_replace('/(\d{2})(\d{2})$/', '$1', $data['lead_front_edited_at']),
                        'created_at' => preg_replace('/(\d{2})(\d{2})$/', '$1', $data['lead_front_created_at']),
                        'lead_id' => $id,
                        'email' => $data['email'],
                        'phone_number' => $data['phone_number'],
                    ]);
                    // $updateActionCount++;
                }

            } else {
                // Insert into lead_front table
                $newLeadFront = LeadFront::create([
                    'name' => $data['lead_front_name'],
                    'mimo' => $data['lead_front_mimo'],
                    'product' => $data['lead_front_product'],
                    'quantity' => $data['lead_front_quantity'],
                    'price' => $data['lead_front_price'],
                    'vc' => $data['lead_front_vc'],
                    'sdm' => $data['lead_front_sdm'],
                    'liquid' => $data['lead_front_liquid'],
                    'bank' => $data['lead_front_bank'],
                    'note' => $data['lead_front_note'],
                    'commission' => $data['lead_front_commission'],
                    'edited_at' => preg_replace('/(\d{2})(\d{2})$/', '$1', $data['lead_front_edited_at']),
                    'created_at' => preg_replace('/(\d{2})(\d{2})$/', '$1', $data['lead_front_created_at']),
                    'lead_id' => $id,
                    'email' => $data['email'],
                    'phone_number' => $data['phone_number'],
                ]);
                $newLeadFront->save();

                // Add the change to the lead notes changes array
                // $leadFrontChanges['New'] = [
                //     'id' => $newLeadFront->id,
                //     'description' => 'A new lead front has been created',
                // ];
            }
        }

        if (count($request->lead_notes) > 0) {
            foreach ($request->lead_notes as $key => $value) {
                if (isset($value['id'])) {
                    $existingLeadNotes = LeadNote::find($value['id']);
            
                    $existingLeadNotes->update([
                        'note' => $value['note'],
                        'attachment' => '',
                        'edited_at' => preg_replace('/(\d{2})(\d{2})$/', '$1', $value['edited_at']),
                        'created_at' => preg_replace('/(\d{2})(\d{2})$/', '$1', $value['edited_at']),
                        'created_by_id' => $value['created_by_id'],
                        'lead_id' => $id,
                        'color' => $value['color'],
                        'user_editable' => $value['user_editable'],
                    ]);

                } else {
                    // Insert into lead_notes table
                    $newLeadNote = LeadNote::create([
                        'note' => $value['note'],
                        'attachment' => '',
                        'edited_at' => preg_replace('/(\d{2})(\d{2})$/', '$1', $value['edited_at']),
                        'created_at' => preg_replace('/(\d{2})(\d{2})$/', '$1', $value['created_at']),
                        'created_by_id' => $value['created_by_id'],
                        'lead_id' => $id,
                        'color' => 'primary',
                        'user_editable' => $value['user_editable'],
                    ]);
                    $newLeadNote->save();

                    // Add the change to the lead notes changes array
                    // $leadNotesChanges[$newLeadNote->id]['New'] = [
                    //     'id' => $newLeadNote->id,
                    //     'description' => 'A new lead note has been created',
                    // ];
                }
                // $updateActionCount++;
            }
        }

        // if ($updateActionCount > 0) {
        //     if (count($leadChanges) > 0 || count($leadFrontChanges) > 0 || count($leadNotesChanges) > 0) {
        //         $newLeadChangelog = new LeadChangelog;

        //         $newLeadChangelog->lead_id = $id;
        //         $newLeadChangelog->column_name = 'leads';
        //         $newLeadChangelog->lead_changes = $leadChanges;
        //         $newLeadChangelog->lead_front_changes = $leadFrontChanges;
        //         // The lead note's id is placed as the key at the top level
        //         $newLeadChangelog->lead_notes_changes = $leadNotesChanges;
        //         $newLeadChangelog->description = 'The lead has been successfully updated';

        //         $newLeadChangelog->save();
        //     }
        // }
		
        $errorMsgTitle = "You have successfully updated the lead.";
        $errorMsgType = "success";

        $errorMsg = [
            'title' => $errorMsgTitle,
            'type' => $errorMsgType,
        ];

        return Redirect::route('leads.index')
                        ->with('errorMsg', $errorMsg);
    }

    /**
     * Delete this lead.
     */
    public function destroy(string $id)
    {
        $existingLead = Lead::find($id);
        $existingLead->delete();

        // $leadChanges = [];
        // // Add the change to the lead notes changes array
        // $leadChanges['Delete'] = [
        //     'id' => $id,
        //     'description' => 'The lead has been deleted',
        // ];

        // $newLeadChangelog = new LeadChangelog;

        // $newLeadChangelog->lead_id = $id;
        // $newLeadChangelog->column_name = 'leads';
        // $newLeadChangelog->lead_changes = $leadChanges;
        // $newLeadChangelog->lead_front_changes = [];
        // $newLeadChangelog->lead_notes_changes = [];
        // $newLeadChangelog->description = 'The lead has been deleted';

        // $newLeadChangelog->save();

        $errorMsgTitle = "You have successfully deleted the lead.";
        $errorMsgType = "success";

        $errorMsg = [
            'title' => $errorMsgTitle,
            'type' => $errorMsgType,
        ];

        return Redirect::route('leads.index')
                        ->with('errorMsg', $errorMsg);
    }

    /**
     * Delete lead front record that has this lead's id as its linked lead.
     */
    public function deleteLeadFront(string $id)
    {
        $existingLeadFront = LeadFront::find($id);
        $linkedLead = $existingLeadFront->lead_id;
        // dd($existingLeadFront);
        $existingLeadFront->delete();

        // $leadFrontChanges = [];
        // // Add the change to the lead notes changes array
        // $leadFrontChanges['Delete'] = [
        //     'id' => $id,
        //     'description' => 'The lead front has been deleted',
        // ];

        // $newLeadChangelog = new LeadChangelog;

        // $newLeadChangelog->lead_id = $linkedLead;
        // $newLeadChangelog->column_name = 'leads';
        // $newLeadChangelog->lead_changes = [];
        // $newLeadChangelog->lead_front_changes = $leadFrontChanges;
        // $newLeadChangelog->lead_notes_changes = [];
        // $newLeadChangelog->description = 'The lead front has been deleted';

        // $newLeadChangelog->save();

        return redirect(route('leads.edit', $linkedLead));
    }

    /**
     * Delete lead note record that has this lead's id as its linked lead.
     */
    public function deleteLeadNote(string $id)
    {
        $existingLeadNote = LeadNote::find($id);
        $linkedLead = $existingLeadNote->lead_id;
        $existingLeadNote->delete();

        // $leadNotesChanges = [];
        // // Add the change to the lead notes changes array
        // $leadNotesChanges[$id]['Delete'] = [
        //     'id' => $id,
        //     'description' => 'The lead note has been deleted',
        // ];

        // $newLeadChangelog = new LeadChangelog;

        // $newLeadChangelog->lead_id = $linkedLead;
        // $newLeadChangelog->column_name = 'leads';
        // $newLeadChangelog->lead_changes = [];
        // $newLeadChangelog->lead_front_changes = [];
        // $newLeadChangelog->lead_notes_changes = $leadNotesChanges;
        // $newLeadChangelog->description = 'The lead note has been deleted';

        // $newLeadChangelog->save();


        return redirect(route('leads.edit', $linkedLead));
    }

    /**
     * Delete lead duplicate.
     */
    public function destroyDuplicate(string $id)
    {
        $existingLeadDuplicate = LeadDuplicated::find($id);
        $existingLeadDuplicate->delete();

        $errorMsgTitle = "You have successfully deleted the lead duplicate.";
        $errorMsgType = "success";

        $errorMsg = [
            'title' => $errorMsgTitle,
            'type' => $errorMsgType,
        ];

        return Redirect::route('leads.index')
                        ->with('errorMsg', $errorMsg);
    }

    public function getLeads(Request $request)
    {
        // Define query with eager loading for related models
        $query = Lead::with([
            'leadCreator:id,username,site_id',
            'leadCreator.site:id,name',
            'assignee:id,full_name,site_id',
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
        ])
        ->latest(); // Order results
    
        // Handle pagination: PrimeVue uses 0-based pages, so add 1 for Laravel
        $rowsPerPage = $request->input('rows', 15);  // Default to 15 if 'rows' not provided
        $currentPage = $request->input('page', 0) + 1; // Make it 1-based for Laravel
    
        // Paginate the query
        $data = $query->simplePaginate($rowsPerPage, ['*'], 'page', $currentPage);
    
        // Cache the total row count for performance optimization
        $totalRows = Cache::remember('leads_total_count', 600, function () {
            return Lead::count();
        });
    
        // Prepare response data for PrimeVue DataTable
        return response()->json([
            'data' => $data->items(),
            'totalRecords' => $totalRows, // Matches PrimeVue's 'totalRecords' field
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
                                    
    public function applyFilterCondition($query, $filter, $filteredIdArr) {
        switch ($filter['condition']) {
            case 'not_contain':
                if ($filteredIdArr) {
                    $tempArr = [];
                    foreach ($filteredIdArr as $index => $value) {
                        array_push($tempArr, $value['id']);   
                    }
                    $query->whereNotIn('assignee_id', $tempArr);
                } else {
                    $query->where($filter['field'], 'NOT LIKE', '%' . $filter['value'] . '%');
                }
                break;
            case 'equal':
                if ($filteredIdArr) {
                    $tempArr = [];
                    foreach ($filteredIdArr as $index => $value) {
                        if ($filter['value'] === $value['value']) {
                            array_push($tempArr, $value['id']);     
                        }
                    }
                    $query->whereIn('assignee_id', $tempArr);
                } else {
                    if ($filter['field'] === 'lead_assignee') {
                        $query->where('assignee_id', 0);
                    } else {
                        $query->where($filter['field'], $filter['value']);
                    }
                }
                break;
            case 'not_equal':
                if ($filteredIdArr) {
                    $tempArr = [];
                    foreach ($filteredIdArr as $index => $value) {
                        if ($filter['value'] !== $value['value']) {
                            array_push($tempArr, $value['id']);    
                        }
                    }
                    $query->whereIn('assignee_id', $tempArr);
                } else {
                    if ($filter['field'] === 'lead_assignee') {
                        $query->whereNot('assignee_id', 0);
                    } else {
                        $query->whereNot($filter['field'], $filter['value']);
                    }
                }
                break;
            case 'start_with':
                if ($filteredIdArr) {
                    $tempArr = [];
                    foreach ($filteredIdArr as $index => $value) {
                        if (str_starts_with($value['value'], $filter['value'])) {
                            array_push($tempArr, $value['id']);    
                        }
                    }
                    $query->whereIn('assignee_id', $tempArr);
                } else {
                    $query->where($filter['field'], 'LIKE', $filter['value'] . '%');
                }
                break;
            case 'end_with':
                if ($filteredIdArr) {
                    $tempArr = [];
                    foreach ($filteredIdArr as $index => $value) {
                        if (str_ends_with($value['value'], $filter['value'])) {
                            array_push($tempArr, $value['id']);    
                        }
                    }
                    $query->whereIn('assignee_id', $tempArr);
                } else {
                    $query->where($filter['field'], 'LIKE', '%' . $filter['value']);
                }
                break;
            case 'greater_than':
                if ($filteredIdArr) {
                    $tempArr = [];
                    foreach ($filteredIdArr as $index => $value) {
                        if ($filter['value'] < $value['value']) {
                            array_push($tempArr, $value['id']);    
                        }
                    }
                    $query->whereIn('assignee_id', $tempArr);
                } else {
                    $query->where($filter['field'], '>', $filter['value']);
                }
                break;
            case 'greater_than_equal':
                if ($filteredIdArr) {
                    $tempArr = [];
                    foreach ($filteredIdArr as $index => $value) {
                        if ($filter['value'] <= $value['value']) {
                            array_push($tempArr, $value['id']);    
                        }
                    }
                    $query->whereIn('assignee_id', $tempArr);
                } else {
                    $query->where($filter['field'], '>=', $filter['value']);
                }
                break;
            case 'less_than':
                if ($filteredIdArr) {
                    $tempArr = [];
                    foreach ($filteredIdArr as $index => $value) {
                        if ($filter['value'] > $value['value']) {
                            array_push($tempArr, $value['id']);    
                        }
                    }
                    $query->whereIn('assignee_id', $tempArr);
                } else {
                    $query->where($filter['field'], '<', $filter['value']);
                }
                break;
            case 'less_than_equal':
                if ($filteredIdArr) {
                    $tempArr = [];
                    foreach ($filteredIdArr as $index => $value) {
                        if ($filter['value'] >= $value['value']) {
                            array_push($tempArr, $value['id']);    
                        }
                    }
                    $query->whereIn('assignee_id', $tempArr);
                } else {
                    $query->where($filter['field'], '<=', $filter['value']);
                }
                break;
            case 'contain':
                if ($filteredIdArr) {
                    $tempArr = [];
                    foreach ($filteredIdArr as $index => $value) {
                        array_push($tempArr, $value['id']);   
                    }
                    $query->whereIn('assignee_id', $tempArr);
                } else {
                    $query->where($filter['field'], 'LIKE', '%' . $filter['value'] . '%');
                }
                break;
            case 'is_null':
                $query->orWhereNull($filter['field']);
                break;
            case 'is_not_null':
                $query->orWhereNotNull($filter['field']);
                break;
        }
    }

    public function getDuplicatedLeads(Request $request)
    {
        if ($request['checkedFilters']) {
            $query = LeadDuplicated::query();

            foreach ($request['checkedFilters'] as $category => $options) {
                if (is_array($options) && count($options) > 0) {
                    $tempArray = [];
                    foreach ($options as $value) {
                        array_push($tempArray, (int)$value);
                    }
                    $query->whereIn($category, $tempArray);

                } elseif (is_string($options) && $options !== '') {
                    switch($options) {
                        case('Today'):
                            $query->whereBetween($category, [Carbon::today()->toDateTimeString(), Carbon::today()->addHours(24)->toDateTimeString()]);
                            break;
                        case('Past 7 days'):
                            $query->whereBetween($category, [Carbon::today()->subDays(7)->toDateTimeString(), Carbon::today()->toDateTimeString()]);
                            break;
                        case('This month'):
                            $query->whereMonth($category, Carbon::today()->month);
                            break;
                        case('This year'):
                            $query->whereYear($category, Carbon::today()->year);
                            break;
                        case('No date'):
                            $query->whereNull($category);
                            break;
                        case('Has date'):
                            $query->whereNotNull($category);
                            break;
                        default:
                            $query->whereBetween($category, [Carbon::today()->toDateTimeString(), Carbon::today()->addHours(24)->toDateTimeString()]);
                    }
                }
            }
            
            $tableColumns = Schema::getColumnListing('core_leadduplicated');

            // Global search
            $searchTerm = $request['params']['search'];
            if (!empty($searchTerm)) {
                $query->where(function ($innerQuery) use ($tableColumns, $searchTerm) {
                    foreach ($tableColumns as $column) {
                        $innerQuery->orWhere($column, 'LIKE', '%' . $searchTerm . '%');
                    }
                });
            }

            // Column-specific searches
            if (isset($request['params']['column_filters'])) {
                foreach ($request['params']['column_filters'] as $filter) {
                    if (isset($filter['value'])) {
                        if ($filter['field'] === 'lead_assignee') {
                            $leadAssigneeId = User::where('username', 'LIKE', '%' . $filter['value'] . '%')
                                                    ->select('id', 'username')
                                                    ->get();

                            $assigneeIdArr = [];
                            foreach ($leadAssigneeId as $key => $value) {
                                $assigneeIdArr[] = [
                                    'id' => $value->id,
                                    'value' => $value->username,
                                ];
                            }
                        }
                        $this->applyFilterCondition($query, $filter, isset($assigneeIdArr) ? $assigneeIdArr : []);
                        
                        if ($filter['condition'] === 'is_null') {
                            $query->orWhereNull($filter['field']);
                        } elseif ($filter['condition'] === 'is_not_null') {
                            $query->orWhereNotNull($filter['field']);
                        }
                    }
                }
            }
                                    
            $sort_column = '';
    
            if ($request['params']['sort_column'] === 'lead_assignee') {
                $sort_column = 'assignee_id';
            }
    
            if ($request['params']['sort_column'] === 'actions') {
                $sort_column = 'id';
            }

            $data = $query->with([
                                'leadCreator:id,username,site_id', 
                                'leadCreator.site:id,name', 
                                'assignee:id,username,site_id', 
                                'assignee.site:id,name', 
                                'contactOutcome:id,title',
                                'stage:id,title',
                                'appointmentLabel:id,title'
                            ])
                            ->orderBy((($sort_column !== '') ? $sort_column : $request['params']['sort_column']), $request['params']['sort_direction'])
                            ->paginate($request['params']['pagesize'], ['*'], 'page', $request['params']['page']);

            $records = [
                'data' => $data,
                'total_rows' => $data->total(),
            ];
            
            return response()->json($records);
        }
        
        $tableColumns = Schema::getColumnListing('core_leadduplicated');
        $sort_column = '';

        if ($request['params']['sort_column'] === 'lead_assignee') {
            $sort_column = 'assignee_id';
        }

        if ($request['params']['sort_column'] === 'actions') {
            $sort_column = 'id';
        }

        $queries = LeadDuplicated::query();
        $queries->with([
                                    'leadCreator:id,username,site_id', 
                                    'leadCreator.site:id,name', 
                                    'assignee:id,username,site_id', 
                                    'assignee.site:id,name', 
                                    'contactOutcome:id,title',
                                    'stage:id,title',
                                    'appointmentLabel:id,title'
        ]);
        $queries->where(function ($query) use ($tableColumns, $request) {
            // Global search
            $searchTerm = $request['params']['search'];
            if (!empty($searchTerm)) {
                $query->where(function ($innerQuery) use ($tableColumns, $searchTerm) {
                    foreach ($tableColumns as $column) {
                        $innerQuery->orWhere($column, 'LIKE', '%' . $searchTerm . '%');
                    }
                });
            }
            
            // Column-specific searches
            if (isset($request['params']['column_filters'])) {
                foreach ($request['params']['column_filters'] as $filter) {
                    if (isset($filter['value'])) {
                        if ($filter['field'] === 'lead_assignee') {
                            $leadAssigneeId = User::where('username', 'LIKE', '%' . $filter['value'] . '%')
                                                    ->select('id', 'username')
                                                    ->get();

                            $assigneeIdArr = [];
                            foreach ($leadAssigneeId as $key => $value) {
                                $assigneeIdArr[] = [
                                    'id' => $value->id,
                                    'value' => $value->username,
                                ];
                            }
                        }
                        $this->applyFilterCondition($query, $filter, isset($assigneeIdArr) ? $assigneeIdArr : []);
                        
                        if ($filter['condition'] === 'is_null') {
                            $query->orWhereNull($filter['field']);
                        } elseif ($filter['condition'] === 'is_not_null') {
                            $query->orWhereNotNull($filter['field']);
                        }
                    }
                }
            }
            // dd($query->toSQL());
        });
        $queries->orderBy((($sort_column !== '') ? $sort_column : $request['params']['sort_column']), $request['params']['sort_direction']);
        $data = $queries->paginate($request['params']['pagesize'], ['*'], 'page', $request['params']['page']);
        
        $records = [
            'data' => $data,
            'total_rows' => $data->total(),
        ];

        return response()->json($records);
    }

    public function getLatestLeads()
    {
        $data = Lead::with([
                            'leadCreator:id,username,site_id', 
                            'leadCreator.site:id,name', 
                            'assignee:id,username,site_id', 
                            'assignee.site:id,name', 
                            'leadnotes',
                            'leadnotes.leadNoteCreator:id,username,site_id',
                            'leadnotes.leadNoteCreator.site:id,name',
                            'contactOutcome:id,title',
                            'stage:id,title',
                            'appointmentLabel:id,title'
                        ])
                        ->latest()
                        ->take(5)
                        ->get();
        
        return response()->json($data);
    }

    public function getCategories(Request $request)
    {
        $contact_outcome_id = DB::table('core_leadcontactoutcome')
                                ->select('id', 'title')
                                ->orderBy('id')
                                ->get();

        $stage_id = DB::table('core_leadstage')
                        ->select('id', 'title')
                        ->orderBy('id')
                        ->get();

        $contacted_at = [ "Today", "Past 7 days", "This month", "This year", "No date", "Has date" ];

        $give_up_at = [ "Today", "Past 7 days", "This month", "This year", "No date", "Has date" ];

        $assignee_id = User::has('assignedLeads')
                            ->with('site:id,name')
                            ->select('id', 'username', 'site_id')
                            ->orderByDesc('id')
                            ->get()
                            ->map(function ($user) {
                                return [
                                    'id' => $user->id,
                                    'title' => $user->username . ' (' . $user->site->name . ')',
                                ];
                            });

        // dd($contact_outcome_id);

        $data = [
            'contact_outcome_id' => $contact_outcome_id,
            'stage_id' => $stage_id,
            'contacted_at' => $contacted_at,
            'give_up_at' => $give_up_at,
            'assignee_id' => $assignee_id,
        ];
        
        return response()->json($data);
    }

    public function getLeadFront(string $id)
    {
        return response()->json(Lead::find($id)->leadfront);
    }

    public function getLeadNotes(string $id)
    {
        // $existingLeadNotes = LeadNote::where('linked_lead', $id)
        //                                 ->orderBy('created_at', 'desc')
        //                                 ->get()
        //                                 ->map(function ($note) {
        //                                     $note['source'] = 'lead_note';
        //                                     return $note;
        //                                 });
        $existingLeadNotes = LeadNote::where('lead_id', $id)
                                        ->with(['leadNoteCreator:id,username,site_id', 'leadNoteCreator.site:id,name'])
                                        ->orderByDesc('id')
                                        ->get();

        foreach($existingLeadNotes as $key=>$value) {
            $existingLeadNotes[$key]->user_editable = boolval($existingLeadNotes[$key]->user_editable);
        }


        return response()->json($existingLeadNotes);
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

    public function getLeadLogEntries(string $id)
    {
        $contentTypeId = ContentType::with('auditLogEntries')
                                        ->where('app_label', 'core')
                                        ->where('model', 'lead')
                                        ->select('id')
                                        ->get();

        $leadLogEntries = [];

        foreach ($contentTypeId[0]->auditLogEntries as $key => $value) {
            if ((string)$value->object_id === $id){
                array_push($leadLogEntries, $value);
            }
        }

        return response()->json($leadLogEntries);
    }

    public function exportToExcel($selectedRowsData)
    {
        $leadsArr = explode(',', $selectedRowsData);
        foreach ($leadsArr as $key => $value) {
            $leadsArr[$key] = (int)$value;
        }

        $currentDate = Carbon::now()->format('Ymd_His');
        $exportTitle = 'leads_' . $currentDate . '.xlsx';
        
        return (new LeadsExport($leadsArr))->download($exportTitle);
    }

    public function importExcel(Request $request)
    {
        $file = $request->file('leadExcelFile');
        $import = new LeadsImport();
        $import->import($file);
        
        $errors = [];
        $duplicatedLeads = [];
        
        foreach ($import->failures() as $failure) {
            $rowErrors = [
                'row' => $failure->row(),
                'attribute' => $failure->attribute(),
                'errors' => $failure->errors(),
                'values' => $failure->values(),
            ];

            array_push($errors, $rowErrors);
        }

        // Set error messages to be displayed based on whether there are any errors
        if (count($errors) > 0) {
            $errorMsgTitle = "It seems there are some errors during the leads import.";
            $errorMsgContent = "There are rows that have not been inputted correctly or filled completely. Any duplicated leads have been moved to the duplicated leads table. The import errors are as below.";
            $errorMsgType = "error";
            $rowErrorMsg = true;
        } else {
            $errorMsgTitle = "You have successfully imported the leads into the system.";
            $errorMsgContent = "Any duplicated leads have been moved to the duplicated leads table. The import errors are as below.";
            $errorMsgType = "success";
            $rowErrorMsg = false;
        }

        // Excel::import(new LeadsImport, $file);
        // try {
        //     $import->import($file);
        // } catch (\Maatwebsite\Excel\Validators\ValidationException $e) {
        //     $failures = $e->failures();
            
        //     foreach ($failures as $failure) {
        //         $rowErrors = [
        //             'row' => $failure->row(),
        //             'attribute' => $failure->attribute(),
        //             'errors' => $failure->errors(),
        //             'values' => $failure->values(),
        //         ];

        //         array_push($errors, $rowErrors);
        //         if ($failure->attribute() === 'Email' || $failure->attribute() === 'Phone Number') {
        //             if ($failure->errors() === "The Email has already been taken." || $failure->errors() === "The Phone Number has already been taken.") {

        //            }
        //         }
        //     }
        // }

        // dd($errors);

        $errorMsg = [
            'title' => $errorMsgTitle,
            'content' => $errorMsgContent,
            'type' => $errorMsgType,
            'rowErrorMsg' => $rowErrorMsg,
        ];

        return Redirect::route('leads.index')
                        ->with('errors', $errors)
                        ->with('errorMsg', $errorMsg);
    }

    /**
     * Restore the lead if needed. Currently not put to use anywhere.
     */
    public function restore(string $id)
    {
        $softDeletedLead = Lead::withTrashed()->find($id);

        $softDeletedLead->restore();

        return redirect(route('leads.index'));
    }

    public function clearSessionMessages(Request $request)
    {
        // Clear the flashed messages from the session
        $request->session()->forget('errors');
        $request->session()->forget('errorMsg');
        
        return;
    }

    public function getTotalLeadCount()
    {
        return response()->json(Lead::count());
    }

    public function getAllLeadStages()
    {
        return response()->json(LeadStage::all());
    }

    public function getAllLeadContactOutcomes()
    {
        return response()->json(LeadContactOutcome::all());
    }

    public function getAllLeadAppointmentLabels()
    {
        return response()->json(LeadAppointmentLabel::all());
    }

    public function getAllLeadsForExport()
    {
        $data = Lead::with([
                            'leadCreator:id,username,site_id', 
                            'leadCreator.site:id,name', 
                            'assignee:id,username,site_id', 
                            'assignee.site:id,name', 
                            'leadnotes',
                            'leadnotes.leadNoteCreator:id,username,site_id',
                            'leadnotes.leadNoteCreator.site:id,name',
                            'contactOutcome:id,title',
                            'stage:id,title',
                            'appointmentLabel:id,title'
                        ])
                        ->get();

        return response()->json($data);
    }
}
