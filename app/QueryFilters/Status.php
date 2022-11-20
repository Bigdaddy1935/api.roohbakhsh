<?php

namespace App\QueryFilters;

use function request;

class Status extends Filter
{


    protected function applyFilter($builder)
    {
      return  $builder->where('status', request($this->filterName()));
    }
}