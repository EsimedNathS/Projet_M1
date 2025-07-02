<?php

/**
 * Created by Reliese Model.
 */

namespace App\Models;

use Illuminate\Database\Eloquent\Collection;
use Illuminate\Database\Eloquent\Model;

/**
 * Class QuotesStatus
 * 
 * @property int $status_id
 * @property string|null $name
 * 
 * @property Collection|Quote[] $quotes
 *
 * @package App\Models
 */
class QuotesStatus extends Model
{
	protected $table = 'quotes_status';
	public $timestamps = false;

	protected $fillable = [
		'name'
	];

	public function quotes()
	{
		return $this->hasMany(Quotes::class, 'status_id');
	}
}
