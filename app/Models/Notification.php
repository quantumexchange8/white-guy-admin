<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Notification extends Model
{
    use HasFactory;

    protected $table = "core_notification";

    public $timestamps = false;

    protected $fillable = [
        'title',
        'description',
        'notification_type',
        'attachment',
        'is_read',
        'edited_at',
        'created_at',
        'user_id',
    ];

    /**
     * User Model
     * Get the user that the notification is for.
     */
    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class, 'user_id');
    }
}
