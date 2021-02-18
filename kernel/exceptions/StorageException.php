<?php
/**
 * Event Platform Statistics
 */
namespace EPStatistics\Exceptions;

use Exception;

class StorageException extends Exception
{

    const CREATE_TABLE_FAILURE_CODE = -100;
    const CREATE_TABLE_FAILURE_MESSAGE = 'Creating table failure.';

}
