<?php

namespace App\QueryFilters;

use function request;

class Access extends Filter
{


    protected function applyFilter($builder)
    {
      return  $builder->where('access', request($this->filterName()));
    }
}