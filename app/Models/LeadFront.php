<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class LeadFront extends Model
{
    use HasFactory;

    protected $table = "core_leadfront";
    
    public $timestamps = false;

    protected $fillable = [
        'name', 
        'mimo', 
        'product', 
        'quantity', 
        'price', 
        'vc', 
        'sdm', 
        'liquid', 
        'bank', 
        'note', 
        'commission', 
        'edited_at', 
        'created_at', 
        'lead_id',
        'email',
        'phone_number',
    ];
    
    /**
     * Lead model
     * Get the lead of the lead front.
     */
    public function lead(): BelongsTo
    {
        return $this->belongsTo(Lead::class, 'lead_id');
    }
}
