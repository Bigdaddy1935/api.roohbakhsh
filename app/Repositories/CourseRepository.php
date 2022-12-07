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

    public function GetCoursesData()
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
              ->paginate(10);

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
            ->findOrFail($id);
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
}