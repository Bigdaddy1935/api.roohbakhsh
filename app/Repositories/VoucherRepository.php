<?php

namespace App\Repositories;

use App\Interfaces\VoucherRepositoryInterface;
use App\Models\Cart;
use App\Models\User;
use App\Models\Voucher;

class VoucherRepository extends Repository implements VoucherRepositoryInterface
{

    public function model()
    {
        return Voucher::class;
    }


    public function CodeExists($code)
    {
       return  Voucher::query()->where('code',$code)->first();
    }

    public function CodeUsed($user_id)
    {
      return  User::query()->withWhereHas('vouchers',function ($q) use ($user_id){
            $q->where('user_id',$user_id);
        })->first();
    }

    public function GetTotalPriceOfCart($user_id)
    {
      return  Cart::query()->where('user_id',$user_id)->sum('price');
    }
}