<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Collaborator extends Model
{
    protected $table='collaborators';

    protected $fillable = [
        'username','password','email','role'
    ];

    public function order(){
        return $this->hasMany('App\Order');
    }
}
