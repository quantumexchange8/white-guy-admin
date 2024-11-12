<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasOne;

class AuditlogLogentry extends Model
{
    use HasFactory;

    protected $table = "auditlog_logentry";

    public $timestamps = false;

    protected $fillable = [
        'object_pk', 
        'object_id',
        'object_repr',
        'action',
        'changes',
        'timestamp',
        'actor_id',
        'content_type_id', 
        'remove_addr',
        'additional_data',
        'serialized_data',
    ];
    
    /**
     * User Model
     * Get the user that created the log entry.
     */
    public function actor(): BelongsTo
    {
        return $this->belongsTo(User::class, 'actor_id');
    }
    
    /**
     * ContentType Model
     * Get the content type of the log entry.
     */
    public function contentType(): BelongsTo
    {
        return $this->belongsTo(AuditlogLogentry::class, 'content_type_id');
    }
}
