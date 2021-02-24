<?php
/**
 * Event Platform Statistics
 */
namespace EPStatistics;

use EPStatistics\PresenceTimes;
use wpdb;

class Users extends Storage
{

    protected $wpdb;

    protected $presence_times;

    public function __construct(wpdb $wpdb)
    {
        
        $this->wpdb = $wpdb;

        $this->presence_times = new PresenceTimes($this->wpdb);

    }

    /**
     * Return all users data.
     * 
     * @return array
     */
    public function getAllData() : array
    {

        $select = $this->wpdb->get_results(
            "SELECT t.user_id, t.meta_key, t.meta_value, t1.user_email
                FROM `".$this->wpdb->prefix."usermeta` AS t
                LEFT JOIN (
                    SELECT t2.ID, t2.user_email
                        FROM `".$this->wpdb->prefix."users` AS t2
                ) AS t1
                ON t.user_id = t1.ID",
            ARRAY_A
        );

        $result = [];

        if (!empty($select) && is_array($select)) {

            $presence = $this->presence_times->getOrderedByUsers();

            foreach ($select as $values) {

                if (!isset(
                    $result[$values['user_id']]['email']
                )) $result[$values['user_id']]['email'] = $values['user_email'];

                if (!isset(
                        $result[$values['user_id']]['presence_times']
                    ) &&
                    isset(
                        $presence[$values['user_id']]
                    )
                ) $result[$values['user_id']]['presence_times'] = $presence[$values['user_id']];

                $result[$values['user_id']][$values['meta_key']] = $values['meta_value'];

            }

        }

        return $result;

    }

    /**
     * Return users IDs grouped by towns.
     * 
     * @return array
     */
    public function getUsersTowns() : array
    {

        $result = [];

        $metadata_matching = new MetadataMatching($this->wpdb);

        $matches = $metadata_matching->getMatchByName('Город');
        $matches = empty($matches) ?
            $metadata_matching->getMatchByName('город') : $matches;

        $key = '';

        foreach ($matches as $match) {

            if ((int)$match['include'] === 1) {

                $key = $match['key'];
                
                break;

            }

        }

        $select = $this->wpdb->get_results(
            "SELECT t.user_id, t.meta_value
                FROM `".$this->wpdb->prefix."usermeta` AS t
                WHERE t.meta_key = '".$key."'",
            ARRAY_A
        );

        if (is_array($select) && !empty($select)) {

            foreach ($select as $values) {

                $town = trim($values['meta_value']);

                if (!empty($town)) $result[$town][] = $values['user_id'];

            }

        }

        return $result;

    }

    /**
     * Return users IDs grouped by countries and towns.
     * 
     * @return array
     */
    public function getUsersCountries() : array
    {

        $result = [];

        $metadata_matching = new MetadataMatching($this->wpdb);

        $matches = $metadata_matching->getMatchByName('Страна');
        $matches = empty($matches) ?
            $metadata_matching->getMatchByName('страна') : $matches;

        $country_key = '';

        foreach ($matches as $match) {

            if ((int)$match['include'] === 1) {

                $country_key = $match['key'];

                break;

            }

        }

        if (!empty($country_key)) {

            $matches = $metadata_matching->getMatchByName('Город');
            $matches = empty($matches) ?
                $metadata_matching->getMatchByName('город') : $matches;

            $city_key = '';

            foreach ($matches as $match) {

                if ((int)$match['include'] === 1) {

                    $city_key = $match['key'];

                    break;

                }

            }

            $select = $this->wpdb->get_results(
                "SELECT t.user_id, t.meta_value AS country, t1.meta_value AS city
                    FROM `".$this->wpdb->prefix."usermeta` AS t
                    LEFT JOIN `".$this->wpdb->prefix."usermeta` AS t1
                    ON t.user_id = t1.user_id
                    WHERE t.meta_key = '".$country_key."'
                    AND t1.meta_key = '".$city_key."'",
                ARRAY_A
            );

            if (is_array($select) && !empty($select)) {

                foreach ($select as $values) {

                    $country = trim($values['country']);

                    $city = trim($values['city']);

                    if (!empty($country) &&
                        !empty($city)) $result[$country][$city][] = $values['user_id'];

                }

            }

        }

        return $result;

    }

}
