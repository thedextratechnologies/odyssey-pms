<?php
namespace App\Models;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Product extends Model {
    protected $fillable = ['family','variant','description','capacity_persons','door_type','base_price','is_active'];
    protected $casts = ['base_price'=>'decimal:2','is_active'=>'boolean'];
    public function addons(): HasMany { return $this->hasMany(ProductAddon::class); }
    public function getFamilyLabelAttribute(): string { return ucfirst($this->family); }
    public function getFamilyColorAttribute(): string {
        return match($this->family) { 'orbit'=>'blue', 'apex'=>'purple', 'nova'=>'yellow', default=>'gray' };
    }
}
