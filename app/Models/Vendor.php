<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Vendor extends Model
{
    protected $table = 'vendors';
    protected $fillable = ['user_id', 'business_name', 'commission_rate', 'status', 'verified_at'];

    public function products(): HasMany { return $this->hasMany(Product::class); }
    public function orders(): HasMany { return $this->hasMany(Order::class); }
}
