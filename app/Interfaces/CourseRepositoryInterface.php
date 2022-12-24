<?php

namespace App\Interfaces;

interface CourseRepositoryInterface
{

    public function GetCoursesData();
    public function GetSpecificCourse($id);
    public function courselist();
    public function CoursesCount();
    public function CourseSeeFull($user);
    public function CurrentCourseSee($user);
    public function getCourseMedia();

}