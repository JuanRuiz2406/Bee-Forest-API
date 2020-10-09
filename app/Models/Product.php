<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Product extends Model
{
    protected $table = 'products';

    protected $fillable = [
        'categoryId','name','price','amount','description','image'
    ];

    public function category(){
        return $this->belongsTo('App\category','categoryId');
    }
}
