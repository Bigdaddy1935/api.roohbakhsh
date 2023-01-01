<?php

namespace App\Interfaces;

interface LessonRepositoryInterface
{
    public function GetLessonData();
    public function GetLessonsOfAnCourse($id);
    public function GetLessonsOfAnMedia($id);
    public function GetSpecificLesson($id);
    public function lessonsCount();
}