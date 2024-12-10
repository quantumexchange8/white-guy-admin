<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class PrivateMessage extends Model
{
    use HasFactory;

    protected $table = "core_privatemessage";

    public $timestamps = false;

    protected $fillable = [
        'id',
        'body',
        'attachment',
        'is_read',
        'edited_at',
        'created_at',
        'recipient_id',
        'sender_id',
    ];
    
    /**
     * Get the receiver of the private message.
     */
    public function receiver(): BelongsTo
    {
        return $this->belongsTo(User::class, 'recipient_id');
    }
    
    /**
     * Get the sender of the private message.
    */
    public function sender(): BelongsTo
    {
        return $this->belongsTo(User::class, 'sender_id');
    }

}
