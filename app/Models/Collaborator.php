<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Collaborator extends Model
{
    protected $table='collaborators';

    protected $fillable = [
        'username','email','role'
    ];

    protected $hidden = [
        'password'
    ];

    public function order(){
        return $this->hasMany('App\Order');
    }
}
