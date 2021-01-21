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

}
