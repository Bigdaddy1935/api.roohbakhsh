<?php

namespace App\Repositories;

use App\Interfaces\ShowcaseRepositoryInterface;
use App\Models\Showcase;

class ShowcaseRepository extends Repository implements ShowcaseRepositoryInterface
{

    public function model()
    {
        return Showcase::class;
    }

    public function getExpired()
    {
        $user= auth('sanctum')->id();
        return Showcase::query()->with('course',function ($q) use ($user){
            $q ->join("users","users.id",'=',"courses.course_user_id")
                ->select("courses.*","users.fullname")
                ->with("categories")
                ->withCount("lessons")
                ->withExists(['bookmarkableBookmarks as bookmark'=>function($q) use ($user){
                    $q->where('user_id',$user);
                }])
                ->with('lessons',function ($q) use ($user){
                    $q->withWhereHas('progress',function ($q) use($user) {
                        $q->where('user_id',$user)->where('percentage','>',0);
                    });
                });
        })->with('product' ,function ($q) use ($user){
            $q->withExists(['bookmarkableBookmarks as bookmark'=>function($q) use ($user){
                $q->where('user_id',$user);
            }])
                ->with('categories')
                ->with(['courses'=>function ($q) {
                    $q->join('users','users.id','=','courses.course_user_id')
                        ->select('courses.*','users.fullname')->withCount('lessons');
                }])->with(['related'=>function ( $q){
                    $q->with('courses');
                }]);
        })->with('lesson',function ($q)use ($user){
            $q   ->join('users','users.id','=','lessons.user_id')
                ->select('lessons.*','users.fullname')
                ->with('categories')
                ->withExists(['bookmarkableBookmarks as bookmark'=>function($q) use ($user){
                    $q->where('user_id',$user);
                }])
                ->withExists(['likers as like'=>function($q)use ($user){
                    $q->where('user_id',$user);
                }])
                ->with(['progress'=>function ($q)use ($user){
                    $q->where('user_id',$user);
                }]);
        })->with('article',function ($q) use ($user){
            $q  ->join('users','users.id','=','articles.user_id')
                ->select('articles.*','users.fullname')
                ->with('tagged')
                ->with('categories');
        })->onlyExpired()->get();
    }

    public function getNotExpired()
    {
        $user= auth('sanctum')->id();
        return Showcase::query()->with('course',function ($q) use ($user){
            $q ->join("users","users.id",'=',"courses.course_user_id")
                ->select("courses.*","users.fullname")
                ->with("categories")
                ->withCount("lessons")
                ->withExists(['bookmarkableBookmarks as bookmark'=>function($q) use ($user){
                    $q->where('user_id',$user);
                }])
                ->with('lessons',function ($q) use ($user){
                    $q->withWhereHas('progress',function ($q) use($user) {
                        $q->where('user_id',$user)->where('percentage','>',0);
                    });
                });
        })->with('product' ,function ($q) use ($user){
            $q->withExists(['bookmarkableBookmarks as bookmark'=>function($q) use ($user){
                $q->where('user_id',$user);
            }])
                ->with('categories')
                ->with(['courses'=>function ($q) {
                    $q->join('users','users.id','=','courses.course_user_id')
                        ->select('courses.*','users.fullname')->withCount('lessons');
                }])->with(['related'=>function ( $q){
                    $q->with('courses');
                }]);
        })->with('lesson',function ($q)use ($user){
            $q   ->join('users','users.id','=','lessons.user_id')
                ->select('lessons.*','users.fullname')
                ->with('categories')
                ->withExists(['bookmarkableBookmarks as bookmark'=>function($q) use ($user){
                    $q->where('user_id',$user);
                }])
                ->withExists(['likers as like'=>function($q)use ($user){
                    $q->where('user_id',$user);
                }])
                ->with(['progress'=>function ($q)use ($user){
                    $q->where('user_id',$user);
                }]);
        })->with('article',function ($q) use ($user){
            $q  ->join('users','users.id','=','articles.user_id')
                ->select('articles.*','users.fullname')
                ->with('tagged')
                ->with('categories');
        })->get();
    }
}