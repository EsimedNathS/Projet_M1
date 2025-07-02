<?php

/**
 * Created by Reliese Model.
 */

namespace App\Models;

use Illuminate\Database\Eloquent\Collection;
use Illuminate\Database\Eloquent\Model;

/**
 * Class ExpensesStatus
 * 
 * @property int $status_id
 * @property string|null $name
 * 
 * @property Collection|Expense[] $expenses
 *
 * @package App\Models
 */
class ExpensesStatus extends Model
{
	protected $table = 'expenses_status';
	public $timestamps = false;

	protected $fillable = [
		'name'
	];

	public function expenses()
	{
		return $this->hasMany(Expense::class, 'status_id');
	}
}
