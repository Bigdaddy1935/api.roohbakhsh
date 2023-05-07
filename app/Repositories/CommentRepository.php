<?php

namespace App\Repositories;

use App\Interfaces\CommentRepositoryInterface;
use App\Models\Comment;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Http\Request;


class
CommentRepository extends Repository implements CommentRepositoryInterface
{

    public function model()
    {
        return Comment::class;
    }

    public function getComments()
    {
        $user= auth()->id();
        return Comment::query()
            ->with('replies')
            ->with('user')
            ->withExists(['likers as like'=>function($q)use ($user){
                $q->where('user_id',$user);
            }])
            ->with('commentable')
            ->withCount('likers as like_count')
            ->whereNull('parent_id')
            ->orderBy('id','DESC')
            ->paginate(10);
    }


    public function AcceptedComments()
    {
        $user= auth()->id();
        return Comment::query()
            ->where('status','=',1)
            ->with('replies')
            ->with('user')
            ->withExists(['likers as like'=>function($q)use ($user){
                $q->where('user_id',$user);
            }])
            ->with('commentable')
            ->withCount('likers as like_count')
            ->whereNull('parent_id')
            ->orderBy('id','DESC')
            ->paginate(10);
    }

    public function rejectedComments()
    {
        $user= auth()->id();
        return Comment::query()
            ->where('status','=',0)
            ->with('replies')
            ->with('user')
            ->withExists(['likers as like'=>function($q)use ($user){
                $q->where('user_id',$user);
            }])
            ->with('commentable')
            ->withCount('likers as like_count')
            ->whereNull('parent_id')
            ->orderBy('id','DESC')
            ->paginate(10);
    }

    public function getSpecificComments($id, $type)
    {

        $user= auth()->id();
      return  Comment::query()
            ->where('commentable_type','=',$type)
            ->where('commentable_id','=',$id)

            ->with('replies')
            ->with('user')
            ->withExists(['likers as like'=>function($q)use ($user){
                $q->where('user_id',$user);
            }])
            ->withCount('likers as like_count')
            ->whereNull('parent_id')
            ->orderBy('id','DESC')
            ->paginate(10);
    }

    public function getSpecificAcceptedComments($id, $type)
    {

        $user= auth()->id();
        return  Comment::query()
            ->where('status','=',"1")
            ->where('commentable_type','=',$type)
            ->where('commentable_id','=',$id)
            ->with('replies')
            ->with('user')
            ->withExists(['likers as like'=>function($q)use ($user){
                $q->where('user_id',$user);
            }])
            ->withCount('likers as like_count')
            ->whereNull('parent_id')
            ->orderBy('id','DESC')
            ->paginate(10);
    }
}