<?php

namespace EPStatistics;

class Main
{

    protected $path;
    protected $url;

    public function __construct(string $path, string $url)
    {
        
        $this->path = $path;
        $this->url = $url;

    }

}
