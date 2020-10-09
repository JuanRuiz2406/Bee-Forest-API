<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Material extends Model
{
    protected $table = 'materials';

    protected $fillable = [
        'providerId','name','price','amount','description','image'
    ];

    public function provider(){
        return $this->belongsTo('App\Provider','providerId');
    }
}
