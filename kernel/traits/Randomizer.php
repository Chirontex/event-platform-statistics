<?php
/**
 * Event Platform Statistics
 */
namespace EPStatistics\Traits;

trait Randomizer
{

    /**
     * Generage a random string of digits and letters.
     * 
     * @param int $length
     * 
     * @return string
     */
    protected function generateRandomString(int $length = 32) : string
    {

        if ($length < 1) $length = 32;

        $result = '';

        $arr = array_merge(range(0, 9), range('a', 'z'));

        for ($i = 1; $i <= $length; $i++) {

            $result .= $arr[rand(0, (count($arr) - 1))];

        }

        return $result;

    }

    /**
     * Generate a random attendance time.
     * 
     * @param int $max_hour
     * Optional. If $max_hour < 0, it will be equal 0.
     * 
     * @return string
     * Time in H:i:s format.
     */
    protected function generateRandomAT(int $max_hour = 3) : string
    {

        $result = '';

        if ($max_hour < 0) $max_hour = 0;

        if ($max_hour === 0) $hour = '00';
        else {

            $hour = rand(0, $max_hour);

            if ($hour < 10) $hour = '0'.$hour;

        }

        $minute = rand(0, 59);

        if ($minute < 10) $minute = '0'.$minute;

        $second = rand(0, 59);

        if ($second < 10) $second = '0'.$second;

        $result = implode(
            ':',
            [
                (string)$hour,
                (string)$minute,
                (string)$second
            ]
        );

        return $result;

    }

}
