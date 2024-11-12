<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class LeadNote extends Model
{
    use HasFactory;

    protected $table = "core_leadnote";

    public $timestamps = false;

    protected $fillable = [
        'note', 
        'attachment',
        'edited_at',
        'created_at',
        'created_by_id',
        'lead_id',
        'color',
        'user_editable', 
    ];
    
    /**
     * User Model
     * Get the user that created the lead note.
     */
    public function leadNoteCreator(): BelongsTo
    {
        return $this->belongsTo(User::class, 'created_by_id');
    }
    
    /**
     * Lead model
     * Get the lead that owns the lead note.
     */
    public function lead(): BelongsTo
    {
        return $this->belongsTo(Lead::class, 'lead_id');
    }
    
}
