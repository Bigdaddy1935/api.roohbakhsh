<?php

namespace App\Models;

use Conner\Tagging\Taggable;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\MorphToMany;
use Illuminate\Database\Eloquent\Relations\Relation;
use LaravelInteraction\Bookmark\Concerns\Bookmarkable;
use Overtrue\LaravelLike\Traits\Likeable;
use Spatie\Searchable\Searchable;
use Spatie\Searchable\SearchResult;

class Article extends Model
{
    use Taggable,HasFactory,Likeable,Bookmarkable;

    protected $fillable = [ 'title','user_id','description','status','visibility','code','picture' ];

    /**
     * @return MorphToMany
     *
     *
     * can set tags for articles
     */
    public function tags(): MorphToMany
    {
        return $this->morphToMany('App\Tag', 'taggable');
    }

    public function users(): BelongsTo
    {
        return $this->belongsTo(User::class ,'user_id');
    }

    /**
     * @return BelongsToMany
     *
     * articles belongs to many categories
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
}
