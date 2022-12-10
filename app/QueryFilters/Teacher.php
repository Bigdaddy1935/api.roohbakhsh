<?php

namespace App\QueryFilters;

use function request;

class Teacher extends Filter
{


    protected function applyFilter($builder)
    {
      return  $builder->where('teacher','LIKE', "%".request($this->filterName())."%");
    }
}