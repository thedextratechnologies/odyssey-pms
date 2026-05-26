<?php
namespace App\Models;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Customer extends Model {
    use SoftDeletes;
    protected $fillable = ['name','phone','email','address','city_id','district_id','state_id','pin_code','property_type','num_floors','budget_range','source','franchise_id','assigned_to','notes'];
    public function city(): BelongsTo { return $this->belongsTo(Territory::class, 'city_id'); }
    public function district(): BelongsTo { return $this->belongsTo(Territory::class, 'district_id'); }
    public function state(): BelongsTo { return $this->belongsTo(Territory::class, 'state_id'); }
    public function franchise(): BelongsTo { return $this->belongsTo(Franchise::class); }
    public function assignedTo(): BelongsTo { return $this->belongsTo(User::class, 'assigned_to'); }
    public function leads(): HasMany { return $this->hasMany(Lead::class); }
    public function latestLead() { return $this->leads()->latest()->first(); }
    public function getPropertyTypeLabelAttribute(): string {
        return ucfirst($this->property_type);
    }
}
