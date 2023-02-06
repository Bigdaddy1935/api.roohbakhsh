<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\Relation;
use LaravelInteraction\Bookmark\Concerns\Bookmarkable;
use Overtrue\LaravelLike\Traits\Likeable;
use Spatie\Searchable\Searchable;
use Spatie\Searchable\SearchResult;

class Lesson extends Model
{
    use HasFactory,Likeable,Bookmarkable;

    protected $fillable=[

        'title',
        'description',
        'url_video',
        'status_complete',
        'course_id',
        'user_id',
        'status',
        'visibility',
        'code',
        'picture',
        'teacher',
        'url_ads',
        'formats'


    ];
protected $casts=[

    'categories'=>'string'
];



    public function courses(): BelongsTo
    {
        return $this->belongsTo(Course::class,'course_id');
    }

    public function users(): BelongsTo
    {
        return $this->belongsTo(User::class ,'user_id');
    }

    /**
     * @return BelongsToMany
     *
     * lessons belongs to many tags
     */
    public function tags(): BelongsToMany
    {
        return $this->belongsToMany(Tag::class);
    }

    /**
     * @return BelongsToMany
     *
     *
     * lessons belongs to many categories
     */
    public function categories(): BelongsToMany
    {
        return    $this->belongsToMany(Category::class);
    }



    public function visits(): Relation
    {
        return visits($this)->relation();
    }

    public function progress()
    {
        return   $this->hasMany(VideoProgressBar::class,'lesson_id','id');
    }
    public function comments()
    {
        return $this->morphMany(Comment::class, 'commentable')->whereNull('parent_id');
    }

    public function notifications()
    {
        return $this->morphMany(Notification::class, 'model');
    }

    public function showcases()
    {
        return $this->morphMany(Showcase::class, 'model');
    }
}
