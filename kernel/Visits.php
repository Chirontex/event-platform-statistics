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

}
