<?php
/**
 * Event Platform Statistics
 */
namespace EPStatistics;

use EPStatistics\Exceptions\VisitsException;
use wpdb;

class Visits extends Storage
{

    public function __construct(wpdb $wpdb)
    {

        $this->table = 'epstatistics_visits';

        $this->fields = [
            'user_id' => 'BIGINT UNSIGNED NOT NULL',
            'page_url' => 'TEXT NOT NULL',
            'datetime' => 'DATETIME NOT NULL'
        ];

        parent::__construct($wpdb);

    }

    /**
     * Add a visit entry in the table.
     * 
     * @param string $url
     * URL of the page was visited.
     * 
     * @param int $user_id
     * User ID cannot be less than 1.
     * 
     * @return bool
     * 
     * @throws EPStatistics\Exceptions\VisitsException
     */
    public function addVisit(string $url, int $user_id) : bool
    {

        if ($user_id < 1) throw new VisitsException(
            VisitsException::INVALID_USER_ID_MESSAGE,
            VisitsException::INVALID_USER_ID_CODE
        );

        date_default_timezone_set('Europe/Moscow');

        if ($this->wpdb->insert(
            $this->wpdb->prefix.$this->table,
            [
                'user_id' => $user_id,
                'page_url' => $url,
                'datetime' => date("Y-m-d H:i:s")
            ],
            ['%d', '%s', '%s']
        ) === false) return false;
        else return true;

    }

    /**
     * Get all visits.
     * 
     * @return array
     * 
     * @throws EPStatistics\Exceptions\VisitsException
     */
    public function getVisits() : array
    {

        $result = [];

        $select = $this->wpdb->get_results(
            "SELECT t.user_id, t.page_url, t.datetime
                FROM `".$this->wpdb->prefix.$this->table."` AS t",
            ARRAY_A
        );

        if (is_array($select)) {

            if (!empty($select)) $result = $select;

        } else throw new VisitsException(
            VisitsException::GET_VISITS_FAILURE_MESSAGE,
            VisitsException::GET_VISITS_FAILURE_CODE
        );

        return $result;

    }

    /**
     * Getting visits ordered by users.
     * 
     * @return array
     */
    public function getVisitsByUsers() : array
    {

        $result = [];

        $visits = $this->getVisits();

        if (!empty($visits)) {

            foreach ($visits as $entry) {

                $result[$entry['user_id']][] = [
                    'page_url' => $entry['page_url'],
                    'datetime' => $entry['datetime']
                ];

            }

        }

        return $result;

    }

    /**
     * Getting visits ordered by pages.
     * 
     * @return array
     */
    public function getVisitsByPages() : array
    {

        $result = [];

        $visits = $this->getVisits();

        if (!empty($visits)) {

            foreach ($visits as $entry) {

                $result[$entry['page_url']][] = [
                    'user_id' => $entry['user_id'],
                    'datetime' => $entry['datetime']
                ];

            }

        }

        return $result;

    }

}
