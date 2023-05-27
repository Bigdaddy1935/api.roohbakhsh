<?php

namespace App\Repositories;

use App\Interfaces\CartRepositoryInterface;
use App\Models\Cart;
use App\Models\Product;

class CartRepository extends Repository implements CartRepositoryInterface
{

    public function model()
    {
       return Cart::class;
    }


    public function CheckCartExists($product_id, $user_id)
    {
      return  Cart::query()->where('product_id',$product_id)->where('user_id',$user_id)->first();
    }

    public function CartList($user_id)
    {
       return Cart::query()->where('user_id',$user_id)->get();
    }

    public function CartTotal($user_id)
    {
      return  Cart::query()->where('user_id',$user_id)->sum('price');
    }

    public function discount()
    {

    }
}