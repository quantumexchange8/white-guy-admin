<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class PaymentSubmission extends Model
{
    use HasFactory;

    protected $table = "core_paymentsubmission";

    public $timestamps = false;

    protected $fillable = [
        'date', 
        'amount',
        'converted_amount',
        'user_memo',
        'admin_memo',
        'admin_remark',
        'status',
        'approved_at',
        'edited_at',
        'created_at',
        'payment_method_id',
        'user_id', 
    ];
    
    /**
     * Get the user of the submitted payment.
     */
    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class, 'user_id');
    }
    
    /**
     * Get the payment method of the payment submission.
     */
    public function paymentMethod(): BelongsTo
    {
        return $this->belongsTo(PaymentMethod::class, 'payment_method_id');
    }
}
