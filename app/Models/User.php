<?php

namespace App\Models;


use BeyondCode\Vouchers\Traits\CanRedeemVouchers;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Laravel\Sanctum\HasApiTokens;
use LaravelInteraction\Bookmark\Concerns\Bookmarker;
use Overtrue\LaravelLike\Traits\Liker;
use Stephenjude\Wallet\Traits\HasWallet;

class User extends Authenticatable
{
    use  HasApiTokens,HasFactory, Notifiable,Liker,Bookmarker,HasWallet,CanRedeemVouchers;


    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $guarded=[];

    /**
     * The attributes that should be hidden for serialization.
     *
     * @var array<int, string>
     */
    protected $hidden = [
        'password',
        'remember_token',
        'api_token'
    ];

    /**
     * The attributes that should be cast.
     *
     * @var array<string, string>
     */
    protected $casts = [
        'email_verified_at' => 'datetime',
    ];


    /**
     * @return HasMany
     *
     *
     * users has many courses
     */
    public function courses(): HasMany
    {
        return $this->hasMany(Course::class );
    }

    /**
     * @return HasMany
     *
     *
     * users has many articles
     */
    public function articles(): HasMany
    {
        return $this->hasMany(Article::class);
    }

    /**
     * @return HasMany
     *
     *
     * we don't use this method in cms
     */
    public function comments(): HasMany
    {
        return $this->hasMany(Comment::class);
    }

    /**
     * @return HasMany
     *
     * we don't use this method on cms
     */
    public function tickets(): HasMany
    {
        return $this->hasMany(Ticket::class);
    }


    public function progress(): BelongsTo
    {
        return   $this->belongsTo(VideoProgressBar::class,'user_id','users.id');
    }
    public function vouchers(): BelongsToMany
    {
        return    $this->belongsToMany(Voucher::class);
    }





}
