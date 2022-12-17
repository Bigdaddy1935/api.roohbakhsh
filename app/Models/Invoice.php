<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Invoice extends Model
{
    use HasFactory;

    protected $guarded=[];


    public function products()
    {
        return $this->hasMany(Product::class ,'id','order_id');
    }

    public function users()
    {
        return $this->hasMany(User::class,'id','user_id');
    }
}
