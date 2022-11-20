<?php

namespace App\QueryFilters;

use function request;

class Title extends Filter
{


    protected function applyFilter($builder)
    {
      return  $builder->where('title', request($this->filterName()));
    }
}