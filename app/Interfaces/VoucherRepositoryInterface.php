<?php

namespace App\Interfaces;

interface VoucherRepositoryInterface
{

    public function CodeExists($code);
    public function CodeUsed($user_id);
    public function GetTotalPriceOfCart($user_id);
}