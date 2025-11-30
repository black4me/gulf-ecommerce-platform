<?php

namespace App\Models\Http\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

/**
 * Vendor Model
 * Represents a vendor/seller profile
 * @property int $id
 * @property int $user_id
 * @property string $business_name
 * @property string $business_type
 * @property string $business_registration
 * @property string|null $tax_id
 * @property string|null $logo
 * @property string|null $description
 * @property string|null $bank_account
 * @property decimal $commission_rate
 * @property bool $is_active
 * @property bool $is_verified
 * @property timestamp $created_at
 * @property timestamp $updated_at
 * @property timestamp|null $deleted_at
 */
class Vendor extends Model
{
    use HasFactory, SoftDeletes;

    protected $fillable = [
        'user_id',
        'business_name',
        'business_type',
        'business_registration',
        'tax_id',
        'logo',
        'description',
        'bank_account',
        'commission_rate',
        'is_active',
        'is_verified',
    ];

    protected $casts = [
        'commission_rate' => 'decimal:2',
        'is_active' => 'boolean',
        'is_verified' => 'boolean',
    ];

    /**
     * Get the user that owns the vendor profile.
     */
    public function user()
    {
        return $this->belongsTo(User::class);
    }

    /**
     * Get the products associated with the vendor.
     */
    public function products()
    {
        return $this->hasMany(Product::class);
    }

    /**
     * Get the orders associated with the vendor's products.
     */
    public function orderItems()
    {
        return $this->hasMany(OrderItem::class);
    }
}
