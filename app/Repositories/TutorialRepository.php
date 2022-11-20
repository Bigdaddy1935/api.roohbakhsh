<?php

namespace App\Repositories;

use App\Models\Tutorial;

class TutorialRepository extends Repository
{

    public function model()
    {
        return Tutorial::class;
    }
}