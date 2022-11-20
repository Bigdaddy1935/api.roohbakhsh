<?php

namespace App\Repositories;

use App\Interfaces\ProgressRepositoryInterface;
use App\Models\Lesson;
use App\Models\VideoProgressBar;

class ProgressRepository extends Repository implements ProgressRepositoryInterface
{

    public function model()
    {
            return VideoProgressBar::class;
    }

    public function TakeVideo($Lesson_id)
    {
        return  Lesson::query()->where('id',$Lesson_id)->first();
    }

    public function GetVideoTime($lesson_id, $user_id)
    {
        return VideoProgressBar::query()->where('lesson_id',$lesson_id)->where('user_id',$user_id)->get()->last();
    }




    public function GetLessonsCountOfAnCourse($course_id)
    {
     return   Lesson::query()->where('course_id',$course_id)->count();
    }

    public function WhereProgressIsFull($course_id)
    {
        $id=  auth('sanctum')->id();
     return   Lesson::query()->where('course_id',$course_id)->withWhereHas('progress',function ($q) use($id) {
            $q->where('user_id',$id)->where('percentage','=',100);
        })->count();
    }

    public function CheckProgressBar($user_id, $video_id)
    {
      return  VideoProgressBar::query()->where('user_id',$user_id)->where('lesson_id',$video_id)->first();
    }
}