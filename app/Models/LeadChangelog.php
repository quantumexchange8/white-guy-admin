<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class LeadChangelog extends Model
{
    use HasFactory, SoftDeletes;

    protected $table = "leads_changelog";

    protected $casts = [
        'lead_changes' => 'json',
        'lead_front_changes' => 'json',
        'lead_notes_changes' => 'json',
    ];

    protected $fillable = [
        'lead_id',
        'column_name',
        'description',
    ];
    
    /**
     * Get the post that owns the comment.
     */
    public function lead(): BelongsTo
    {
        return $this->belongsTo(Lead::class, 'id');
    }
}
