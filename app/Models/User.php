<?php

namespace App\Models;

use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Illuminate\Database\Eloquent\Collection;

class User extends Authenticatable
{
    use Notifiable;

    protected $table = 'users';
    public $timestamps = false;

    protected $casts = [
        'admin' => 'boolean',
    ];

    protected $hidden = [
        'password'
    ];

    protected $fillable = [
        'first_name',
        'last_name',
        'phone',
        'email',
        'adresse',
        'ca_max',
        'charges',
        'password'
    ];

    public function customers()
    {
        return $this->hasMany(Customer::class);
    }
}
