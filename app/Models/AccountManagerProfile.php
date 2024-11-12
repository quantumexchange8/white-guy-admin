<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class AccountManagerProfile extends Model
{
    use HasFactory;

    protected $table = "core_accountmanagerprofile";
    
    public $timestamps = false;

    protected $fillable = [
        'file', 
        'edited_at', 
        'created_at', 
        'user_id', 
    ];
    
    /**
     * User model
     * Get the user that owns the account manager profile.
     */
    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class, 'user_id');
    }
}
