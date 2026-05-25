<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Role extends Model
{
    protected $fillable = ['name', 'display_name', 'description', 'level'];

    // Role name constants
    const SUPER_ADMIN     = 'super_admin';
    const SALES_DIRECTOR  = 'sales_director';
    const ZONE_MANAGER    = 'zone_manager';
    const BDM             = 'bdm';
    const BDE             = 'bde';

    // Level constants (higher = more authority)
    const LEVEL_BDE             = 1;
    const LEVEL_BDM             = 2;
    const LEVEL_ZONE_MANAGER    = 3;
    const LEVEL_SALES_DIRECTOR  = 4;
    const LEVEL_SUPER_ADMIN     = 5;

    public function users(): HasMany
    {
        return $this->hasMany(User::class);
    }

    public function isSuperAdmin(): bool   { return $this->name === self::SUPER_ADMIN; }
    public function isSalesDirector(): bool { return $this->name === self::SALES_DIRECTOR; }
    public function isZoneManager(): bool   { return $this->name === self::ZONE_MANAGER; }
    public function isBDM(): bool           { return $this->name === self::BDM; }
    public function isBDE(): bool           { return $this->name === self::BDE; }
}
