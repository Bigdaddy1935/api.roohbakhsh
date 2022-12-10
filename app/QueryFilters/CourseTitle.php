<?php

namespace App\QueryFilters;

use function request;

class CourseTitle extends Filter
{


    protected function applyFilter($builder)
    {
      return  $builder->where('course_title','LIKE', "%".request($this->filterName())."%");
    }
}