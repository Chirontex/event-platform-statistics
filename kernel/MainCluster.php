<?php
/**
 * Event Platform Statistics
 */
namespace EPStatistics;

abstract class MainCluster
{

    protected $path;
    protected $url;
    protected $wpdb;

    public function __construct(string $path, string $url)
    {

        global $wpdb;

        $this->wpdb = $wpdb;
        
        $this->path = $path;
        $this->url = $url;

    }

}
