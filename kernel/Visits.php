<?php
/**
 * @package Event Platform Statistics
 */
namespace EPStatistics;

use EPStatistics\Exceptions\VisitsException;

/**
 * Visits storage.
 * @since 1.9.11
 */
class Visits extends Storage
{

    protected $table = 'epstatistics_visits';

    protected $fields = [
        'user_id' => 'BIGINT UNSIGNED NOT NULL',
        'page_url' => 'TEXT NOT NULL',
        'datetime' => 'DATETIME NOT NULL'
    ];

    /**
     * Add a visit entry in the table.
     * @since 1.9.11
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
     * @since 1.9.11
     * 
     * @param string $date_start
     * Start date.
     * 
     * @param string $date_end
     * End date.
     * 
     * @return array
     * 
     * @throws EPStatistics\Exceptions\VisitsException
     */
    public function getVisits(string $date_start = '', string $date_end = '') : array
    {

        $result = [];

        $where = '';

        if (!empty($date_start) ||
            !empty($date_end)) {

            $where = " WHERE";

            if (!empty($date_start)) $where .= " t.datetime > '".$date_start."'";

            if (!empty($date_end)) {

                if ($where !== " WHERE") $where .= " AND";

                $where .= " t.datetime < '".$date_end."'";

            }

        }

        $select = $this->wpdb->get_results(
            "SELECT t.user_id, t.page_url, t.datetime
                FROM `".$this->wpdb->prefix.$this->table."` AS t".$where,
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
     * @since 1.9.11
     * 
     * @param string $page_url
     * Page URL. Optional.
     * 
     * @param int $since_timestamp
     * Timestamp since which we need visits. Optional.
     * 
     * @param int $to_timestamp
     * Timestamp to wich we need visits. Optional.
     * 
     * @return array
     * 
     * @throws EPStatistics\Exceptions\VisitsException
     */
    public function getVisitsByUsers(string $page_url = '', int $since_timestamp = 0, int $to_timestamp = 0) : array
    {

        date_default_timezone_set("Europe/Moscow");

        $result = [];

        $where = "";

        if (!empty($page_url)) $where = $this->wpdb->prepare(
            " WHERE t.page_url = %s", $page_url
        );

        if ($since_timestamp !== 0) $where .= (empty($where) ?
            " WHERE" : " AND")." t.datetime > '".
                date("Y-m-d H:i:s", $since_timestamp)."'";

        if ($to_timestamp !== 0) $where .= (empty($where) ?
            " WHERE" : " AND")." t.datetime < '".
                date("Y-m-d H:i:s", $to_timestamp)."'";

        $visits = $this->wpdb->get_results(
            "SELECT t.user_id, t.page_url, t.datetime
                FROM `".$this->wpdb->prefix.$this->table."` AS t".
                $where." ORDER BY t.datetime ASC",
            ARRAY_A
        );

        if (is_array($visits)) {

            foreach ($visits as $entry) {

                $result[$entry['user_id']][] = [
                    'page_url' => $entry['page_url'],
                    'datetime' => $entry['datetime']
                ];

            }

        } else throw new VisitsException(
            VisitsException::GET_VISITS_FAILURE_MESSAGE,
            VisitsException::GET_VISITS_FAILURE_CODE
        );

        return $result;

    }

    /**
     * Getting visits ordered by pages.
     * @since 1.9.11
     * 
     * @param int $user_id
     * 
     * @param int $since_timestamp
     * Timestamp since which we need visits. Optional.
     * 
     * @param int $to_timestamp
     * Timestamp to which we need visits. Optional.
     * 
     * @return array
     * 
     * @throws EPStatistics\Exceptions\VisitsException
     */
    public function getVisitsByPages(int $user_id = 0, int $since_timestamp = 0, int $to_timestamp = 0) : array
    {

        $result = [];

        $where = "";

        if ($user_id !== 0) $where = $this->wpdb->prepare(
            " WHERE t.user_id = %d",
            $user_id
        );

        if ($since_timestamp !== 0) $where .= (empty($where) ?
            " WHERE" : " AND")." t.datetime > '".
                date("Y-m-d H:i:s", $since_timestamp)."'";

        if ($to_timestamp !== 0) $where .= (empty($where) ?
            " WHERE" : " AND")." t.datetime < '".
                date("Y-m-d H:i:s", $to_timestamp)."'";

        $visits = $this->wpdb->get_results(
            "SELECT t.user_id, t.page_url, t.datetime
                FROM `".$this->wpdb->prefix.$this->table."` AS t".
                $where." ORDER BY t.datetime ASC",
            ARRAY_A
        );

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
