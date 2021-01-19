<?php
/**
 * Event Platform Statistics
 */
namespace EPStatistics;

final class Main
{

    private $path;
    private $url;
    private $wpdb;

    public function __construct(string $path, string $url)
    {

        global $wpdb;

        $this->wpdb = $wpdb;
        
        $this->path = $path;
        $this->url = $url;

    }

}
