<?php

/**
 * Created by Reliese Model.
 */

namespace App\Models;

use Carbon\Carbon;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Database\Eloquent\Model;

/**
 * Class Expenses
 * 
 * @property int $expense_id
 * @property int $status_id
 * @property int $quote_id
 * @property string $expense_number
 * @property string $html_file_path
 * @property string|null $product_name
 * @property Carbon|null $date_edition
 * @property string|null $type_payment
 * @property Carbon $date_payment_limit
 * @property Carbon|null $date_payment_effect
 * @property string|null $note
 * 
 * @property ExpensesStatus $expenses_status
 * @property ExpensesLine|null $expenses_line
 * @property Collection|Quote[] $quotes
 *
 * @package App\Models
 */

class Expenses extends Model
{
    protected $table = 'expenses';
    public $timestamps = false;
    
    protected $casts = [
        'status_id' => 'int',
        'quote_id' => 'int',
        'date_edition' => 'datetime',
        'date_payment_limit' => 'datetime',
        'date_payment_effect' => 'datetime'
    ];
    
    protected $fillable = [
        'status_id',
        'quote_id',
        'expense_number',
        'html_file_path',
        'product_name',
        'date_edition',
        'type_payment',
        'date_payment_limit',
        'date_payment_effect',
        'note'
    ];

    public function calculateAmount()
	{
		return $this->lines->sum(function($line) {
			return $line->unit_price * $line->quantity;
		});
	}

    // Relations
    public function expenses_status()
    {
        return $this->belongsTo(ExpensesStatus::class, 'status_id');
    }

    public function lines()
    {
        return $this->hasMany(ExpensesLine::class, 'expense_id');
    }

    public function quote()
    {
        return $this->belongsTo(Quotes::class, 'quote_id');
    }

    // Scopes pour filtrer les dépenses
    public function scopePaid($query)
    {
        return $query->whereNotNull('date_payment_effect');
    }

    public function scopeUnpaid($query)
    {
        return $query->whereNull('date_payment_effect');
    }

    public function scopeByPaymentType($query, $type)
    {
        return $query->where('type_payment', $type);
    }
}
