<?php

namespace App\Interfaces;

interface ArticleRepositoryInterface
{
    public function GetArticlesData();
    public function GetSpecificArticle($id);
    public function ArticlesCount();

    public function ArticlesFromTag($tags,$user);
}