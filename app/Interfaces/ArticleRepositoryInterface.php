<?php

namespace App\Interfaces;

interface ArticleRepositoryInterface
{
    public function GetArticlesData();
    public function GetSpecificArticle($id);

}