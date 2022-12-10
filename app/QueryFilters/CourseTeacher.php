<?php

namespace App\QueryFilters;

use function request;

class CourseTeacher extends Filter
{


    protected function applyFilter($builder)
    {
      return  $builder->where('course_teacher','LIKE', "%".request($this->filterName())."%");
    }
}