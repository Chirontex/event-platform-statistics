<?php
/**
 * Event Platform Statistics
 */
namespace EPStatistics\Exceptions;

use Exception;

class MetadataMatchingException extends Exception
{

    const EMPTY_NAME_CODE = -70;
    const EMPTY_NAME_MESSAGE = 'Name cannot be empty.';

    const EMPTY_KEY_CODE = -71;
    const EMPTY_KEY_MESSAGE = 'Key cannot be empty.';

    const MATCH_INSERT_FAILURE_CODE = -72;
    const MATCH_INSERT_FAILURE_MESSAGE = 'Match insertion failure.';

    const MATCH_INVALID_ID_CODE = -73;
    const MATCH_INVALID_ID_MESSAGE = 'Invalid match ID.';

    const MATCH_UPDATE_FAILURE_CODE = -74;
    const MATCH_UPDATE_FAILURE_MESSAGE = 'Match updating failure.';

    const MATCH_DELETE_FAILURE_CODE = -75;
    const MATCH_DELETE_FAILURE_MESSAGE = 'Match deleting failure.';

}
