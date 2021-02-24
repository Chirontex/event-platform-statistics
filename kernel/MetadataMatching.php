<?php
/**
 * Event Platform Statistics
 */
namespace EPStatistics;

use EPStatistics\Exceptions\MetadataMatchingException;
use wpdb;

class MetadataMatching extends Storage
{

    public function __construct(wpdb $wpdb)
    {

        $this->table = 'epstatistics_metadata_matching';

        $this->fields = [
            'name' => 'CHAR(64) NOT NULL',
            'key' => 'CHAR(64) NOT NULL',
            'periodic_number' => 'BIGINT NOT NULL',
            'include' => 'TINYINT UNSIGNED NOT NULL DEFAULT 1'
        ];
        
        parent::__construct($wpdb);

    }

    /**
     * Add a new match.
     * 
     * @param string $name
     * Metadata name to display in file.
     * Name cannot be empty.
     * 
     * @param string $key
     * Metadata table key. Cannot be empty.
     * 
     * @return void
     * 
     * @throws EPStatistics\Exceptions\MetadataMatchingException
     */
    public function matchAdd(string $name, string $key) : void
    {

        if (empty($name)) throw new MetadataMatchingException(
            MetadataMatchingException::EMPTY_NAME_MESSAGE,
            MetadataMatchingException::EMPTY_NAME_CODE
        );

        if (empty($key)) throw new MetadataMatchingException(
            MetadataMatchingException::EMPTY_KEY_MESSAGE,
            MetadataMatchingException::EMPTY_KEY_CODE
        );

        $select = $this->matchesAll('DESC');

        if (empty($select)) $periodic_number = 0;
        else {

            $periodic_number = $select[0]['periodic_number'] + 1;

            if ($periodic_number > 650) $periodic_number = 650;

        }

        if ($this->wpdb->insert(
            $this->wpdb->prefix.$this->table,
            [
                'name' => $name,
                'key' => $key,
                'periodic_number' => $periodic_number,
                'include' => 1
            ],
            ['%s', '%s', '%d', '%d']
        ) === false) throw new MetadataMatchingException(
            MetadataMatchingException::MATCH_INSERT_FAILURE_MESSAGE,
            MetadataMatchingException::MATCH_INSERT_FAILURE_CODE
        );

    }

    /**
     * Update an existing match.
     * 
     * @param int $id
     * Match ID. Cannot be lesser than 1.
     * 
     * @param string $name
     * Metadata name to display in file.
     * Name cannot be empty.
     * 
     * @param string $key
     * Metadata table key. Cannot be empty.
     * 
     * @param int $periodic_number
     * May be from -650 to 650.
     * 
     * @param int $include
     * May be 0 or 1. Default value is 1.
     * 
     * @return void
     * 
     * @throws EPStatistics\Exceptions\MetadataMatchingException
     */
    public function matchUpdate(int $id, string $name, string $key, int $periodic_number, int $include) : void
    {

        if ($id < 1) throw new MetadataMatchingException(
            MetadataMatchingException::MATCH_INVALID_ID_MESSAGE,
            MetadataMatchingException::MATCH_INVALID_ID_CODE
        );

        if (empty($name)) throw new MetadataMatchingException(
            MetadataMatchingException::EMPTY_NAME_MESSAGE,
            MetadataMatchingException::EMPTY_NAME_CODE
        );

        if (empty($key)) throw new MetadataMatchingException(
            MetadataMatchingException::EMPTY_KEY_MESSAGE,
            MetadataMatchingException::EMPTY_KEY_CODE
        );

        if ($periodic_number > 650) $periodic_number = 650;
        elseif ($periodic_number < -650) $periodic_number = -650;

        if ($include !== 0 && $include !== 1) $include = 1;

        if ($this->wpdb->update(
            $this->wpdb->prefix.$this->table,
            [
                'name' => $name,
                'key' => $key,
                'periodic_number' => $periodic_number,
                'include' => $include
            ],
            ['id' => $id],
            ['%s', '%s', '%d', '%d'],
            ['%d']
        ) === false) throw new MetadataMatchingException(
            MetadataMatchingException::MATCH_UPDATE_FAILURE_MESSAGE,
            MetadataMatchingException::MATCH_UPDATE_FAILURE_CODE
        );

    }

    /**
     * Delete the match.
     * 
     * @param int $id
     * Match ID. Cannot be lesser than 1.
     * 
     * @return void
     * 
     * @throws EPStatistics\Exceptions\MetadataMatchingException
     */
    public function matchDelete(int $id) : void
    {

        if ($id < 1) throw new MetadataMatchingException(
            MetadataMatchingException::MATCH_INVALID_ID_MESSAGE,
            MetadataMatchingException::MATCH_INVALID_ID_CODE
        );
        
        $delete = $this->wpdb->delete(
            $this->wpdb->prefix.$this->table,
            ['id' => $id],
            ['%d']
        );

        if ($delete === 0 ||
            $delete === false) throw new MetadataMatchingException(
                MetadataMatchingException::MATCH_DELETE_FAILURE_MESSAGE,
                MetadataMatchingException::MATCH_DELETE_FAILURE_CODE
            );

    }

    /**
     * Get all existing matches.
     * 
     * @param string $pn_order
     * Periodic numbers order mode, ASC or DESC.
     * 
     * @param bool $includes_only
     * If true, the method will return matches with enable including only.
     * 
     * @return array
     * 
     * @throws EPStatistics\Exceptions\MetadataMatchingException
     */
    public function matchesAll(string $pn_order = 'ASC', bool $includes_only = false) : array
    {

        if ($pn_order === 'asc') $pn_order = 'ASC';
        elseif ($pn_order === 'desc') $pn_order = 'DESC';
        else if ($pn_order !== 'ASC' &&
                $pn_order !== 'DESC') $pn_order = 'ASC';

        if ($includes_only) $where = " WHERE t.include = '1' ";
        else $where = "";

        $select = $this->wpdb->get_results(
            "SELECT *
                FROM `".$this->wpdb->prefix.$this->table."` AS t".$where."
                ORDER BY t.periodic_number ".$pn_order,
            ARRAY_A
        );

        if (is_array($select)) return $select;
        else throw new MetadataMatchingException(
            MetadataMatchingException::MATCHES_SELECT_FAILURE_MESSAGE,
            MetadataMatchingException::MATCHES_SELECT_FAILURE_CODE
        );

    }

    /**
     * Get match by name.
     * 
     * @param string $name
     * The name cannot be empty.
     * 
     * @return array
     * 
     * @throws EPStatistics\Exceptions\MetadataMatchingException
     */
    public function getMatchByName(string $name) : array
    {

        if (empty($name)) throw new MetadataMatchingException(
            MetadataMatchingException::EMPTY_NAME_MESSAGE,
            MetadataMatchingException::EMPTY_NAME_CODE
        );

        $select = $this->wpdb->get_results(
            $this->wpdb->prepare(
                "SELECT *
                    FROM `".$this->wpdb->prefix.$this->table."` AS t
                    WHERE t.name = %s",
                $name
            ),
            ARRAY_A
        );

        if (!is_array($select)) throw new MetadataMatchingException(
            MetadataMatchingException::MATCHES_SELECT_FAILURE_MESSAGE,
            MetadataMatchingException::MATCHES_SELECT_FAILURE_CODE
        );

        return $select;

    }

    /**
     * Get match by key.
     * 
     * @param string $key
     * The key cannot be empty.
     * 
     * @return array
     * 
     * @throws EPStatistics\Exceptions\MetadataMatchingException
     */
    public function getMatchByKey(string $key) : array
    {

        if (empty($key)) throw new MetadataMatchingException(
            MetadataMatchingException::EMPTY_KEY_MESSAGE,
            MetadataMatchingException::EMPTY_KEY_CODE
        );

        $select = $this->wpdb->get_results(
            $this->wpdb->prepare(
                "SELECT *
                    FROM `".$this->wpdb->prefix.$this->table."` AS t
                    WHERE t.key = %s",
                $key
            ),
            ARRAY_A
        );

        if (!is_array($select)) throw new MetadataMatchingException(
            MetadataMatchingException::MATCHES_SELECT_FAILURE_MESSAGE,
            MetadataMatchingException::MATCHES_SELECT_FAILURE_CODE
        );

        return $select;

    }

    /**
     * Getting all existing keys.
     * 
     * @return array
     * 
     * @throws EPStatistics\Exceptions\MetadataMatchingException
     */
    public function keysAll() : array
    {

        $result = [];

        $keys = $this->wpdb->get_results(
            "SELECT t.meta_key
                FROM `".$this->wpdb->prefix."usermeta` AS t
                GROUP BY t.meta_key",
            ARRAY_A
        );

        if (!is_array($keys)) throw new MetadataMatchingException(
            MetadataMatchingException::KEYS_GETTING_FAILURE_MESSAGE,
            MetadataMatchingException::KEYS_GETTING_FAILURE_CODE
        );

        if (!empty($keys)) {

            foreach ($keys as $values) {

                $result[] = $values['meta_key'];

            }

        }

        return $result;

    }

}
