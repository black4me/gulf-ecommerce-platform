<?php namespace App\Models; use Illuminate\Database\Eloquent\Model; use Illuminate\Database\Eloquent\Relations\HasMany;
class Product extends Model {
    protected $table = 'products';
    protected $fillable = ['vendor_id', 'name', 'description', 'price', 'stock', 'category_id', 'status', 'currency'];
    public function vendor() { return $this->belongsTo(Vendor::class); }
    public function reviews(): HasMany { return $this->hasMany(Review::class); }
}
