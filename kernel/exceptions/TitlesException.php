<?php
/**
 * Event Platform Statistics
 */
namespace EPStatistics\Exceptions;

use Exception;

class TitlesException extends Exception
{

    const EMPTY_TITLE_CODE = -51;
    const EMPTY_TITLE_MESSAGE = 'Title cannot be empty.';

    const EMPTY_LIST_NAME_CODE = -54;
    const EMPTY_LIST_NAME_MESSAGE = 'List name cannot be empty.';

    const INVALID_ID_CODE = -53;
    const INVALID_ID_MESSAGE = 'Invalid entry ID.';

    const SELECT_TITLES_FAILURE_CODE = -52;
    const SELECT_TITLES_FAILURE_MESSAGE = 'Selecting titles from DB failure.';

}
