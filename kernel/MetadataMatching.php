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
            'key' => 'CHAR(64) NOT NULL'
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

        if ($this->wpdb->insert(
            $this->wpdb->prefix.$this->table,
            [
                'name' => $name,
                'key' => $key
            ],
            ['%s', '%s']
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
     * @return void
     * 
     * @throws EPStatistics\Exceptions\MetadataMatchingException
     */
    public function matchUpdate(int $id, string $name, string $key) : void
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

        if ($this->wpdb->update(
            $this->wpdb->prefix.$this->table,
            [
                'name' => $name,
                'key' => $key
            ],
            ['id' => $id],
            ['%s', '%s'],
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
