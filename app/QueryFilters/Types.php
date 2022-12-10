<?php

namespace App\QueryFilters;

use function request;

class Types extends Filter
{


    protected function applyFilter($builder)
    {
      return  $builder->where('type','=', request($this->filterName()));
    }
}