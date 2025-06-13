<?php

/**
 * Created by Reliese Model.
 */

namespace App\Models;

use Carbon\Carbon;
use Illuminate\Database\Eloquent\Model;

/**
 * Class Customer
 * 
 * @property int $customer_id
 * @property int|null $user_id
 * @property int|null $project_id
 * @property string|null $name
 * @property string|null $denomination
 * @property Carbon|null $required_date
 * 
 * @property User|null $user
 * @property Project|null $project
 *
 * @package App\Models
 */
class Customer extends Model
{
	protected $table = 'customers';
	protected $primaryKey = 'customer_id';
	public $timestamps = false;

	protected $casts = [
		'user_id' => 'int',
		'project_id' => 'int',
		'required_date' => 'datetime'
	];

	protected $fillable = [
		'user_id',
		'project_id',
		'name',
		'denomination',
		'required_date'
	];

	public function user()
	{
		return $this->belongsTo(User::class);
	}

	public function projects()
	{
		return $this->hasMany(Project::class, 'project_id');
	}
}
