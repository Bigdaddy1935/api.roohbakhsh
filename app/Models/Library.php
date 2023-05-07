<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Library extends Model
{
    use HasFactory;


    protected $fillable=[

        'title',
        'size',
        'picture',
        'type',
        'desc'

    ];

    public function galleries()
    {
        return $this->belongsToMany(Gallery::class);
    }



}
