<?php

namespace App\Repositories;

use App\Interfaces\LessonRepositoryInterface;
use App\Models\Lesson;
use App\Models\VideoProgressBar;

class LessonRepository extends Repository implements LessonRepositoryInterface
{

    public function model()
    {
        return Lesson::class;
    }

    public function GetLessonData()
    {
        $user= auth('sanctum')->id();
     return   Lesson::query()->join('users', 'users.id', '=', 'lessons.user_id')
              ->select('lessons.*','users.fullname')
            ->with('courses')
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
            ->paginate(10);
    }

    public function GetLessonsOfAnCourse($id)
    {
        $user= auth('sanctum')->id();
        return   Lesson::query()
            ->where('course_id',$id)
            ->with('categories')
            ->withAggregate('visits','score')
            ->withExists(['bookmarkableBookmarks as bookmark'=>function($q) use ($user){
                $q->where('user_id',$user);
            }])->withExists(['likers as like'=>function($q)use ($user){
                $q->where('user_id',$user);
            }])->withCount('likers as like_count')
            ->with('progress',function ($q)use ($user){
                $q->where('user_id',$user);
            })->with('relatedLessons',)->with('relatedArticles',function ($q){
                $q->join('articles','articles.id','=','article_related_for_lessons.article_id')->select('articles.title','article_related_for_lessons.*');
            })
            ->paginate(20);
    }
    public function GetLessonsOfAnCourseGet($id)
    {
        $user= auth('sanctum')->id();
        return   Lesson::query()
            ->where('course_id',$id)
            ->with('categories')
            ->withAggregate('visits','score')
            ->withExists(['bookmarkableBookmarks as bookmark'=>function($q) use ($user){
                $q->where('user_id',$user);
            }])
            ->with('progress',function ($q)use ($user){
                $q->where('user_id',$user);
            })->with('relatedLessons',)->with('relatedArticles',function ($q){
                $q->join('articles','articles.id','=','article_related_for_lessons.article_id')->select('articles.title','article_related_for_lessons.*');
            })
            ->get();
    }

    public function GetSpecificLesson($id)
    {
        $user= auth('sanctum')->id();
       return Lesson::query()
            ->join('users','users.id','=','lessons.user_id')
            ->select('lessons.*','users.fullname')
            ->with('categories')
           ->withExists(['bookmarkableBookmarks as bookmark'=>function($q) use ($user){
               $q->where('user_id',$user);
           }])
           ->withExists(['likers as like'=>function($q)use ($user){
               $q->where('user_id',$user);
           }])->withCount('likers as like_count')
           ->with(['progress'=>function ($q)use ($user){
               $q->where('user_id',$user);
           }])
           ->withCount(['comments'=>function($q){
               $q->where('status','=',1);
           }])->with('relatedLessons',)->with('relatedArticles',function ($q){
               $q->join('articles','articles.id','=','article_related_for_lessons.article_id')->select('articles.title','article_related_for_lessons.*');
           })
            ->findOrFail($id);
    }

    public function lessonsCount()
    {
        return Lesson::query()->get()->count();
    }

    public function GetLessonsOfAnMedia($id)
    {
        $user= auth('sanctum')->id();
        return   Lesson::query()
            ->where('course_id',$id)
            ->with('categories')
            ->withAggregate('visits','score')
            ->withExists(['bookmarkableBookmarks as bookmark'=>function($q) use ($user){
                $q->where('user_id',$user);
            }])->withExists(['likers as like'=>function($q)use ($user){
                $q->where('user_id',$user);
            }])->withCount('likers as like_count')
            ->with('progress',function ($q)use ($user){
                $q->where('user_id',$user);
            })->with('relatedLessons',)->with('relatedArticles',function ($q){
                $q->join('articles','articles.id','=','article_related_for_lessons.article_id')->select('articles.title','article_related_for_lessons.*');
            })
            ->orderBy('id','DESC')
            ->paginate(20);
    }

    public function GetLessonsOfAllMedias()
    {
        $user= auth('sanctum')->id();
        return   Lesson::query()
            ->whereHas('courses',function ($q){
                $q->where('type','=','media');
            })
            ->with('categories')
            ->withAggregate('visits','score')
            ->withExists(['bookmarkableBookmarks as bookmark'=>function($q) use ($user){
                $q->where('user_id',$user);
            }])
            ->with('progress',function ($q)use ($user){
                $q->where('user_id',$user);
            })
            ->with('relatedLessons',)->with('relatedArticles',function ($q){
                $q->join('articles','articles.id','=','article_related_for_lessons.article_id')->select('articles.title','article_related_for_lessons.*');
            })
            ->orderBy('id','DESC')
            ->paginate(35);
    }

    public function lessonsList()
    {
       return Lesson::query()->orderBy('title')->get();
    }

    public function GetLessonsOfAnMahdyar($id)
    {
        $user= auth('sanctum')->id();
        return   Lesson::query()
            ->where('course_id',$id)
            ->with('categories')
            ->withAggregate('visits','score')
            ->withExists(['bookmarkableBookmarks as bookmark'=>function($q) use ($user){
                $q->where('user_id',$user);
            }])->withExists(['likers as like'=>function($q)use ($user){
                $q->where('user_id',$user);
            }])->withCount('likers as like_count')
            ->with('progress',function ($q)use ($user){
                $q->where('user_id',$user);
            })
            ->with('relatedLessons',)->with('relatedArticles',function ($q){
                $q->join('articles','articles.id','=','article_related_for_lessons.article_id')->select('articles.title','article_related_for_lessons.*');
            })
            ->orderBy('id','DESC')
            ->paginate(20);
    }

    public function GetLessonsOfAnKolbe($id)
    {
        $user= auth('sanctum')->id();
        return   Lesson::query()
            ->where('course_id',$id)
            ->with('categories')
            ->withAggregate('visits','score')
            ->withExists(['bookmarkableBookmarks as bookmark'=>function($q) use ($user){
                $q->where('user_id',$user);
            }])->withExists(['likers as like'=>function($q)use ($user){
                $q->where('user_id',$user);
            }])->withCount('likers as like_count')
            ->with('progress',function ($q)use ($user){
                $q->where('user_id',$user);
            })->with('relatedLessons',)->with('relatedArticles',function ($q){
                $q->join('articles','articles.id','=','article_related_for_lessons.article_id')->select('articles.title','article_related_for_lessons.*');
            })
            ->orderBy('id','DESC')
            ->paginate(20);
    }

    public function GetLessonsOfAllMahdyar()
    {
        $user= auth('sanctum')->id();
        return   Lesson::query()
            ->whereHas('courses',function ($q){
                $q->where('type','=','mahdyar');
            })
            ->with('categories')
            ->withAggregate('visits','score')
            ->withExists(['bookmarkableBookmarks as bookmark'=>function($q) use ($user){
                $q->where('user_id',$user);
            }])
            ->with('progress',function ($q)use ($user){
                $q->where('user_id',$user);
            })->with('relatedLessons',)->with('relatedArticles',function ($q){
                $q->join('articles','articles.id','=','article_related_for_lessons.article_id')->select('articles.title','article_related_for_lessons.*');
            })
            ->orderBy('id','DESC')
            ->paginate(35);
    }

    public function GetLessonsOfAllKolbe()
    {
        $user= auth('sanctum')->id();
        return   Lesson::query()
            ->whereHas('courses',function ($q){
                $q->where('type','=','kolbe');
            })
            ->with('categories')
            ->withAggregate('visits','score')
            ->withExists(['bookmarkableBookmarks as bookmark'=>function($q) use ($user){
                $q->where('user_id',$user);
            }])
            ->with('progress',function ($q)use ($user){
                $q->where('user_id',$user);
            })->with('relatedLessons',)->with('relatedArticles',function ($q){
                $q->join('articles','articles.id','=','article_related_for_lessons.article_id')->select('articles.title','article_related_for_lessons.*');
            })
            ->orderBy('id','DESC')
            ->paginate(35);
    }

    public function GetLessonsOfAnTv($id)
    {
        $user= auth('sanctum')->id();
        return   Lesson::query()
            ->where('course_id',$id)
            ->with('categories')
            ->withAggregate('visits','score')
            ->withExists(['bookmarkableBookmarks as bookmark'=>function($q) use ($user){
                $q->where('user_id',$user);
            }])->withExists(['likers as like'=>function($q)use ($user){
                $q->where('user_id',$user);
            }])->withCount('likers as like_count')
            ->with('progress',function ($q)use ($user){
                $q->where('user_id',$user);
            })->with('relatedLessons',)->with('relatedArticles',function ($q){
                $q->join('articles','articles.id','=','article_related_for_lessons.article_id')->select('articles.title','article_related_for_lessons.*');
            })
            ->paginate(20);
    }

    public function GetLessonsOfAllTv()
    {
        $user= auth('sanctum')->id();
        return   Lesson::query()
            ->whereHas('courses',function ($q){
                $q->where('type','=','tv');
            })
            ->with('categories')
            ->withAggregate('visits','score')
            ->withExists(['bookmarkableBookmarks as bookmark'=>function($q) use ($user){
                $q->where('user_id',$user);
            }])
            ->with('progress',function ($q)use ($user){
                $q->where('user_id',$user);
            })->with('relatedLessons',)->with('relatedArticles',function ($q){
                $q->join('articles','articles.id','=','article_related_for_lessons.article_id')->select('articles.title','article_related_for_lessons.*');
            })
            ->paginate(35);
    }

    public function LessonsFromTag($tags, $user)
    {
        return Lesson::withAnyTag($tags)
            ->join('users', 'users.id', '=', 'lessons.user_id')
            ->select('lessons.*','users.fullname')
            ->with('courses')
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
            ->paginate(10);
    }

    public function GetAllPodcast()
    {
        $user= auth('sanctum')->id();
        return   Lesson::query()->where('formats','=','sound')->join('users', 'users.id', '=', 'lessons.user_id')
            ->select('lessons.*','users.fullname')
            ->withWhereHas('courses',function ($q){
                $q->where('type','=','podcast')->where('course_title','=','پادکست');
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
            ->paginate(10);
    }

    public function GetLessonsOfAnCourseFullProgress($id)
    {
        $user= auth('sanctum')->id();
        return   Lesson::query()
            ->withWhereHas('progress',function ($q)use ($user){
                $q->where('user_id',$user)->where('percentage','=','100');
            })
            ->where('course_id',$id)
            ->with('categories')
            ->withAggregate('visits','score')
            ->withExists(['bookmarkableBookmarks as bookmark'=>function($q) use ($user){
                $q->where('user_id',$user);
            }])->withExists(['likers as like'=>function($q)use ($user){
                $q->where('user_id',$user);
            }])->withCount('likers as like_count')
           ->with('relatedLessons',)->with('relatedArticles',function ($q){
                $q->join('articles','articles.id','=','article_related_for_lessons.article_id')->select('articles.title','article_related_for_lessons.*');
            })
            ->paginate(20);
    }

    public function GetLessonsOfAnCourseNotCompleted($id)
    {
        $user= auth('sanctum')->id();
        return   Lesson::query()
            ->withWhereHas('progress',function ($q)use ($user){
                $q->where('user_id',$user)->where('percentage','!=','100');
            })
            ->where('course_id',$id)
            ->with('categories')
            ->withAggregate('visits','score')
            ->withExists(['bookmarkableBookmarks as bookmark'=>function($q) use ($user){
                $q->where('user_id',$user);
            }])->withExists(['likers as like'=>function($q)use ($user){
                $q->where('user_id',$user);
            }])->withCount('likers as like_count')
            ->with('progress',function ($q)use ($user){
                $q->where('user_id',$user);
            })->with('relatedLessons',)->with('relatedArticles',function ($q){
                $q->join('articles','articles.id','=','article_related_for_lessons.article_id')->select('articles.title','article_related_for_lessons.*');
            })
            ->paginate(20);
    }
}

