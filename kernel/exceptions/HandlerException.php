<?php
/**
 * Event Platform Statistics
 */
namespace EPStatistics\Exceptions;

use Exception;

class HandlerException extends Exception
{

    const DIRECTORY_NOT_EXIST_CODE = -20;
    const DIRECTORY_NOT_EXIST_MESSAGE = 'Directory doesn\'t exist and cannot be created.';

    const FILE_READING_FAILURE_CODE = -21;
    const FILE_READING_FAILURE_MESSAGE = 'Cannot read a saved file.';

}
