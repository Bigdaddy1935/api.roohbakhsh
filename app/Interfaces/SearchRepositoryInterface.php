<?php

namespace App\Interfaces;

interface SearchRepositoryInterface
{
    public function SearchInCourseFirstWord($req,$user);
    public function SearchInLessonFirstWord($req,$user);
    public function SearchInArticleFirstWord($req,$user);
    public function SearchInProductFirstWord($req,$user);
    public function SearchInCourse($req,$user);
    public function SearchInArticle($req,$user);
    public function SearchInLesson($req,$user);
    public function SearchInProduct($req,$user);
    public function SearchInQuestion($req,$user,$id);
    public function SearchInClubLessons($req,$user,$id);
    public function SearchInClubCourses($req,$user,$id);
}