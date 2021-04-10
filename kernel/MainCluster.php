<?php
/**
 * @package Event Platform Statistics
 */
namespace EPStatistics;

use wpdb;

/**
 * Application point of entry.
 * @abstract
 * @since 1.9.11
 */
abstract class MainCluster
{

    /**
     * @var string $path
     * Plugin directory.
     * @since 1.9.11
     */
    protected $path;

    /**
     * @var string $url
     * Plugin main URL path.
     * @since 1.9.11
     */
    protected $url;

    /**
     * @var wpdb $wpdb
     * WordPress wpdb object.
     * @since 1.9.11
     */
    protected $wpdb;

    public function __construct(string $path, string $url)
    {

        global $wpdb;

        $this->wpdb = $wpdb;
        
        $this->path = $path;
        $this->url = $url;

        $this->init();

    }

    /**
     * Fires after object construction.
     * @since 1.9.11
     * @abstract
     * 
     * @return $this
     */
    protected function init() : self
    {

        return $this;

    }

}
