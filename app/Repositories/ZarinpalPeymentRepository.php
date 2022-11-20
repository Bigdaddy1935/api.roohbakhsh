<?php

namespace App\Repositories;

use App\Interfaces\ZarinpalRepositoryInterface;
use App\Models\Zarinpal;

class ZarinpalPeymentRepository extends Repository implements ZarinpalRepositoryInterface
{

    public function model()
    {
        return Zarinpal::class;
    }

    public function VerifyZarinpalPayment($authority)
    {
       return  Zarinpal::query()->where('authority',$authority)->first();
    }
}