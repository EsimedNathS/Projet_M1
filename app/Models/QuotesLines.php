<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class QuotesLines extends Model
{
    use HasFactory;

    protected $fillable = [
        'quote_id',
        'wording',
        'unit_price',
        'quantity'
    ];

    protected $casts = [
        'unit_price' => 'decimal:2',
        'quantity' => 'integer'
    ];

    /**
     * Relation avec Quote
     */
    public function quote()
    {
        return $this->belongsTo(Quotes::class);
    }

    /**
     * Accessor pour formater le prix unitaire
     */
    public function getFormattedUnitPriceAttribute()
    {
        return number_format($this->unit_price, 2) . ' €';
    }
}
