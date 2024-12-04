<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class OrderNote extends Model
{
    use HasFactory;

    protected $table = "core_ordernote";

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
    public function orderNoteCreator(): BelongsTo
    {
        return $this->belongsTo(User::class, 'created_by_id');
    }
    
    /**
     * Order model
     * Get the order that owns the order note.
     */
    public function order(): BelongsTo
    {
        return $this->belongsTo(Order::class, 'order_id');
    }
    
}
