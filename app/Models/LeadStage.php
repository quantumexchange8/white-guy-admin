<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class LeadStage extends Model
{
    use HasFactory;

    protected $table = "core_leadstage";

    protected $fillable = [
        'title',
        'description',
        'order',
        'edited_at',
        'created_at',
        'required_form',
    ];

    /**
     * Get the leads for the lead stage.
     */
    public function leads(): HasMany
    {
        return $this->hasMany(Lead::class, 'stage_id');
    }

    /**
     * Get the duplicated leads for the lead stage.
     */
    public function duplicatedLeads(): HasMany
    {
        return $this->hasMany(LeadDuplicated::class, 'stage_id');
    }
}
