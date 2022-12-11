<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class VideoProgressBar extends Model
{
    use HasFactory;

    protected $guarded=[];

    public function lessons(): HasMany
    {
        return    $this->hasMany(Lesson::class,'id','lesson_id');
    }
}
