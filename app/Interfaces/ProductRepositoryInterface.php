<?php

namespace App\Interfaces;

interface ProductRepositoryInterface
{
    public function GetProductsData();
    public function GetSpecificProduct($id);
    public function GetRelatedOfAnProduct($id);
    public function ProductList();
    public function latestProduct();
    public function ProductsCount();
}