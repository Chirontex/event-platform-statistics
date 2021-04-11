<?php
/**
 * @package Event Platform Statistics
 */
namespace EPStatistics\Exceptions;

use Exception;

/**
 * @since 1.9.11
 */
class PresenceTimesException extends Exception
{

    const INVALID_USER_ID_CODE = -31;
    const INVALID_USER_ID_MESSAGE = 'Invalid user ID.';

    const EMPTY_LIST_NAME_CODE = -33;
    const EMPTY_LIST_NAME_MESSAGE = 'List name cannot be empty.';

    const GET_PRESENCE_TIMES_FAILURE_CODE = -32;
    const GET_PRESENCE_TIMES_FAILURE_MESSAGE = 'Getting presence times failure.';

}
