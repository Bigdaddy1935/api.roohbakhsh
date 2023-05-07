<?php

namespace App\Repositories;

use App\Interfaces\GalleryRepositoryInterface;
use App\Models\Gallery;

class GalleryRepository extends Repository implements GalleryRepositoryInterface
{

    public function model()
    {
        return Gallery::class;
    }

    public function viewGallery()
    {
        return Gallery::query()->with('libraries')->get();
    }
}