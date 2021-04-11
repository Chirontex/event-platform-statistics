<?php
/**
 * @package Event Platform Statistics
 */
namespace EPStatistics\Exceptions;

use Exception;

/**
 * @since 1.9.11
 */
class StorageException extends Exception
{

    const CREATE_TABLE_FAILURE_CODE = -100;
    const CREATE_TABLE_FAILURE_MESSAGE = 'Creating table failure.';

}
