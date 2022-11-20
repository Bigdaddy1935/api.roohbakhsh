<?php

namespace App\Repositories;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;

abstract class Repository
{
    protected $model;
    public function __construct()
    {
        $this->model =app($this->model());
    }

    abstract public function model();

    public function all()
    {
        return $this->model->orderBy('id','DESC')->get();
    }

    public function create(array $data)
    {
        return $this->model->create($data);
    }

    public function update($id , array $data)
    {
        $record=$this->model->find($id);
          $record->where('id',$id)->update($data);
        return   $this->model->find($id);

//        return $this->model->findOrFail($id)->update($data);
    }

    public function delete($ids)
    {
        return $this->model->destroy($ids);
    }
    public function find($id){
        return $this->model->findOrFail($id);
    }

}