<?php

/**
 * Created by Reliese Model.
 */

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

/**
 * Class QuotesLine
 * 
 * @property int $line_id
 * @property int|null $quote_id
 * @property string|null $text
 * 
 * @property Quote|null $quote
 *
 * @package App\Models
 */
class QuotesLine extends Model
{
	protected $table = 'quotes_lines';
	protected $primaryKey = 'line_id';
	public $timestamps = false;

	protected $casts = [
		'quote_id' => 'int'
	];

	protected $fillable = [
		'quote_id',
		'text'
	];

	public function quote()
	{
		return $this->hasOne(Quote::class, 'quote_id');
	}
}
