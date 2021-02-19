<?php
/**
 * Event Platform Statistics
 */
namespace EPStatistics;

use EPStatistics\Exceptions\PresenceTimesException;
use wpdb;

class PresenceTimes extends Storage
{

    public function __construct(wpdb $wpdb)
    {

        $this->table = 'epstatistics_presence_times';

        $this->fields = [
            'user_id' => 'BIGINT UNSIGNED NOT NULL',
            'presence_datetime' => 'DATETIME NOT NULL',
            'titles_list_name' => 'TEXT NOT NULL'
        ];

        parent::__construct($wpdb);

    }

    /**
     * Add presence time record.
     * 
     * @param int $user_id
     * If $user_id lesser than 1, an exception will be thrown.
     * 
     * @return bool
     * 
     * @throws EPStatistics\Exceptions\PresenceTimesException
     */
    public function add(int $user_id, string $list_name) : bool
    {

        date_default_timezone_set('Europe/Moscow');

        if ($user_id < 1) throw new PresenceTimesException(
            PresenceTimesException::INVALID_USER_ID_MESSAGE,
            PresenceTimesException::INVALID_USER_ID_CODE
        );

        if (empty($list_name)) throw new PresenceTimesException(
            PresenceTimesException::EMPTY_LIST_NAME_MESSAGE,
            PresenceTimesException::EMPTY_LIST_NAME_CODE
        );

        if ($this->wpdb->insert(
                $this->wpdb->prefix.$this->table,
                [
                    'user_id' => $user_id,
                    'titles_list_name' => $list_name,
                    'presence_datetime' => date("Y-m-d H:i:s")
                ],
                ['%d', '%s', '%s']
        ) === false) return false;
        else return true;

    }

    /**
     * Return all presence times records.
     * 
     * @return array
     * 
     * @throws EPStatistics\Exceptions\PresenceTimesException
     */
    public function getAll() : array
    {

        $select = $this->wpdb->get_results(
            "SELECT *
                FROM `".$this->wpdb->prefix.$this->table."`",
            ARRAY_A
        );

        if (is_array($select)) return $select;
        else throw new PresenceTimesException(
            PresenceTimesException::GET_PRESENCE_TIMES_FAILURE_MESSAGE,
            PresenceTimesException::GET_PRESENCE_TIMES_FAILURE_CODE
        );

    }

    /**
     * Return presence times records ordered by users.
     * 
     * @return array
     * 
     * @throws EPStatistics\Exceptions\PresenceTimesException
     */
    public function getOrderedByUsers() : array
    {

        $select = $this->getAll();

        if (empty($select)) return $select;
        else {

            $ordered = [];

            foreach ($select as $record) {

                $ordered[$record['user_id']][] = [
                    'datetime' => $record['presence_datetime'],
                    'list' => $record['titles_list_name']
                ];

            }

            return $ordered;

        }

    }

    /**
     * Return wpdb object which object of this class associated with.
     * 
     * @return wpdb
     */
    public function wpdbGet() : wpdb
    {

        return $this->wpdb;

    }

}
