<?php
/**
 * Event Platform Statistics
 */
namespace EPStatistics;

use EPStatistics\Exceptions\VisitsException;
use wpdb;

class Visits
{

    protected $wpdb;
    protected $dbname;
    protected $table;

    public function __construct(wpdb $wpdb, string $dbname = '')
    {
        
        $this->wpdb = $wpdb;

        if (empty($dbname)) $this->dbname = DB_NAME;
        else $this->dbname = $dbname;

        $this->table = 'epstatistics_visits';

        $this->tableCreate();

    }

    /**
     * Create a table.
     * 
     * @return void
     * 
     * @throws EPStatistics\Exceptions\VisitsException
     */
    public function tableCreate() : void
    {

        if ($this->wpdb->query(
            "CREATE TABLE IF NOT EXISTS `".$this->wpdb->prefix.$this->table."` (
                `id` BIGINT UNSIGNED NOT NULL AUTO_INCREMENT,
                `user_id` BIGINT UNSIGNED NOT NULL,
                `page_url` TEXT NOT NULL,
                `datetime` DATETIME NOT NULL,
                PRIMARY KEY (`id`)
            )
            COLLATE='utf8mb4_unicode_ci'
            AUTO_INCREMENT=0"
        ) === false) throw new VisitsException(
            'Creating table failure.',
            -50
        );

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
            'User ID cannot be less than 1.',
            -51
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
            'Getting visits failure.',
            -52
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
