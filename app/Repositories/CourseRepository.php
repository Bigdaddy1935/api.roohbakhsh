<?php

namespace App\Repositories;

use App\Interfaces\CourseRepositoryInterface;
use App\Models\Course;


class CourseRepository extends Repository implements CourseRepositoryInterface
{

    public function model()
    {
        return Course::class;
    }

    public function getCourseMedia()
    {
        $user= auth('sanctum')->id();
//        return
        return Course::all();

    }

    public function GetCoursesData()
    {
       $user= auth('sanctum')->id();
      return Course::query()->where('type','=','course')->join('users','users.id','=','courses.course_user_id')
              ->select('courses.*','users.fullname')
              ->with('categories')
              ->withAggregate('visits','score')
              ->withCount('lessons')
              ->withExists(['bookmarkableBookmarks as bookmark'=>function($q) use ($user){
                    $q->where('user_id',$user);
                }])
          ->with('lessons',function ($q) use ($user){
              $q->withWhereHas('progress',function ($q) use($user) {
                  $q->where('user_id',$user)->where('percentage','>',0);
              });
          })
          ->orderBy('id','DESC')
              ->paginate(10)->toArray();

    }

    public function GetSpecificCourse($id)
    {
        $user= auth('sanctum')->id();
        return  Course::query()
            ->join("users","users.id",'=',"courses.course_user_id")
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
            })
            ->findOrFail($id)->toArray();
    }

    public function courselist()
    {
        $user= auth('sanctum')->id();
        return Course::query()->join('users','users.id','=','courses.course_user_id')
            ->select('courses.*','users.fullname')
            ->with('categories')
            ->withAggregate('visits','score')
            ->withCount('lessons')
            ->withExists(['bookmarkableBookmarks as bookmark'=>function($q) use ($user){
                $q->where('user_id',$user);
            }])
            ->orderBy('id','DESC')
            ->get();
    }


    public function CoursesCount()
    {
       return Course::query()->where('type','=','course')->get()->count();
    }


    public function CourseSeeFull($user)
    {
        return Course::query()->withWhereHas('lessons',function ($q) use ($user){
            $q->withWhereHas('progress',function ($q) use($user) {
                $q->where('user_id',$user)->where('percentage','=','100');
            })->join('users','users.id','=','lessons.user_id')
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
                }]);;

        })->get()->toArray();
    }

    public function CurrentCourseSee($user)
    {
        return Course::query()->withWhereHas('lessons',function ($q) use ($user){
            $q->withWhereHas('progress',function ($q) use($user) {
                $q->where('user_id',$user)->where('percentage','>',0);
            })->join('users','users.id','=','lessons.user_id')
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

        })->withCount('lessons')->get()->toArray();
    }
}