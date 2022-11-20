<?php

namespace App\QueryFilters;

use function request;

class Username extends Filter
{


    protected function applyFilter($builder)
    {
      return  $builder->where('username', request($this->filterName()));
    }
}