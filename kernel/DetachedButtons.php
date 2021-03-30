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
     * Add detached button enabling datetime.
     * 
     * @param string $button_id
     * Cannot be empty.
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

    /**
     * Update detached button datetime.
     * 
     * @param int $id
     * Entry ID. Cannot be lesser than 1.
     * 
     * @param string $button_id
     * Cannot be empty.
     * 
     * @param int $enable_timestamp
     * 
     * @return bool
     * 
     * @throws EPStatistics\Exceptions\DetachedButtonsException
     */
    public function update(int $id, string $button_id, int $enable_timestamp) : bool
    {

        date_default_timezone_set('Europe/Moscow');

        if ($id < 1) throw new DetachedButtonsException(
            DetachedButtonsException::INVALID_ID_MESSAGE,
            DetachedButtonsException::INVALID_ID_CODE
        );

        if (empty($button_id)) throw new DetachedButtonsException(
            DetachedButtonsException::EMPTY_BUTTON_ID_MESSAGE,
            DetachedButtonsException::EMPTY_BUTTON_ID_CODE
        );

        if ($this->wpdb->update(
            $this->wpdb->prefix.$this->table,
            [
                'button_id' => $button_id,
                'enable_datetime' => date("Y-m-d H:i:s", $enable_timestamp)
            ],
            ['id' => $id],
            ['%s', '%s'],
            '%d'
        ) === false) return false;
        else return true;

    }

    /**
     * Select all datetimes.
     * 
     * @param bool $order_by_datetime
     * If true, order datetimes ASC.
     * 
     * @return array
     * 
     * @throws EPStatistics\Exceptions\DetachedButtonsException
     */
    public function selectAll(bool $order_by_datetime = true) : array
    {

        $result = [];

        $condition = "";

        if ($order_by_datetime) $condition = " ORDER BY t.enable_datetime ASC";

        $select = $this->wpdb->get_results(
            "SELECT *
                FROM `".$this->wpdb->prefix.$this->table."` AS t".
                    $condition,
            ARRAY_A
        );

        if (is_array($select)) $result = $select;
        else throw new DetachedButtonsException(
            DetachedButtonsException::SELECTING_FAILURE_MESSAGE,
            DetachedButtonsException::SELECTING_FAILURE_CODE
        );

        return $result;

    }

}
