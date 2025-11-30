<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

/**
 * OrderItem Model - نموذج عنصر الطلب
 * 
 * يمثل عنصر مفرد في الطلب
 * Represents a single item in an order
 */
class OrderItem extends Model
{
    /**
     * The table associated with the model
     * @var string
     */
    protected $table = 'order_items';

    /**
     * The attributes that are mass assignable
     * @var array
     */
    protected $fillable = [
        'order_id',
        'product_id',
        'vendor_id',
        'quantity',
        'price',
        'subtotal'
    ];

    /**
     * The attributes that should be cast
     * @var array
     */
    protected $casts = [
        'quantity' => 'integer',
        'price' => 'decimal:2',
        'subtotal' => 'decimal:2'
    ];

    /**
     * Get the order that owns this item - الطلب الذي يملكه هذا العنصر
     */
    public function order(): BelongsTo
    {
        return $this->belongsTo(Order::class);
    }

    /**
     * Get the product for this item - المنتج هذا العنصر
     */
    public function product(): BelongsTo
    {
        return $this->belongsTo(Product::class);
    }

    /**
     * Get the vendor for this item - البائع لهذا العنصر
     */
    public function vendor(): BelongsTo
    {
        return $this->belongsTo(Vendor::class);
    }
}
