<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class DuplicatedLead extends Model
{
    use HasFactory, SoftDeletes;

    protected $table = "duplicated_leads";

    protected $fillable = [
        'first_name', 
        'last_name', 
        'country', 
        'address',
        'date_oppd_in', 
        'campaign_product',
        'sdm',
        'date_of_birth',
        'occupation',
        'agents_book',
        'account_manager',
        'vc', 
        'data_type',
        'data_source',
        'data_code',
        'email', 
        'email_alt_1',
        'email_alt_2',
        'email_alt_3',
        'phone_number',
        'phone_number_alt_1',
        'phone_number_alt_2',
        'phone_number_alt_3',
        'private_remark',
        'remark',
        'appointment_start_at',
        'appointment_end_at',
        'last_called', 
        'assignee_read_at',
        'give_up_at', 
        'appointment_label',
        'contact_outcome',
        'stage',
        'assignee', 
        'created_by',
        'delete_at',
    ];
}
