<?php

namespace App\Interfaces;

interface ProductRepositoryInterface
{
    public function GetProductsData();
    public function GetSpecificProduct($id);
    public function GetRelatedOfAnProduct($id);

    public function latestProduct();
}