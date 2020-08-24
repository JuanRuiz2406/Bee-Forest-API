<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Refund extends Model
{
    protected $table = 'refunds';

    protected $fillable = [
        'refundDate','orderId'
    ];

    public function order(){
        return $this->belongsTo('App\Order','refundDate');
    }
}
