<?php

namespace App\Interfaces;

interface LessonRepositoryInterface
{
    public function GetLessonData();
    public function GetLessonsOfAnCourse($id);
    public function GetLessonsOfAnCourseGet($id);
    public function GetLessonsOfAnMedia($id);
    public function GetLessonsOfAllMedias();
    public function GetSpecificLesson($id);
    public function lessonsCount();
    public function lessonsList();
}