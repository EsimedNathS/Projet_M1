<?php

/**
 * Created by Reliese Model.
 */

namespace App\Models;

use Carbon\Carbon;
use Illuminate\Database\Eloquent\Model;

/**
 * Class Quote
 * 
 * @property int $quote_id
 * @property int|null $project_id
 * @property int|null $expense_id
 * @property int|null $status_id
 * @property Carbon|null $date
 * 
 * @property Expense|null $expense
 * @property QuotesLine $quotes_line
 * @property QuotesStatus|null $quotes_status
 * @property Project|null $project
 *
 * @package App\Models
 */
class Quote extends Model
{
	protected $table = 'quotes';
	protected $primaryKey = 'quote_id';
	public $timestamps = false;

	protected $casts = [
		'project_id' => 'int',
		'expense_id' => 'int',
		'status_id' => 'int',
		'date' => 'datetime'
	];

	protected $fillable = [
		'project_id',
		'expense_id',
		'status_id',
		'date'
	];

	public function expense()
	{
		return $this->belongsTo(Expense::class);
	}

	public function quotes_line()
	{
		return $this->belongsTo(QuotesLine::class, 'quote_id');
	}

	public function quotes_status()
	{
		return $this->belongsTo(QuotesStatus::class, 'status_id');
	}

	public function project()
	{
		return $this->hasOne(Project::class, 'project_id');
	}
}
