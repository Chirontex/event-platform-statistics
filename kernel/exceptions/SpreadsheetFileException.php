<?php
/**
 * Event Platform Statistics
 */
namespace EPStatistics\Exceptions;

use Exception;

class SpreadsheetFileException extends Exception
{

    const FILE_CREATION_FAILURE_CODE = -30;
    const FILE_CREATION_FAILURE_MESSAGE = 'File creation failure.';

}
