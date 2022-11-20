<?php

namespace App\QueryFilters;

use function request;

class Role extends Filter
{


    protected function applyFilter($builder)
    {
      return  $builder->where('role', request($this->filterName()));
    }
}