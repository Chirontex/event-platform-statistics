<?php
/**
 * Event Platform Statistics
 */
namespace EPStatistics;

use EPStatistics\Exceptions\PresenceTimesException;
use wpdb;

class PresenceTimes
{

    protected $wpdb;
    protected $dbname;
    protected $table;

    public function __construct(wpdb $wpdb, string $dbname = '')
    {
        
        $this->wpdb = $wpdb;

        if (empty($dbname)) $this->dbname = DB_NAME;
        else $this->dbname = $dbname;

        $this->table = 'presence';

        $this->createTable();

    }

    /**
     * Create DB table to store the presence times.
     * 
     * @return void
     * 
     * @throws PresenceTimesException
     */
    public function createTable() : void
    {

        if (!$this->wpdb->query(
            "CREATE TABLE IF NOT EXISTS `".$this->wpdb->prefix.$this->table."` (
                `id` BIGINT UNSIGNED NOT NULL AUTO_INCREMENT,
                `user_id` BIGINT UNSIGNED NOT NULL,
                `presence_datetime` DATETIME NOT NULL,
                PRIMARY KEY (`id`)
            )
            COLLATE='utf8mb4_unicode_ci'
            AUTO_INCREMENT=0"
        )) throw new PresenceTimesException(
            'Cannot create presence table.',
            -30
        );

    }

    /**
     * Add presence time record.
     * 
     * @param int $user_id
     * If $user_id lesser than 1, an exception will be thrown.
     * 
     * @return bool
     * 
     * @throws PresenceTimesException
     */
    public function add(int $user_id) : bool
    {

        date_default_timezone_set('Europe/Moscow');

        if ($user_id < 1) throw new PresenceTimesException(
            'Invalid user ID.',
            -31
        );

        if ($this->wpdb->insert(
                $this->wpdb->prefix.$this->table,
                [
                    'user_id' => $user_id,
                    'presence_datetime' => date("Y-m-d H:i:s")
                ],
                ['%d', '%s']
        ) === false) return false;
        else return true;

    }

    /**
     * Return all presence times records.
     * 
     * @return array
     * 
     * @throws PresenceTimesException
     */
    public function getAll() : array
    {

        $select = $this->wpdb->get_results(
            "SELECT *
                FROM ".$this->dbname.".".$this->wpdb->prefix.$this->table,
            ARRAY_A
        );

        if (is_array($select)) return $select;
        else throw new PresenceTimesException(
            'Getting presence times exception.',
            -32
        );

    }

}
