<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class LeadContactOutcome extends Model
{
    use HasFactory;

    protected $table = "core_leadcontactoutcome";

    protected $fillable = [
        'title',
        'description',
        'edited_at',
        'created_at',
    ];

    /**
     * Get the leads for the lead contact outcome.
     */
    public function leads(): HasMany
    {
        return $this->hasMany(Lead::class, 'contact_outcome_id');
    }

    /**
     * Get the duplicated leads for the lead contact outcome.
     */
    public function duplicatedLeads(): HasMany
    {
        return $this->hasMany(LeadDuplicated::class, 'contact_outcome_id');
    }
}
