<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Shipping extends Model
{
    protected $table = 'shippings';

    protected $fillable = [
        'name', 'price', 'description'
    ];

    public function order()
    {
        return $this->hasMany('App\Order');
    }
}
