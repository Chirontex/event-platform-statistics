<?php
/**
 * Event Platform Statistics
 */
namespace EPStatistics\Handlers;

use EPStatistics\Titles;

class Programme
{

    protected $titles;

    public function __construct(Titles $titles)
    {
        
        $this->titles = $titles;

    }

}
