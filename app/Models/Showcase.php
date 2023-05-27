<?php

namespace App\Models;

use ALajusticia\Expirable\Traits\Expirable;
use Carbon\Carbon;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Showcase extends Model
{
    use HasFactory ,Expirable;

    protected $guarded=[];

    const EXPIRES_AT = 'ends_at';

    public function lesson()
    {
        return $this->belongsTo(Lesson::class,'model_id','id');
    }
    public function product()
    {
        return $this->belongsTo(Product::class,'model_id','id');
    }

    public function course()
    {
        return $this->belongsTo(Course::class,'model_id','id');
    }

    public function article()
    {
        return $this->belongsTo(Article::class,'model_id','id');
    }

    public static function defaultExpiresAt()
    {
        return Carbon::now()->addMonths(10);
    }
}

