<?php

/**
 * Created by Reliese Model.
 */

namespace App\Models;

use Illuminate\Database\Eloquent\Collection;
use Illuminate\Database\Eloquent\Model;

/**
 * Class ExpensesLine
 * 
 * @property int $line_id
 * @property int|null $expense_id
 * 
 * @property Collection|Expense[] $expenses
 *
 * @package App\Models
 */
class ExpensesLine extends Model
{
	protected $table = 'expenses_lines';
	protected $primaryKey = 'line_id';
	public $timestamps = false;

	protected $casts = [
		'expense_id' => 'int'
	];

	protected $fillable = [
		'expense_id'
	];

	public function expenses()
	{
		return $this->hasMany(Expense::class, 'expense_line_id');
	}
}
