<?php

namespace EPStatistics;

use wpdb;

final class Main
{

    protected $path;
    protected $url;
    protected $wpdb;

    public function __construct(string $path, string $url, wpdb $wpdb)
    {
        
        $this->path = $path;
        $this->url = $url;
        $this->wpdb = $wpdb;

    }

}
