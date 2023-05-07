<?php

namespace App\Interfaces;

interface CommentRepositoryInterface
{
    public function AcceptedComments();
    public function rejectedComments();
    public function getSpecificComments($id , $type);
    public function getSpecificAcceptedComments($id , $type);
}