<?php

namespace App\Repositories;

use App\Models\File;

class FileRepository extends Repository
{

    public function model()
    {
        return File::class;
    }
}