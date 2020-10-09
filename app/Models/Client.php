<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Client extends Model
{

    protected $table='clients';

    protected $fillable = [
        'identificationCard','name','surname','telephone','email'
    ];

    public function order(){
        return $this->hasMany('App\Order');
    }

    public function direction(){
        return $this->hasMany('App\Direction');
    }
}
