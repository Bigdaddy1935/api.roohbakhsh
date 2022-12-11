<?php

namespace App\Repositories;

use App\Interfaces\SearchRepositoryInterface;
use App\Models\Article;
use App\Models\Course;
use App\Models\Lesson;
use App\Models\Product;
use Illuminate\Database\Eloquent\Builder;

class SearchRepository  implements SearchRepositoryInterface
{

    public function SearchInCourseFirstWord($req, $user)
    {
      return Course::query()->where('course_title','LIKE',"{$req}%")->join("users","users.id",'=',"courses.course_user_id")
            ->select("courses.*","users.fullname")
            ->with("categories")
            ->withCount("lessons")
            ->withExists(['bookmarkableBookmarks as bookmark'=>function($q) use ($user){
                $q->where('user_id',$user);
            }])->orderBy('id','DESC')
            ->get()->toArray();
    }

    public function SearchInLessonFirstWord($req, $user)
    {
        return   Lesson::query()->
        with('courses',function ($q) use ($user){
            $q->join("users","users.id",'=',"courses.course_user_id")
                ->select("courses.*","users.fullname")
                ->with('products')
                ->with("categories")
                ->withCount("lessons")
                ->withExists(['bookmarkableBookmarks as bookmark'=>function($q) use ($user){
                    $q->where('user_id',$user);
                }])->orderBy('id','DESC')->get()->toArray();
        })->where('title','LIKE',"{$req}%")
            ->orderBy('id','DESC')->get()->toArray();
    }

    public function SearchInArticleFirstWord($req, $user)
    {
     return   Article::query()
            ->where('title','LIKE',"{$req}%")->join('users','users.id','=','articles.user_id')
            ->select('articles.*','users.fullname')
            ->with(['tagged','categories'])
            ->withExists(['bookmarkableBookmarks as bookmark'=>function($q) use ($user){
                $q->where('user_id',$user);
            }])
            ->withAggregate('visits','score')
         ->orderBy('id','DESC')
         ->get()
         ->toArray();
    }

    public function SearchInCourse($req, $user)
    {
      return  Course::query()->where('course_title','LIKE',"%{$req}%")->join("users","users.id",'=',"courses.course_user_id")
            ->select("courses.*","users.fullname")
            ->with("categories")
            ->withCount("lessons")
            ->withExists(['bookmarkableBookmarks as bookmark'=>function($q) use ($user){
                $q->where('user_id',$user);
            }])
          ->orderBy('id','DESC')
            ->get()->toArray();
    }

    public function SearchInArticle($req, $user)
    {
      return  Article::query()
            ->where('title','LIKE',"%{$req}%")->join('users','users.id','=','articles.user_id')
            ->select('articles.*','users.fullname')
            ->with(['tagged','categories'])
            ->withExists(['bookmarkableBookmarks as bookmark'=>function($q) use ($user){
                $q->where('user_id',$user);
            }])
            ->withAggregate('visits','score')->orderBy('id','DESC')->get()->toArray();
    }

    public function SearchInLesson($req, $user)
    {
     return   Lesson::query()->where('title','LIKE',"%{$req}%")->
            with('courses',function ($q) use ($user){
                $q->join("users","users.id",'=',"courses.course_user_id")
                    ->select("courses.*","users.fullname")
                    ->with('products', function ($q) use ($user){
                        $q->wherehas('courses',function (Builder $q) use ($req) {
                            $q->where('course_title','LIKE',"%{$req}%");
                        })->withExists(['bookmarkableBookmarks as bookmark'=>function($q) use ($user){
                            $q->where('user_id',$user);
                        }])
                            ->with('categories')
                            ->with(['courses'=>function ($q) {
                                $q->withCount('lessons');
                            }])->join('courses','courses.id','products.course_id')->orderBy('id','DESC')->get()->toArray();
                    })
                    ->with("categories")
                    ->withCount("lessons")
                    ->withExists(['bookmarkableBookmarks as bookmark'=>function($q) use ($user){
                        $q->where('user_id',$user);
                    }])->orderBy('id','DESC')->get()->toArray();
            })
            ->orderBy('id','DESC')->get()->toArray();
    }

    public function SearchInProductFirstWord($req, $user)
    {
        return Product::query()->wherehas('courses',function (Builder $q) use ($req) {
            $q->where('course_title','LIKE',"{$req}%");
        })->withExists(['bookmarkableBookmarks as bookmark'=>function($q) use ($user){
            $q->where('user_id',$user);
        }])
            ->with('categories')
            ->with(['courses'=>function ($q) {
                $q->withCount('lessons');
            }])->join('courses','courses.id','products.course_id')->orderBy('id','DESC')->get()->toArray();
    }

    public function SearchInProduct($req, $user)
    {
        return Product::query()->wherehas('courses',function (Builder $q) use ($req) {
            $q->where('course_title','LIKE',"%{$req}%");
        })->withExists(['bookmarkableBookmarks as bookmark'=>function($q) use ($user){
            $q->where('user_id',$user);
        }])
            ->with('categories')
            ->with(['courses'=>function ($q) {
                $q->withCount('lessons');
            }])->join('courses','courses.id','products.course_id')->orderBy('id','DESC')->get()->toArray();
    }

}