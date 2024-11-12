<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class SaleOrderItem extends Model
{
    use HasFactory;
    
    protected $table = "core_saleorderitem";

    public $timestamps = false;

    protected $fillable = [
        'public_id',
        'order_type',
        'product',
        'price',
        'exchanged_price',
        'quantity',
        'subtotal',
        'commission_rate',
        'commission',
        'total_exchanged_price',
        'total_price',
        'edited_at',
        'created_at',
        'sale_order_id',
        'completed_at',
        'order_id',
    ];

    /**
     * SaleOrder Model
     * Get the sale order that created the sale order item.
     */
    public function saleOrder(): BelongsTo
    {
        return $this->belongsTo(SaleOrder::class, 'sale_order_id');
    }
}
