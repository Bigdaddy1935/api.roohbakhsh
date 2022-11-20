<?php

namespace App\Repositories;

use App\Interfaces\InvoiceRepositoryInterface;
use App\Models\Invoice;

class InvoiceRepository extends Repository implements InvoiceRepositoryInterface
{

    public function model()
    {
        return Invoice::class;
    }
}