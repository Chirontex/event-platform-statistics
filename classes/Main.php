<?php

namespace EPStatistics;

final class Main
{

    protected $path;
    protected $url;
    protected $wpdb;

    public function __construct(string $path, string $url)
    {

        global $wpdb;
        
        $this->path = $path;
        $this->url = $url;
        $this->wpdb = $wpdb;

    }

}
