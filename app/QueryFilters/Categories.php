<?php

namespace App\QueryFilters;

class Categories extends Filter
{


    protected function applyFilter($builder)
    {

        //get id from value of filter
       $id= request($this->filterName());

       //check id on pivot table of course_category and return true
        return   $builder->whereHas('categories',function($query) use ($id){
               $query->where('categories.id',$id);
      });
    }
}