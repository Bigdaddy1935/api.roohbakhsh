<?php

namespace App\Models;


use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\Relation;
use LaravelInteraction\Bookmark\Concerns\Bookmarkable;
use Overtrue\LaravelLike\Traits\Likeable;
use Spatie\Searchable\Searchable;
use Spatie\Searchable\SearchResult;
use function visits;

class Course extends Model
{
    use HasFactory,Likeable,Bookmarkable;

    protected $fillable=[
        'course_title',
        'course_user_id',
        'category_id',
        'description',
        'course_visibility',
        'code',
        'access',
        'course_status',
        'navigation',
        'course_teacher',
        'picture',
        'type'

    ];


    /**
     * @return BelongsTo
     *
     * courses belongs to users
     */
    public function users(): BelongsTo
    {
        return $this->belongsTo(User::class,'course_user_id');
    }


    public function lessons(): HasMany
    {
        return $this->hasMany(Lesson::class);

    }

    /**
     * @return BelongsToMany
     *
     *
     * courses belongs to many categories
     */
    public function categories(): BelongsToMany
    {
    return    $this->belongsToMany(Category::class);
}



    public function visits(): Relation
    {
        return visits($this)->relation();
   }

    public function comments()
    {
        return $this->morphMany(Comment::class, 'commentable')->whereNull('parent_id');
    }

    public function products()
    {
       return $this->belongsTo(Product::class ,'id','course_id',);
    }

    public function notifications()
    {
        return $this->morphMany(Notification::class, 'model');
    }

}
