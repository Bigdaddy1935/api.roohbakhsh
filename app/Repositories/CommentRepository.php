<?php

namespace App\Repositories;

use App\Interfaces\CommentRepositoryInterface;
use App\Models\Comment;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Http\Request;


class CommentRepository extends Repository implements CommentRepositoryInterface
{

    public function model()
    {
        return Comment::class;
    }

    public function getComments($product_id)
    {
        $user= auth('sanctum')->id();
        return Comment::query()
            ->with('replies')
            ->with('user',function ($q) use ($user,$product_id){
                $q->with('invoices',function ($q) use ($user,$product_id){
                  $q->where('user_id',$user)->where('order_id',$product_id)->get();
                })->get();
            })
            ->withExists(['likers as like'=>function($q)use ($user){
                $q->where('user_id',$user);
            }])
            ->withCount('likers as like_count')
            ->whereNull('parent_id')
            ->orderBy('id','DESC')
            ->get()->toArray();
    }



}