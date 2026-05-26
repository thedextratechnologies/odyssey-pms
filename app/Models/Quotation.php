<?php
namespace App\Models;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Builder;

class Quotation extends Model {
    use SoftDeletes;
    protected $fillable = ['quote_number','lead_id','customer_id','created_by','product_id','version','configuration','subtotal','gst_rate','gst_amount','discount_amount','total','status','valid_until','notes','rejection_reason'];
    protected $casts = ['configuration'=>'array','valid_until'=>'date','subtotal'=>'decimal:2','total'=>'decimal:2','gst_amount'=>'decimal:2','discount_amount'=>'decimal:2'];

    const STATUS_COLORS = [
        'draft'=>'gray','pending_bdm'=>'yellow','pending_zm'=>'blue','pending_sd'=>'purple',
        'approved'=>'green','rejected'=>'red','revision_requested'=>'orange','expired'=>'gray','won'=>'green','lost'=>'red'
    ];
    const STATUS_LABELS = [
        'draft'=>'Draft','pending_bdm'=>'Pending BDM','pending_zm'=>'Pending ZM',
        'pending_sd'=>'Pending SD','approved'=>'Approved','rejected'=>'Rejected',
        'revision_requested'=>'Revision Needed','expired'=>'Expired','won'=>'Won','lost'=>'Lost'
    ];

    public function lead(): BelongsTo { return $this->belongsTo(Lead::class); }
    public function customer(): BelongsTo { return $this->belongsTo(Customer::class); }
    public function createdBy(): BelongsTo { return $this->belongsTo(User::class, 'created_by'); }
    public function product(): BelongsTo { return $this->belongsTo(Product::class); }
    public function items(): HasMany { return $this->hasMany(QuotationItem::class); }
    public function approvals(): HasMany { return $this->hasMany(Approval::class); }

    public function getStatusLabelAttribute(): string { return self::STATUS_LABELS[$this->status] ?? ucfirst($this->status); }
    public function getStatusColorAttribute(): string { return self::STATUS_COLORS[$this->status] ?? 'gray'; }

    public static function generateNumber(): string {
        $year = date('Y');
        $last = static::whereYear('created_at', $year)->count() + 1;
        return 'OE-' . $year . '-' . str_pad($last, 4, '0', STR_PAD_LEFT);
    }

    public function canBeApprovedBy(User $user): bool {
        return match($this->status) {
            'pending_bdm' => $user->isBDM() || $user->isZoneManager() || $user->isSalesDirector() || $user->isSuperAdmin(),
            'pending_zm'  => $user->isZoneManager() || $user->isSalesDirector() || $user->isSuperAdmin(),
            'pending_sd'  => $user->isSalesDirector() || $user->isSuperAdmin(),
            default       => false,
        };
    }

    // Fix: $query auto-injected by Laravel
    public function scopeVisibleTo(Builder $query, User $user): Builder {
        if ($user->isSuperAdmin() || $user->isSalesDirector()) return $query;
        if ($user->isZoneManager()) return $query->whereHas('createdBy', fn($q)=>$q->where('state_id',$user->state_id));
        if ($user->isBDM()) return $query->whereHas('createdBy', fn($q)=>$q->where('district_id',$user->district_id));
        return $query->where('created_by', $user->id);
    }
}
