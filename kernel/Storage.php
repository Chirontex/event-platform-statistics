<?php
/**
 * Event Platform Statistics
 */
namespace EPStatistics;

use EPStatistics\Exceptions\StorageException;
use wpdb;

abstract class Storage
{

    protected $wpdb;
    protected $table;
    protected $fields;
    protected $indexes;

    public function __construct(wpdb $wpdb)
    {
        
        $this->wpdb = $wpdb;

        if (!is_array($this->fields)) $this->fields = [];

        if (!is_array($this->indexes)) $this->indexes = [];

        $this->createTable();

    }

    /**
     * Create a table.
     * 
     * @return void
     * 
     * @throws EPStatistics\Exceptions\StorageException
     */
    public function createTable() : void
    {

        if (empty($this->fields)) $fields = '';
        else {

            $fields = [];

            foreach ($this->fields as $key => $params) {

                $fields[] = "`".$key."` ".$params;

            }

            $fields = ", ".implode(", ", $fields);

        }

        if (empty($this->indexes)) $indexes = '';
        else {

            $indexes = [];

            foreach ($this->indexes as $key => $params) {

                $indexes[] = $params." `".$key."` (`".$key."`)";

            }

            $indexes = ", ".implode(", ", $indexes);

        }

        if ($this->wpdb->query(
            "CREATE TABLE IF NOT EXISTS `".$this->wpdb->prefix.$this->table."` (
                `id` BIGINT UNSIGNED NOT NULL AUTO_INCREMENT".$fields.",
                PRIMARY KEY (`id`)".$indexes."
            )
            COLLATE='utf8mb4_unicode_ci'
            AUTO_INCREMENT=0"
        ) === false) throw new StorageException(
            StorageException::CREATE_TABLE_FAILURE_MESSAGE.
                ' ('.$this->table.')',
            StorageException::CREATE_TABLE_FAILURE_CODE
        );

    }

}
