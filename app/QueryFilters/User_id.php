<?php

namespace App\QueryFilters;

use function request;

class User_id extends Filter
{


    protected function applyFilter($builder)
    {
      return  $builder->where('user_id', request($this->filterName()));
    }
}