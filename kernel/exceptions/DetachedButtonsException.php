<?php
/**
 * Event Platform Statistics
 */
namespace EPStatistics\Exceptions;

use Exception;

class DetachedButtonsException extends Exception
{

    const EMPTY_BUTTON_ID_MESSAGE = 'Button ID cannot be empty.';
    const EMPTY_BUTTON_ID_CODE = -70;

    const SELECTING_FAILURE_MESSAGE = 'Data selecting failure.';
    const SELECTING_FAILURE_CODE = -71;

}
