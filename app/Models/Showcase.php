<?php

namespace App\Models;

use ALajusticia\Expirable\Traits\Expirable;
use Carbon\Carbon;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Showcase extends Model
{
    use HasFactory , Expirable;

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

    public static function defaultExpiresAt()
    {
        return Carbon::now()->addMonths(10);
    }
}

