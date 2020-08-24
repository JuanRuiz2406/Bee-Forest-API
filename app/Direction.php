<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Direction extends Model
{
    protected $table='directions';

    protected $fillable = [
        'clientId','coutry','province','city','zipCode','direction'
    ];

    public function clients(){
        return $this->belongsTo('App\client','clientId');
    }
}
