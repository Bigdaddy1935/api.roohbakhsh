<?php

namespace App\Interfaces;

interface ShowcaseRepositoryInterface
{

    public function getExpired();

    public function getNotExpired();

}