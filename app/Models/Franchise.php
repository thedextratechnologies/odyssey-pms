<?php
namespace App\Models;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Franchise extends Model {
    use SoftDeletes;
    protected $fillable = ['company_name','owner_name','phone','email','state_id','district_id','managed_by','agreement_date','status','notes'];
    protected $casts = ['agreement_date' => 'date'];
    public function state(): BelongsTo { return $this->belongsTo(Territory::class, 'state_id'); }
    public function district(): BelongsTo { return $this->belongsTo(Territory::class, 'district_id'); }
    public function manager(): BelongsTo { return $this->belongsTo(User::class, 'managed_by'); }
    public function customers(): HasMany { return $this->hasMany(Customer::class); }
}
