<?php

namespace App\QueryFilters;

use function request;

class Sort extends Filter
{


    protected function applyFilter($builder)
    {

      return  $builder->orderBy('updated_at', request($this->filterName()));

    }
}