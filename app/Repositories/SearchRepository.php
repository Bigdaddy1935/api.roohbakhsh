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
                ->with('products',function ($q) use ($user){
                    $q->withExists(['bookmarkableBookmarks as bookmark'=>function($q) use ($user){
                        $q->where('user_id',$user);
                    }])
                        ->with('categories')
                        ->with(['courses'=>function ($q) {
                            $q->join('users','users.id','=','courses.course_user_id')
                                ->select('courses.*','users.fullname')->withCount('lessons');
                        }])->with(['related'=>function ( $q){
                            $q->with('courses');
                        }]) ->orderBy('id','DESC')->get()->toArray();
                })
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
        return   Lesson::query()->
        with('courses',function ($q) use ($user){
            $q->join("users","users.id",'=',"courses.course_user_id")
                ->select("courses.*","users.fullname")
                ->with('products',function ($q) use ($user){
                    $q->withExists(['bookmarkableBookmarks as bookmark'=>function($q) use ($user){
                        $q->where('user_id',$user);
                    }])
                        ->with('categories')
                        ->with(['courses'=>function ($q) {
                            $q->join('users','users.id','=','courses.course_user_id')
                                ->select('courses.*','users.fullname')->withCount('lessons');
                        }])->with(['related'=>function ( $q){
                            $q->with('courses');
                        }]) ->orderBy('id','DESC')->get()->toArray();
                })
                ->with("categories")
                ->withCount("lessons")
                ->withExists(['bookmarkableBookmarks as bookmark'=>function($q) use ($user){
                    $q->where('user_id',$user);
                }])->orderBy('id','DESC')->get()->toArray();
        })->where('title','LIKE',"%{$req}%")
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

    public function SearchInQuestion($req, $user,$id)
    {
        return   Lesson::query()->where('formats','=','sound')
            ->join('users', 'users.id', '=', 'lessons.user_id')
            ->select('lessons.*','users.fullname')
            ->WhereHas('courses',function ($q){
                $q->where('course_title','=','سوالات');
            })
            ->WhereHas('categories',function ($q) use ($id){
                $q->where('id',$id);
            })
            ->withAggregate('visits','score')
            ->withExists(['bookmarkableBookmarks as bookmark'=>function($q) use ($user){
                $q->where('user_id',$user);
            }])
            ->with(['progress'=>function ($q)use ($user){
                $q->where('user_id',$user);
            }])->with('relatedLessons',)->with('relatedArticles',function ($q){
                $q->join('articles','articles.id','=','article_related_for_lessons.article_id')->select('articles.title','article_related_for_lessons.*');
            })
            ->where('title','LIKE',"%{$req}%")
            ->orderBy('id','DESC')->paginate(10);
    }

    public function SearchInClubLessons($req, $user, $id)
    {
        return   Lesson::query()->where('course_id',$id)
            ->join('users', 'users.id', '=', 'lessons.user_id')
            ->select('lessons.*','users.fullname')
            ->WhereHas('courses',function ($q){
                $q->where('type','=','club');
            })
            ->with('categories')
            ->withAggregate('visits','score')
            ->withExists(['bookmarkableBookmarks as bookmark'=>function($q) use ($user){
                $q->where('user_id',$user);
            }])
            ->with(['progress'=>function ($q)use ($user){
                $q->where('user_id',$user);
            }])->with('relatedLessons',)->with('relatedArticles',function ($q){
                $q->join('articles','articles.id','=','article_related_for_lessons.article_id')->select('articles.title','article_related_for_lessons.*');
            })
            ->where('title','LIKE',"%{$req}%")
            ->orderBy('id','DESC')->paginate(10);
    }

    public function SearchInClubCourses($req, $user, $id)
    {
        return  Course::query()->where('course_title','LIKE',"%{$req}%")->where('type','=','club')->join("users","users.id",'=',"courses.course_user_id")
            ->select("courses.*","users.fullname")
            ->WhereHas('categories',function ($q) use ($id){
                $q->where('id',$id);
            })
            ->with('categories')
            ->withCount("lessons")
            ->withExists(['bookmarkableBookmarks as bookmark'=>function($q) use ($user){
                $q->where('user_id',$user);
            }])
            ->orderBy('id','DESC')
            ->paginate(10);
    }
}