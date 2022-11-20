<?php

namespace App\QueryFilters;

use function request;

class Visibility extends Filter
{


    protected function applyFilter($builder)
    {
      return  $builder->where('visibility', request($this->filterName()));
    }
}