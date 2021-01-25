<?php
/**
 * Event Platform Statistics
 */
namespace EPStatistics\Handlers;

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

        $user_id = get_current_user_id();

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

        // some logic

        return $worksheet;

    }

}
