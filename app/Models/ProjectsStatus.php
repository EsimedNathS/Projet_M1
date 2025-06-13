<?php

/**
 * Created by Reliese Model.
 */

namespace App\Models;

use Illuminate\Database\Eloquent\Collection;
use Illuminate\Database\Eloquent\Model;

/**
 * Class ProjectsStatus
 * 
 * @property int $status_id
 * @property string|null $name
 * 
 * @property Collection|Project[] $projects
 *
 * @package App\Models
 */
class ProjectsStatus extends Model
{
	protected $table = 'projects_status';
	protected $primaryKey = 'status_id';
	public $timestamps = false;

	protected $fillable = [
		'name'
	];

	public function projects()
	{
		return $this->hasMany(Project::class, 'status_id');
	}
}
