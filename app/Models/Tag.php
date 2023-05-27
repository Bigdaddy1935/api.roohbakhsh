<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\MorphToMany;

class Tag extends Model
{
    use HasFactory;
    protected $guarded=[];


    /**
     * @return MorphToMany
     *
     *
     * tags set for many articles
     */
    public function articles(): MorphToMany
    {
        return $this->morphedByMany('App\Article', 'taggable');
    }

    public function lessons(): MorphToMany
    {
        return $this->morphedByMany('App\Lesson', 'taggable');
    }


}
