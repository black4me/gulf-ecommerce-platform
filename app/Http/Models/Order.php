<?php

namespace App\Models\Http\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

/**
 * Order Model
 * Represents a customer order
 * @property int $id
 * @property int $user_id
 * @property string $order_number
 * @property decimal $total_amount
 * @property decimal $tax_amount
 * @property decimal $shipping_cost
 * @property string $status
 * @property string $currency
 * @property string|null $payment_method
 * @property string|null $tracking_number
 * @property array $shipping_address
 * @property array|null $notes
 * @property timestamp $created_at
 * @property timestamp $updated_at
 */
class Order extends Model
{
    use HasFactory;

    protected $fillable = [
        'user_id',
        'order_number',
        'total_amount',
        'tax_amount',
        'shipping_cost',
        'status',
        'currency',
        'payment_method',
        'tracking_number',
        'shipping_address',
        'notes',
    ];

    protected $casts = [
        'total_amount' => 'decimal:2',
        'tax_amount' => 'decimal:2',
        'shipping_cost' => 'decimal:2',
        'shipping_address' => 'array',
        'notes' => 'array',
    ];

    /**
     * Get the user that created the order.
     */
    public function user()
    {
        return $this->belongsTo(User::class);
    }

    /**
     * Get the order items.
     */
    public function items()
    {
        return $this->hasMany(OrderItem::class);
    }

    /**
     * Get the payment associated with the order.
     */
    public function payment()
    {
        return $this->hasOne(Payment::class);
    }

    /**
     * Get the shipment associated with the order.
     */
    public function shipment()
    {
        return $this->hasOne(Shipment::class);
    }
}
