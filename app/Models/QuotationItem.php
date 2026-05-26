<?php
namespace App\Models;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class QuotationItem extends Model {
    protected $fillable = ['quotation_id','description','category','quantity','unit_price','amount'];
    protected $casts = ['unit_price'=>'decimal:2','amount'=>'decimal:2'];
    public function quotation(): BelongsTo { return $this->belongsTo(Quotation::class); }
}
