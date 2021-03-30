<?php
/**
 * Event Platform Statistics
 */
namespace EPStatistics;

use EPStatistics\Exceptions\DetachedButtonsException;
use wpdb;

class DetachedButtons extends Storage
{

    public function __construct(wpdb $wpdb)
    {
        
        $this->table = 'epstatistics_detached_buttons';

        $this->fields = [
            'button_id' => 'TEXT NOT NULL',
            'enable_datetime' => 'DATETIME NOT NULL'
        ];

        parent::__construct($wpdb);

    }

    /**
     * Add detached button enabling timestamp.
     * 
     * @param string $button_id
     * 
     * @param int $enable_timestamp
     * 
     * @return bool
     * 
     * @throws EPStatistics\Exceptions\DetachedButtonsException
     */
    public function add(string $button_id, int $enable_timestamp) : bool
    {

        date_default_timezone_set('Europe/Moscow');

        if (empty($button_id)) throw new DetachedButtonsException(
            DetachedButtonsException::EMPTY_BUTTON_ID_MESSAGE,
            DetachedButtonsException::EMPTY_BUTTON_ID_CODE
        );

        if ($this->wpdb->insert(
            $this->wpdb->prefix.$this->table,
            [
                'button_id' => $button_id,
                'enable_datetime' => date("Y-m-d H:i:s", $enable_timestamp)
            ],
            ['%s', '%s']
        ) === false) return false;
        else return true;

    }

}
