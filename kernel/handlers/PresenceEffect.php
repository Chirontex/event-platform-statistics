<?php
/**
 * Event Platform Statistics
 */
namespace EPStatistics\Handlers;

use EPStatistics\Tokens;
use EPStatistics\PresenceTimes;
use EPStatistics\Exceptions\PresenceTimesException;
use EPStatistics\Interfaces\WorksheetHandler;
use PhpOffice\PhpSpreadsheet\Spreadsheet;
use PhpOffice\PhpSpreadsheet\Worksheet\Worksheet;

class PresenceEffect implements WorksheetHandler
{

    protected $presence_times;

    public function __construct(PresenceTimes $presence_times)
    {
        
        $this->presence_times = $presence_times;

    }

    /**
     * Implementation of /event-platform-statistics/v1/presence-time/add route.
     * 
     * @return array
     */
    public function apiAddPresenceTime() : array
    {

        $result = [];

        $tokens = new Tokens($this->presence_times->wpdbGet());

        $user_id = $tokens->userGetByToken($_COOKIE['eps_api_token']);

        try {

            $add = $this->presence_times->add($user_id);

        } catch (PresenceTimesException $e) {

            $result = [
                'code' => $e->getCode(),
                'message' => $e->getMessage()
            ];

        }

        if (empty($result)) {

            if ($add) $result = [
                'code' => 0,
                'message' => 'Success.'
            ];
            else $result = [
                'code' => -33,
                'message' => 'Presence time adding to database failure.'
            ];

        }

        return $result;

    }

    public function worksheetGet(Spreadsheet $spreadsheet, string $name) : Worksheet
    {

        if (empty($name)) $name = 'Лист '.$spreadsheet->getSheetCount();

        $worksheet = new Worksheet($spreadsheet, $name);

        $confirmations = $this->presence_times->getOrderedByUsers();

        if (!empty($times)) {

            $worksheet->setCellValue('A1', 'ID пользователя');
            $worksheet->setCellValue('B1', 'Другие');

            $i = 2;

            foreach ($confirmations as $user_id => $times) {

                $worksheet->setCellValue('A'.$i, $user_id);

                $time_str = '';
                
                foreach ($times as $time) {

                    $time_str .= $time.'; ';

                }

                $worksheet->setCellValue('B'.$i, $time_str);

                $i += 1;

            }

        }

        return $worksheet;

    }

}
