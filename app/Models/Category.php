<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

/**
 * فئة الفئة - نموذج لتمثيل فئات المنتجات
 * Category Model - Represents product categories
 *
 * @property int $id
 * @property string $name
 * @property string $description
 * @property string $slug
 * @property bool $status
 * @property \Illuminate\Support\Carbon $created_at
 * @property \Illuminate\Support\Carbon $updated_at
 */
class Category extends Model
{
    protected $table = 'categories';

    protected $fillable = [
        'name',
        'description',
        'slug',
        'status',
    ];

    /**
     * احصل على المنتجات في هذه الفئة
     * Get the products for this category
     *
     * @return HasMany
     */
    public function products(): HasMany
    {
        return $this->hasMany(Product::class);
    }
}
