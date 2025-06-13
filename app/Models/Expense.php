<?php

/**
 * Created by Reliese Model.
 */

namespace App\Models;

use Carbon\Carbon;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Database\Eloquent\Model;

/**
 * Class Expense
 * 
 * @property int $expense_id
 * @property int|null $status_id
 * @property int|null $expense_line_id
 * @property string|null $product_name
 * @property Carbon|null $date_edition
 * @property Carbon|null $date_payment
 * @property string|null $type_payment
 * @property Carbon|null $date_payement_effect
 * @property string|null $note
 * @property string|null $facturation
 * 
 * @property ExpensesStatus|null $expenses_status
 * @property ExpensesLine|null $expenses_line
 * @property Collection|Quote[] $quotes
 *
 * @package App\Models
 */
class Expense extends Model
{
	protected $table = 'expenses';
	protected $primaryKey = 'expense_id';
	public $timestamps = false;

	protected $casts = [
		'status_id' => 'int',
		'expense_line_id' => 'int',
		'date_edition' => 'datetime',
		'date_payment' => 'datetime',
		'date_payement_effect' => 'datetime'
	];

	protected $fillable = [
		'status_id',
		'expense_line_id',
		'product_name',
		'date_edition',
		'date_payment',
		'type_payment',
		'date_payement_effect',
		'note',
		'facturation'
	];

	public function expenses_status()
	{
		return $this->belongsTo(ExpensesStatus::class, 'status_id');
	}

	public function expenses_line()
	{
		return $this->belongsTo(ExpensesLine::class, 'expense_line_id');
	}

	public function quotes()
	{
		return $this->hasMany(Quote::class);
	}
}
