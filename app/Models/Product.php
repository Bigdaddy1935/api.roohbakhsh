<?php

namespace App\Models;

use BeyondCode\Vouchers\Traits\HasVouchers;
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

class Product extends Model
{
    use Taggable, HasFactory,Likeable,Bookmarkable,HasVouchers;

    protected $fillable=[
        'title',
        'picture',
        'description',
        'tiny_desc',
        'price',
        'teacher',
        'duration',
        'type',
        'status',
        'visibility',
        'price_discount',
        'duration',
        'code',
        'user_id',
        'vouchers',
        'course_id'
    ];


    /**
     * @return MorphToMany
     *
     *
     * products can have
     */
    public function tags(): MorphToMany
    {
        return $this->morphToMany(Tag::class, 'taggable');
    }

    /**
     * @return BelongsToMany
     *
     *
     * products belongs to many categories
     */
    public function categories(): BelongsToMany
    {
        return    $this->belongsToMany(Category::class);
    }

    /**
     * @return BelongsToMany
     *
     * products belongs to many files
     */
    public function files(): BelongsToMany
    {
        return $this->belongsToMany(File::class);
    }


    public function users(): BelongsTo
    {
        return $this->belongsTo(User::class,'user_id');
    }

     public function visits(): Relation
    {
        return visits($this)->relation();
    }

    public function invoices(): BelongsTo
    {
        return $this->belongsTo(Invoice::class ,'id','order_id');
    }

    public function comments()
    {
        return $this->morphMany(Comment::class, 'commentable')->whereNull('parent_id');
    }

    public function courses()
    {
        return $this->hasOne(Course::class,'id','course_id');
    }
}
