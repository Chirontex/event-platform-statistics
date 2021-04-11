<?php
/**
 * @package Event Platform Statistics
 */
namespace EPStatistics\Exceptions;

use Exception;

/**
 * @since 1.9.11
 */
class DetachedButtonsException extends Exception
{

    const EMPTY_BUTTON_ID_MESSAGE = 'Button ID cannot be empty.';
    const EMPTY_BUTTON_ID_CODE = -70;

    const SELECTING_FAILURE_MESSAGE = 'Data selecting failure.';
    const SELECTING_FAILURE_CODE = -71;

    const INVALID_ID_MESSAGE = 'Entry ID cannot be lesser than 1.';
    const INVALID_ID_CODE = -72;

    const DELETING_FAILURE_MESSAGE = 'Data deleting failure.';
    const DELETING_FAILURE_CODE = -73;

}
