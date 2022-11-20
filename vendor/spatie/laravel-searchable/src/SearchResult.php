<?php

namespace Spatie\Searchable;

class SearchResult
{
    /** @var \Spatie\Searchable\Searchable */
    public $searchable;

    /** @var string */
    public $title;

    /** @var null|string */
    public $free;

    /** @var string */
    public $type;

    public $picture;

    public function __construct(Searchable $searchable, string $title, ?string $free = null , $picture)
    {
        $this->searchable = $searchable;
        $this->title = $title;
        $this->free = $free;
        $this->picture=$picture;
    }

    public function setType(string $type): self
    {
        $this->type = $type;

        return $this;
    }
}
