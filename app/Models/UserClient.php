<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class UserClient extends Model
{
    use HasFactory, SoftDeletes;

    protected $table = "users_clients";

    protected $fillable = [
        'site',
        'username',
        'password',
        'account_id',
        'first_name',
        'last_name',
        'full_legal_name',
        'email',
        'phone_number',
        'address',
        'country_of_citizenship',
        'account_holder',
        'account_type',
        'customer_type',
        'account_manager',
        'lead_status',
        'client_stage',
        'rank',
        'remark',
        'previous_broker_name',
        'kyc_status',
        'is_active',
        'has_crm_access',
        'has_leads_access',
        'is_staff',
        'is_superuser',
        'last_login',
    ];
    
}
