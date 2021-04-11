<?php
/**
 * @package Event Platform Statistics
 */
namespace EPStatistics\Exceptions;

use Exception;

/**
 * @since 1.9.11
 */
class TokensException extends Exception
{

    const INVALID_USER_ID_CODE = -41;
    const INVALID_USER_ID_MESSAGE = 'Invalid user ID.';

    const INSERTING_TOKEN_FAILURE_CODE = -44;
    const INSERTING_TOKEN_FAILURE_MESSAGE = 'Inserting token into DB failure.';

    const TOKEN_CANNOT_BE_EMPTY_CODE = -43;
    const TOKEN_CANNOT_BE_EMPTY_MESSAGE = 'Token cannot be empty.';

    const SELECTING_TOKEN_FAILURE_CODE = -45;
    const SELECTING_TOKEN_FAILURE_MESSAGE = 'Token selecting in DB failure.';

}
