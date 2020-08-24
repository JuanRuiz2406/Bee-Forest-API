<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class DispatchTicket extends Model
{
    protected $table='dispotch_tickets';

    protected $fillable = [
        'orderId','totalPrice','dispathDate','status','discount'
    ];

    public function Order(){
        return $this->belongsTo('App\Order','orderId');
    }
}
