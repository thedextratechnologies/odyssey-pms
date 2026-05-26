<?php
namespace App\Models;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Builder;

class Lead extends Model {
    use SoftDeletes;
    protected $fillable = ['customer_id','assigned_to','stage','follow_up_at','site_visit_date','lost_reason','notes'];
    protected $casts = ['follow_up_at'=>'datetime','site_visit_date'=>'date'];

    const STAGES = [
        'new'                  => ['label'=>'New','color'=>'gray'],
        'contacted'            => ['label'=>'Contacted','color'=>'blue'],
        'site_visit_scheduled' => ['label'=>'Site Visit','color'=>'purple'],
        'quotation_sent'       => ['label'=>'Quote Sent','color'=>'yellow'],
        'negotiation'          => ['label'=>'Negotiation','color'=>'orange'],
        'won'                  => ['label'=>'Won','color'=>'green'],
        'lost'                 => ['label'=>'Lost','color'=>'red'],
        'on_hold'              => ['label'=>'On Hold','color'=>'gray'],
    ];

    public function customer(): BelongsTo { return $this->belongsTo(Customer::class); }
    public function assignedTo(): BelongsTo { return $this->belongsTo(User::class, 'assigned_to'); }
    public function quotations(): HasMany { return $this->hasMany(Quotation::class); }

    public function getStageLabelAttribute(): string { return self::STAGES[$this->stage]['label'] ?? ucfirst($this->stage); }
    public function getStageColorAttribute(): string { return self::STAGES[$this->stage]['color'] ?? 'gray'; }
    public function isOverdue(): bool { return $this->follow_up_at && $this->follow_up_at->isPast() && !in_array($this->stage,['won','lost']); }

    // Fix: $query is auto-injected by Laravel as first arg
    public function scopeVisibleTo(Builder $query, User $user): Builder {
        if ($user->isSuperAdmin() || $user->isSalesDirector()) return $query;
        if ($user->isZoneManager()) return $query->whereHas('assignedTo', fn($q)=>$q->where('state_id',$user->state_id));
        if ($user->isBDM()) return $query->whereHas('assignedTo', fn($q)=>$q->where('district_id',$user->district_id));
        return $query->where('assigned_to', $user->id);
    }
}
