<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Order extends Model
{
    protected $table = 'Order';

    protected $fillable = [
        'collaboratorId','clientId','ShippingId','crationDate','deliveryDate','totlaPrice','status'
    ];

    public function collaborator(){
        return $this->belongsTo('App\Collaborator','colaboratorId');
    }

    public function client(){
        return $this->belongsTo('App\Client','clientId');
    }

    public function shipping(){
        return $this->belongsTo('App\Shipping','ShippingId');
    }
}
