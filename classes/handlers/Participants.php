<?php

namespace EPStatistics\Handlers;

use wpdb;

class Participants
{

    protected $wpdb;
    protected $dbname;

    public function __construct(wpdb $wpdb, string $dbname = '')
    {
        
        $this->wpdb = $wpdb;

        if (empty($dbname)) $this->dbname = DB_NAME;
        else $this->dbname = $dbname;

    }

    public function getAllData() : array
    {

        $select = $this->wpdb->get_results(
            "SELECT t.user_id, t.meta_key, t.meta_value, t1.user_email
                FROM ".$this->dbname.".".$this->wpdb->prefix."usermeta AS t
                LEFT JOIN (
                    SELECT t2.ID, t2.user_email
                        FROM ".$this->dbname.".".$this->wpdb->prefix."users AS t2
                ) AS t1
                ON t.user_id = t1.ID",
            ARRAY_A
        );

        $result = [];

        if (!empty($select) && is_array($select)) {

            foreach ($select as $values) {

                $result[$values['user_id']]['email'] = $values['user_email'];
                $result[$values['user_id']][$values['meta_key']] = $values['meta_value'];

            }

        }

        return $result;

    }

}
