<?php

namespace App\QueryFilters;

use function request;

class CourseVisibility extends Filter
{


    protected function applyFilter($builder)
    {
      return  $builder->where('course_visibility', request($this->filterName()));
    }
}