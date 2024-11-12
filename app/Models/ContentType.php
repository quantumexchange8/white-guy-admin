<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class ContentType extends Model
{
    use HasFactory;

    protected $table = "django_content_type";

    public $timestamps = false;

    protected $fillable = [
        'app_label', 
        'model',
    ];

    /**
     * AuditlogLogentry model
     * Get the audit log entries for the content type.
     */
    public function auditLogEntries(): HasMany
    {
        return $this->hasMany(AuditlogLogentry::class, 'content_type_id');
    }
}
