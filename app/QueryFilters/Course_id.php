<?php

namespace App\QueryFilters;

use function request;

class Course_id extends Filter
{


    protected function applyFilter($builder)
    {
      return  $builder->where('course_id', request($this->filterName()));
    }
}