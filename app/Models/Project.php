<?php

/**
 * Created by Reliese Model.
 */

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

/**
 * Class Project
 * 
 * @property int $id
 * @property int $client_id
 * @property int|null $status_id
 * @property string|null $name
 * @property float|null $list_price
 * @property float|null $discount
 * 
 * @property Customer $customer
 * @property Quote $quote
 * @property ProjectsStatus|null $projects_status
 *
 * @package App\Models
 */
class Project extends Model
{
	protected $table = 'projects';
	public $timestamps = false;

	protected $casts = [
		'customer_id' => 'int',
		'status_id' => 'int',
		'list_price' => 'float',
		'discount' => 'float'
	];

	protected $fillable = [
		'customer_id',
		'status_id',
		'name',
		'list_price',
		'discount'
	];

	public function customer()
	{
		return $this->belongsTo(Customer::class);
	}

	public function status()
	{
		return $this->belongsTo(ProjectsStatus::class);
	}

	public function quotes()
	{
		return $this->hasMany(Quotes::class);
	}

	public function getTotalQuotesAttribute()
	{
		return $this->quotes()->sum('amount');
	}
}
