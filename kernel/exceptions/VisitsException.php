<?php
/**
 * @package Event Platform Statistics
 */
namespace EPStatistics\Exceptions;

use Exception;

/**
 * @since 1.9.11
 */
class VisitsException extends Exception
{

    const INVALID_USER_ID_CODE = -51;
    const INVALID_USER_ID_MESSAGE = 'Invalid user ID.';

    const GET_VISITS_FAILURE_CODE = -52;
    const GET_VISITS_FAILURE_MESSAGE = 'Getting visits failure.';

}
