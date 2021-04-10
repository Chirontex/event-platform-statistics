<?php
/**
 * @package Event Platform Statistics
 */
namespace EPStatistics;

use EPStatistics\PresenceTimes;
use wpdb;

/**
 * Users DB table access class.
 * @since 1.9.11
 */
class Users extends Storage
{

    /**
     * @var PresenceTimes $presence_times
     * Presence times storage class.
     */
    protected $presence_times;

    public function __construct(wpdb $wpdb)
    {

        $this->presence_times = new PresenceTimes($wpdb);

        parent::__construct($wpdb);

    }

    /**
     * Return all users data.
     * @since 1.9.11
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
     * @since 1.9.11
     * 
     * @return array
     */
    public function getUsersTowns() : array
    {

        $result = [];

        $key = $this->getFirstKey(['Город', 'город']);

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
     * @since 1.9.11
     * 
     * @return array
     */
    public function getUsersCountries() : array
    {

        $result = [];

        $country_key = $this->getFirstKey(['Страна', 'страна']);
        $country_key = empty($country_key) ?
            "''" : $this->wpdb->prepare("%s", $country_key);

        $region_key = $this->getFirstKey([
            'Регион', 'регион',
            'Субъект', 'субъект',
            'Субъект РФ', 'субъект РФ',
            'Область', 'область'
        ]);
        $region_key = empty($region_key) ?
            "''" : $this->wpdb->prepare("%s", $region_key);

        $city_key = $this->getFirstKey(['Город', 'город']);
        $city_key = empty($city_key) ?
            "''" : $this->wpdb->prepare("%s", $city_key);

        $select = $this->wpdb->get_results(
            "SELECT t.user_id, t.meta_value AS country,
                    t1.meta_value AS region, t2.meta_value AS city
                FROM `".$this->wpdb->prefix."usermeta` AS t
                LEFT JOIN `".$this->wpdb->prefix."usermeta` AS t1
                ON t.user_id = t1.user_id
                LEFT JOIN `".$this->wpdb->prefix."usermeta` AS t2
                ON t.user_id = t2.user_id
                WHERE t.meta_key = ".$country_key."
                AND t1.meta_key = ".$region_key."
                AND t2.meta_key = ".$city_key,
            ARRAY_A
        );

        if (is_array($select) && !empty($select)) {

            foreach ($select as $values) {

                $country = trim($values['country']);

                $region = trim($values['region']);

                $city = trim($values['city']);

                if (!empty($country) &&
                    !empty($region) &&
                    !empty($city)) $result[$country][$region][$city][] = $values['user_id'];

            }

        }

        return $result;

    }

    /**
     * Handle MetadataMatching::getMatchByName() results
     * to get the first key.
     * @since 1.9.11
     * 
     * @param array $names
     * 
     * @return string
     */
    protected function getFirstKey(array $names) : string
    {

        $result = '';

        $metadata_matching = new MetadataMatching($this->wpdb);

        $matches = [];

        foreach ($names as $name) {

            if (empty(
                $matches
            )) $matches = $metadata_matching->getMatchByName((string)$name);
            else break;

        }

        foreach ($matches as $match) {

            if ((int)$match['include'] === 1) {

                $result = $match['key'];

                break;

            }

        }

        return $result;

    }

}
