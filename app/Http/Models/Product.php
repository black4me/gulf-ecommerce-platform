<?php

namespace App\Models\Http\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

/**
 * Product Model
 * Represents a product item
 * @property int $id
 * @property int $vendor_id
 * @property string $name_ar
 * @property string $name_en
 * @property string $description_ar
 * @property string $description_en
 * @property decimal $price
 * @property int $stock_quantity
 * @property string $sku
 * @property string|null $image_url
 * @property array $categories
 * @property bool $is_active
 * @property bool $is_featured
 * @property timestamp $created_at
 * @property timestamp $updated_at
 * @property timestamp|null $deleted_at
 */
class Product extends Model
{
    use HasFactory, SoftDeletes;

    protected $fillable = [
        'vendor_id',
        'name_ar',
        'name_en',
        'description_ar',
        'description_en',
        'price',
        'stock_quantity',
        'sku',
        'image_url',
        'categories',
        'is_active',
        'is_featured',
    ];

    protected $casts = [
        'price' => 'decimal:2',
        'categories' => 'array',
        'is_active' => 'boolean',
        'is_featured' => 'boolean',
    ];

    /**
     * Get the vendor that owns the product.
     */
    public function vendor()
    {
        return $this->belongsTo(Vendor::class);
    }

    /**
     * Get the order items for this product.
     */
    public function orderItems()
    {
        return $this->hasMany(OrderItem::class);
    }

    /**
     * Get the reviews for this product.
     */
    public function reviews()
    {
        return $this->hasMany(Review::class);
    }
}
