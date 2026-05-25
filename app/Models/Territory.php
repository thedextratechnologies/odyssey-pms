<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Territory extends Model
{
    protected $fillable = ['type', 'name', 'parent_id', 'is_active'];

    const TYPE_STATE    = 'state';
    const TYPE_DISTRICT = 'district';
    const TYPE_CITY     = 'city';

    public function parent(): BelongsTo
    {
        return $this->belongsTo(Territory::class, 'parent_id');
    }

    public function children(): HasMany
    {
        return $this->hasMany(Territory::class, 'parent_id');
    }

    // Get all states
    public static function states()
    {
        return static::where('type', self::TYPE_STATE)->where('is_active', true)->orderBy('name')->get();
    }

    // Get districts for a state
    public static function districtsFor(int $stateId)
    {
        return static::where('type', self::TYPE_DISTRICT)
            ->where('parent_id', $stateId)
            ->where('is_active', true)
            ->orderBy('name')
            ->get();
    }

    // Get cities for a district
    public static function citiesFor(int $districtId)
    {
        return static::where('type', self::TYPE_CITY)
            ->where('parent_id', $districtId)
            ->where('is_active', true)
            ->orderBy('name')
            ->get();
    }

    public function getFullPathAttribute(): string
    {
        if ($this->type === self::TYPE_CITY) {
            $district = $this->parent;
            $state    = $district?->parent;
            return implode(' › ', array_filter([$state?->name, $district?->name, $this->name]));
        }
        if ($this->type === self::TYPE_DISTRICT) {
            return ($this->parent?->name ? $this->parent->name . ' › ' : '') . $this->name;
        }
        return $this->name;
    }
}
