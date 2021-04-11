<?php
/**
 * @package Event Platform Statistics
 */
namespace EPStatistics\Exceptions;

use Exception;

/**
 * @since 1.9.11
 */
class ParticipantsException extends Exception
{

    const DAY_NOT_MATCH_CODE = -81;
    const DAY_NOT_MATCH_MESSAGE = 'Attendance start and end days must match.';

    const INVALID_END_TIMESTAMP_CODE = -82;
    const INVALID_END_TIMESTAMP_MESSAGE = 'End timestamp cannot be lesser or equal than start timestamp.';

    const INVALID_TIMESTAMP_CODE = -83;
    const INVALID_TIMESTAMP_MESSAGE = 'Invalid timestamp.';

}
