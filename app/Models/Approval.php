<?php
namespace App\Models;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Approval extends Model {
    protected $fillable = ['quotation_id','approver_id','role_level','status','comment','actioned_at'];
    protected $casts = ['actioned_at'=>'datetime'];
    public function quotation(): BelongsTo { return $this->belongsTo(Quotation::class); }
    public function approver(): BelongsTo { return $this->belongsTo(User::class, 'approver_id'); }
}
