<?php
/**
 * Event Platform Statistics
 */
namespace EPStatistics;

use EPStatistics\Exceptions\TitlesException;
use wpdb;

class Titles
{

    protected $wpdb;
    protected $dbname;
    protected $table;

    public function __construct(wpdb $wpdb, string $dbname = '')
    {
        
        $this->wpdb = $wpdb;

        if (empty($dbname)) $this->dbname = DB_NAME;
        else $this->dbname = $dbname;

        $this->table = 'epstatistics_titles';

        $this->tableCreate();

    }

    /**
     * Create a titles table.
     * 
     * @return void
     * 
     * @throws TitlesException
     */
    public function tableCreate() : void
    {

        if ($this->wpdb->query(
                "CREATE TABLE IF NOT EXISTS `".$this->wpdb->prefix.$this->table."` (
                    `id` BIGINT UNSIGNED NOT NULL AUTO_INCREMENT,
                    `title` TEXT NOT NULL,
                    `list_name` TEXT NOT NULL,
                    `datetime_start` DATETIME NOT NULL,
                    `datetime_end` DATETIME NOT NULL,
                    `nmo` TINYINT UNSIGNED NOT NULL DEFAULT 0,
                    PRIMARY KEY (`id`)
                )
                COLLATE='utf8mb4_unicode_ci'
                AUTO_INCREMENT=0"
            ) === false) throw new TitlesException(
                'Creating table failure.',
                -50
            );

    }

    /**
     * Add a title.
     * 
     * @param string $title
     * Title cannot be empty.
     * 
     * @param int $timestamp_start
     * 
     * @param int $timestamp_end
     * 
     * @param int $nmo
     * If $nmo lesser than 0 or bigger than 1, it will be equal 0.
     * 
     * @return bool
     * 
     * @throws TitlesException
     */
    public function titleAdd(string $title, string $list_name, int $timestamp_start, int $timestamp_end, int $nmo = 0) : bool
    {

        if (empty($title)) throw new TitlesException(
            'Title cannot be empty.',
            -51
        );

        if (empty($list_name)) throw new TitlesException(
            'List name cannot be empty.',
            -54
        );

        if ($nmo < 0 || $nmo > 1) $nmo = 0;

        if ($this->wpdb->insert(
            $this->wpdb->prefix.$this->table,
            [
                'title' => $title,
                'list_name' => $list_name,
                'datetime_start' => date("Y-m-d H:i:s", $timestamp_start),
                'datetime_end' => date("Y-m-d H:i:s", $timestamp_end),
                'nmo' => $nmo
            ],
            ['%s', '%s', '%s', '%s', '%d']
        ) === false) return false;
        else return true;

    }

    /**
     * Return all titles.
     * 
     * @param string $list_name
     * 
     * @param bool $actual
     * 
     * @return array
     * 
     * @throws TitlesException
     */
    public function selectTitles(string $list_name = '', bool $actual = false) : array
    {

        $where = "";

        if (!empty($list_name)) $where = $this->wpdb->prepare(" AS t WHERE t.list_name = %s", $list_name);

        if ($actual) {

            date_default_timezone_set('Europe/Moscow');

            $now = date("Y-m-d H:i:s");

            $where .= empty($where) ?
            " AS t WHERE " :
            " AND ";

            $where .= "t.datetime_start < '".$now."' AND t.datetime_end > '".$now."'";

        }

        $select = $this->wpdb->get_results(
            "SELECT *
                FROM ".$this->dbname.".".$this->wpdb->prefix.$this->table.$where,
            ARRAY_A
        );

        if (is_array($select)) return $select;
        else throw new TitlesException(
            'Selecting titles from DB failure.',
            -52
        );

    }

    /**
     * Delete a title by its ID.
     * 
     * @param int $id
     * ID cannot be lesser than 1.
     * 
     * @return bool
     * 
     * @throws TitlesException
     */
    public function titleDelete(int $id) : bool
    {

        if ($id < 1) throw new TitlesException(
            'ID cannot be lesser than 1.',
            -53
        );

        $delete = $this->wpdb->delete(
            $this->wpdb->prefix.$this->table,
            ['id' => $id],
            ['%d']
        );

        return !empty($delete);

    }

}
