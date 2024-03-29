<?php
/**
 * @package Event Platform Statistics
 */
namespace EPStatistics;

use EPStatistics\Exceptions\TitlesException;

/**
 * Titles storage.
 * @since 1.9.11
 */
class Titles extends Storage
{

    protected $table = 'epstatistics_titles';

    protected $fields = [
        'title' => 'TEXT NOT NULL',
        'list_name' => 'TEXT NOT NULL',
        'datetime_start' => 'DATETIME NOT NULL',
        'datetime_end' => 'DATETIME NOT NULL',
        'nmo' => 'TINYINT UNSIGNED NOT NULL DEFAULT 0'
    ];

    /**
     * Add a title.
     * @since 1.9.11
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
     * @throws EPStatistics\Exceptions\TitlesException
     */
    public function titleAdd(string $title, string $list_name, int $timestamp_start, int $timestamp_end, int $nmo = 0) : bool
    {

        date_default_timezone_set('Europe/Moscow');

        if (empty($title)) throw new TitlesException(
            TitlesException::EMPTY_TITLE_MESSAGE,
            TitlesException::EMPTY_TITLE_CODE
        );

        if (empty($list_name)) throw new TitlesException(
            TitlesException::EMPTY_LIST_NAME_MESSAGE,
            TitlesException::EMPTY_LIST_NAME_CODE
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
     * Update a title.
     * @since 1.9.11
     * 
     * @param int $id
     * ID cannot be lesser than 1.
     * 
     * @param string $title
     * Title cannot be empty.
     * 
     * @param string $list_name
     * List name cannot be empty.
     * 
     * @param int $timestamp_start
     * 
     * @param int $timestamp_end
     * 
     * @param int $nmo
     * 
     * @return bool
     * 
     * @throws EPStatistics\Exceptions\TitlesException
     */
    public function titleUpdate(int $id, string $title, string $list_name, int $timestamp_start, int $timestamp_end, int $nmo = 0) : bool
    {

        date_default_timezone_set('Europe/Moscow');

        if ($id < 1) throw new TitlesException(
            TitlesException::INVALID_ID_MESSAGE,
            TitlesException::INVALID_ID_CODE
        );

        if (empty($title)) throw new TitlesException(
            TitlesException::EMPTY_TITLE_MESSAGE,
            TitlesException::EMPTY_TITLE_CODE
        );

        if (empty($list_name)) throw new TitlesException(
            TitlesException::EMPTY_LIST_NAME_MESSAGE,
            TitlesException::EMPTY_LIST_NAME_CODE
        );

        if ($nmo < 0 || $nmo > 1) $nmo = 0;

        if ($this->wpdb->update(
            $this->wpdb->prefix.$this->table,
            [
                'title' => $title,
                'list_name' => $list_name,
                'datetime_start' => date("Y-m-d H:i:s", $timestamp_start),
                'datetime_end' => date("Y-m-d H:i:s", $timestamp_end),
                'nmo' => $nmo
            ],
            ['id' => $id],
            ['%s', '%s', '%s', '%s', '%d'],
            '%d'
        ) === false) return false;
        else return true;

    }

    /**
     * Return all titles.
     * @since 1.9.11
     * 
     * @param string $list_name
     * 
     * @param bool $actual
     * 
     * @return array
     * 
     * @throws EPStatistics\Exceptions\TitlesException
     */
    public function selectTitles(string $list_name = '', bool $actual = false) : array
    {

        date_default_timezone_set('Europe/Moscow');

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
                FROM `".$this->wpdb->prefix.$this->table."`".$where,
            ARRAY_A
        );

        if (is_array($select)) return $select;
        else throw new TitlesException(
            TitlesException::SELECT_TITLES_FAILURE_MESSAGE,
            TitlesException::SELECT_TITLES_FAILURE_CODE
        );

    }

    /**
     * Delete a title by its ID.
     * @since 1.9.11
     * 
     * @param int $id
     * ID cannot be lesser than 1.
     * 
     * @return bool
     * 
     * @throws EPStatistics\Exceptions\TitlesException
     */
    public function titleDelete(int $id) : bool
    {

        if ($id < 1) throw new TitlesException(
            TitlesException::INVALID_ID_MESSAGE,
            TitlesException::INVALID_ID_CODE
        );

        $delete = $this->wpdb->delete(
            $this->wpdb->prefix.$this->table,
            ['id' => $id],
            ['%d']
        );

        return !empty($delete);

    }

}
