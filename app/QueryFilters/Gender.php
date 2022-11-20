<?php

namespace App\QueryFilters;

use function request;

class Gender extends Filter
{


    protected function applyFilter($builder)
    {
      return  $builder->where('gender', request($this->filterName()));
    }
}