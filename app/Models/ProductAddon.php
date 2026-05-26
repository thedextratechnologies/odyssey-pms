<?php
namespace App\Models;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class ProductAddon extends Model {
    protected $fillable = ['product_id','name','category','price','unit','is_active'];
    protected $casts = ['price'=>'decimal:2','is_active'=>'boolean'];
    public function product(): BelongsTo { return $this->belongsTo(Product::class); }
}
