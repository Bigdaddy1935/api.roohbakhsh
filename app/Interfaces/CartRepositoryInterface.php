<?php

namespace App\Interfaces;

interface CartRepositoryInterface
{

    public function CheckCartExists($product_id ,$user_id);

    public function CartList($user_id);
    public function CartTotal();

    public function discount();

}