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
             }])
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
          }])
             ->with('progress',function ($q)use ($user){
                 $q->where('user_id',$user);
             })
            ->paginate(10);
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
           }])
           ->with(['progress'=>function ($q)use ($user){
               $q->where('user_id',$user);
           }])
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
            }])
            ->with('progress',function ($q)use ($user){
                $q->where('user_id',$user);
            })->latest('id')
            ->paginate(10);
    }
}