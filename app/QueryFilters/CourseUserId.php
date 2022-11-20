<?php

namespace App\QueryFilters;

use function request;

class CourseUserId extends Filter
{


    protected function applyFilter($builder)
    {
      return  $builder->where('course_user_id', request($this->filterName()));
    }
}