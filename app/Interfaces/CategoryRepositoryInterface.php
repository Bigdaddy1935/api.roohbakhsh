<?php

namespace App\Interfaces;

interface CategoryRepositoryInterface
{
    public function GetCatWithSub();
    public function Get_Course_With_Their_Cat($id);
    public function Get_Club_With_Their_Cat($id);
    public function Get_Lesson_With_Their_Cat($id);
    public function Get_Article_With_Their_Cat($id);
    public function Get_Product_With_Their_Cat($id);
    public function Get_Podcast_With_Their_Cat($id);
}