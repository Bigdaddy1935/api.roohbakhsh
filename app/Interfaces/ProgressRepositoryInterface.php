<?php

namespace App\Interfaces;

interface ProgressRepositoryInterface
{
    public function TakeVideo($Lesson_id);
    public function GetVideoTime($lesson_id,$user_id);

    public function CheckProgressBar($user_id,$video_id);
    public function GetLessonsCountOfAnCourse($course_id);

    public function WhereProgressIsFull($course_id);
}