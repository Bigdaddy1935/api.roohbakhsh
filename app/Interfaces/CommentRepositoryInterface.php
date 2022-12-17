<?php

namespace App\Interfaces;

interface CommentRepositoryInterface
{
    public function getComments($product_id);
}