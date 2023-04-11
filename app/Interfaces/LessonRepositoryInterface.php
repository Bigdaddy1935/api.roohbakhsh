<?php

namespace App\Interfaces;

interface LessonRepositoryInterface
{
    public function GetLessonData();
    public function GetLessonsOfAnCourse($id);
    public function GetLessonsOfAnCourseGet($id);
    public function GetLessonsOfAnMedia($id);
    public function GetLessonsOfAnMahdyar($id);
    public function GetLessonsOfAnKolbe($id);
    public function GetLessonsOfAnTv($id);
    public function GetLessonsOfAllMedias();
    public function GetLessonsOfAllTv();
    public function GetLessonsOfAllMahdyar();
    public function GetLessonsOfAllKolbe();
    public function GetSpecificLesson($id);
    public function LessonsFromTag($tags,$user);
    public function lessonsCount();
    public function lessonsList();
}