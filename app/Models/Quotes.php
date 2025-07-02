<?php

/**
 * Created by Reliese Model.
 */

namespace App\Models;

use Carbon\Carbon;
use Illuminate\Database\Eloquent\Model;

/**
 * Class Quotes
 * 
 * @property int $id
 * @property int|null $project_id
 * @property int|null $expense_id
 * @property int|null $status_id
 * @property Carbon $date_edition
 * @property Carbon|null $date_payment
 * @property string|null $type_payment
 * @property Carbon $created_at
 * @property Carbon|null $updated_at
 * 
 * @property Expense|null $expense
 * @property QuotesLine $quotes_line
 * @property QuotesStatus|null $quotes_status
 * @property Project|null $project
 *
 * @package App\Models
 */
class Quotes extends Model
{
	protected $table = 'quotes';
	public $timestamps = true; // On active les timestamps si tu ajoutes 'updated_at'

	protected $casts = [
		'project_id' => 'int',
		'expense_id' => 'int',
		'status_id' => 'int',
		'date_edition' => 'datetime',
		'date_payment' => 'datetime',
		'created_at' => 'datetime',
		'updated_at' => 'datetime',
	];

	protected $fillable = [
		'project_id',
		'expense_id',
		'status_id',
		'date_edition',
		'date_payment',
		'type_payment',
		'description'
	];
	
	public function calculateAmount()
	{
		return $this->lines->sum(function($line) {
			return $line->unit_price * $line->quantity;
		});
	}

	public function expenses()
	{
		return $this->hasMany(Expenses::class, 'quote_id');
	}

	public function quotes_line()
	{
		return $this->belongsTo(QuotesLines::class, 'quote_id');
	}

	public function status()
	{
		return $this->belongsTo(QuotesStatus::class, 'status_id');
	}

	public function project()
	{
		return $this->belongsTo(Project::class);
	}

	public function lines()
	{
		return $this->hasMany(QuotesLines::class, 'quote_id');
	}
}
