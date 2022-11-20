<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;

class File extends Model
{
    use HasFactory;


    protected $guarded=[];

    /**
     * @return BelongsToMany
     *
     *
     * files belongs to many products
     */
    public function products(): BelongsToMany
    {
        return $this->belongsToMany(Product::class);
}
}
