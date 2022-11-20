<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Category extends Model
{
    use HasFactory;

    protected $fillable = [
        'name',
        'parent_id',
        'description',
        'slug'
    ];

    /**
     * @return HasMany
     *
     * categories has many sub categories
     *
     * with children allow us to have N subs for a cat
     */
    public function children(): HasMany
    {
        return $this->hasMany(Category::class,'parent_id')->with('children');
    }

    /**
     * @return BelongsToMany
     *
     *
     * categories belongs to many course
     */
    public function course(): BelongsToMany
    {
     return   $this->belongsToMany(Course::class);
}

    /**
     * @return BelongsToMany
     *
     *
     * categories belongs to many lessons
     */
    public function lesson(): BelongsToMany
    {
        return $this->belongsToMany(Lesson::class);
}

    /**
     * @return BelongsToMany
     *
     * categories blongs to many articles
     */
    public function article(): BelongsToMany
    {
        return $this->belongsToMany(Article::class);
}

    /**
     * @return BelongsToMany
     *
     * categories belongs to many products
     */
    public function product(): BelongsToMany
    {
        return $this->belongsToMany(Product::class);
}


}
