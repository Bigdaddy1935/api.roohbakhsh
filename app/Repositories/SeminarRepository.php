<?php

namespace App\Repositories;

use App\Models\Seminar;

class SeminarRepository extends Repository
{

    public function model()
    {
        return Seminar::class;
    }
}