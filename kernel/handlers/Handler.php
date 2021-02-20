<?php
/**
 * Event Platform Statistics
 */
namespace EPStatistics\Handlers;

use EPStatistics\Traits\Randomizer;
use EPStatistics\Exceptions\HandlerException;

class Handler
{

    use Randomizer;

    protected $path;
    protected $file;
    protected $pathfile;

    public function __construct(string $path)
    {

        if (!file_exists($path)) {

            if (!mkdir($path)) throw new HandlerException(
                HandlerException::DIRECTORY_NOT_EXIST_MESSAGE,
                HandlerException::DIRECTORY_NOT_EXIST_CODE
            );

        }
        
        $this->path = $path;

        if (substr($this->path, -1) !== '/' &&
            substr($this->path, -1) !== '\\') $this->path .= '/';

    }

    /**
     * Create an empty file.
     * 
     * @param string $filename
     * If $filename is empty, if will be generated by $this->generateRandomString().
     * 
     * @param string $extension
     * Dot can be skipped.
     * 
     * @return bool
     */
    protected function fileCreate(string $filename = '', string $extension = 'xlsx') : bool
    {

        if (substr($extension, 0, 1) !== '.') $extension = '.'.$extension;

        if (empty($filename)) {

            do {

                $filename = $this->generateRandomString();

            } while (file_exists($this->path.$filename.$extension));

        }

        $this->file = $filename.$extension;
        $this->pathfile = $this->path.$this->file;

        if (file_put_contents($this->pathfile, '') === false) return false;
        else return true;

    }

    /**
     * Load file data.
     * 
     * @return string
     */
    public function fileLoad() : string
    {

        $result = '';

        $file = file_get_contents($this->pathfile);

        if (is_string($file)) $result = $file;
        else throw new HandlerException(
            HandlerException::FILE_READING_FAILURE_MESSAGE,
            HandlerException::FILE_READING_FAILURE_CODE
        );

        return $result;

    }

    /**
     * Load file data and delete from directory.
     * 
     * @return string
     */
    public function fileGetUnlink() : string
    {

        $file = $this->fileLoad();

        unlink($this->pathfile);

        $this->pathfile = '';
        $this->file = '';

        return $file;

    }

}
