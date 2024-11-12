<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class OrderChangelog extends Model
{
    use HasFactory, SoftDeletes;

    protected $table = "orders_changelog";

    protected $casts = [
        'changes' => 'json',
    ];

    protected $fillable = [
        'orders_id',
        'column_name',
        'description',
    ];
    
    /**
     * Get the changelog that belongs.
     */
    public function order(): BelongsTo
    {
        return $this->belongsTo(Order::class, 'orders_id');
    }
}
