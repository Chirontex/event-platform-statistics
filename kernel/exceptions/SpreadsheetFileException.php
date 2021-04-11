<?php
/**
 * @package Event Platform Statistics
 */
namespace EPStatistics\Exceptions;

use Exception;

/**
 * @since 1.9.11
 */
class SpreadsheetFileException extends Exception
{

    const FILE_CREATION_FAILURE_CODE = -30;
    const FILE_CREATION_FAILURE_MESSAGE = 'File creation failure.';

}
