<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class LeadAppointmentLabel extends Model
{
    use HasFactory;

    protected $table = "core_leadappointmentlabel";

    protected $fillable = [
        'title',
        'description',
        'edited_at',
        'created_at',
    ];

    /**
     * Get the duplicated leads for the lead appointment label.
     */
    public function duplicatedLeads(): HasMany
    {
        return $this->hasMany(LeadDuplicated::class, 'appointment_label_id');
    }
}
