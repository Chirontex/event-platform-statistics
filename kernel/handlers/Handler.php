<?php
/**
 * Event Platform Statistics
 */
namespace EPStatistics\Handlers;

class Handler
{

    protected $path;

    public function __construct(string $path)
    {
        
        $this->path = $path;

    }

    /**
     * Load file data.
     * 
     * @param string $filename
     * 
     * @return string
     */
    public function fileLoad(string $filename) : string
    {

        $result = '';

        $file = file_get_contents($this->path.$filename);

        if (is_string($file)) $result = $file;

        return $result;

    }

}
