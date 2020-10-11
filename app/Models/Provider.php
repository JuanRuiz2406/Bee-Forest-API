<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Provider extends Model{
    
    protected $fillable = [
       'id','name','surname','telephone','direction','email','startDay','finalDay','StartTime','finalTime'
    ];

    public function materials(){
        return $this->hasMany('App\Material');
    }

}
