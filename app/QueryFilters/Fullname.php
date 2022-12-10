<?php

namespace App\QueryFilters;

use function request;

class Fullname extends Filter
{


    protected function applyFilter($builder)
    {
      return  $builder->where('fullname','LIKE', "%".request($this->filterName())."%");
    }
}