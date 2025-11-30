<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

/**
 * User Model
 *
 * نموذج المستخدم
 * يمثل جدول المستخدمين في النظام
 *
 * @package App\Models
 */
class User extends Model
{
    protected $table = 'users';
    protected $fillable = [
        'name',
        'email',
        'phone',
        'password',
        'address',
        'city',
        'country_code',
        'role',
        'status',
        'email_verified_at',
        'language'
    ];

    protected $casts = [
        'email_verified_at' => 'datetime',
        'created_at' => 'datetime',
        'updated_at' => 'datetime',
    ];

    /**
     * Get user orders
     */
    public function orders(): HasMany
    {
        return $this->hasMany(Order::class);
    }

    /**
     * Get user reviews
     */
    public function reviews(): HasMany
    {
        return $this->hasMany(Review::class);
    }
}
