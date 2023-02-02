<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Notification extends Model
{
    use HasFactory;

    protected $guarded=[];




    public function lesson()
    {
        return $this->belongsTo(Lesson::class);
    }
    public function product()
    {
        return $this->belongsTo(Product::class);
    }
    public function course()
    {
        return $this->belongsTo(Course::class);
    }
    public function article()
    {
        return $this->belongsTo(Article::class);
    }
}
