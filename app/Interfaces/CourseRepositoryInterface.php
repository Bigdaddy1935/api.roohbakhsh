<?php

namespace App\Interfaces;

interface CourseRepositoryInterface
{

    public function GetCoursesData();
    public function GetSpecificCourse($id);


}