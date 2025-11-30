<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

/**
 * نموذج الدفع - يمثل عمليات الدفع
 * Payment Model - Represents payment transactions
 *
 * @property int $id
 * @property int $order_id
 * @property decimal $amount
 * @property string $method
 * @property string $status
 * @property string $reference_id
 * @property string $currency
 * @property \Illuminate\Support\Carbon $created_at
 * @property \Illuminate\Support\Carbon $updated_at
 */
class Payment extends Model
{
    protected $table = 'payments';

    protected $fillable = [
        'order_id',
        'amount',
        'method',
        'status',
        'reference_id',
        'currency',
    ];

    /**
     * احصل على الطلب المرتبط بهذا الدفع
     * Get the order associated with this payment
     *
     * @return BelongsTo
     */
    public function order(): BelongsTo
    {
        return $this->belongsTo(Order::class);
    }
}
