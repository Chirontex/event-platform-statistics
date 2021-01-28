<?php
/**
 * Event Platform Statistics
 */
namespace EPStatistics\Handlers;

use EPStatistics\Users;
use EPStatistics\Tokens;
use EPStatistics\Titles;
use EPStatistics\PresenceTimes;
use EPStatistics\Exceptions\PresenceTimesException;
use EPStatistics\Exceptions\TitlesException;
use EPStatistics\Interfaces\WorksheetHandler;
use PhpOffice\PhpSpreadsheet\Spreadsheet;
use PhpOffice\PhpSpreadsheet\Worksheet\Worksheet;

class PresenceEffect implements WorksheetHandler
{

    protected $presence_times;

    const WORKSHEET_MODE_RAW = 'raw';
    const WORKSHEET_MODE_TITLES = 'titles';

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

    public function worksheetGet(Spreadsheet $spreadsheet, string $name, string $mode = 'raw') : Worksheet
    {

        if (empty($name)) $name = 'Лист '.$spreadsheet->getSheetCount();

        if ($mode !== 'raw' && $mode !== 'titles') $mode = 'raw';

        $worksheet = new Worksheet($spreadsheet, $name);

        $users = new Users($this->presence_times->wpdbGet());

        $users_data = $users->getAllData();

        if (!empty($users_data)) {

            switch ($mode) {

                case 'raw':

                    $worksheet->setCellValue('A1', 'ID');
                    $worksheet->setCellValue('B1', 'ФИО');
                    $worksheet->setCellValue('C1', 'Дата и время подтверждения');

                    $i = 2;

                    foreach ($users_data as $user_id => $values) {

                        if (isset($values['presence_times'])) {

                            foreach ($values['presence_times'] as $datetime) {

                                $worksheet->setCellValue('A'.$i, $user_id);
                                $worksheet->setCellValue(
                                    'B'.$i,
                                    $values['Surname'].' '.$values['Name'].' '.$values['LastName']
                                );
                                $worksheet->setCellValue('C'.$i, $datetime);

                                $i += 1;

                            }

                        }

                    }

                    break;

                case 'titles':

                    $titles = new Titles($this->presence_times->wpdbGet());

                    try {

                        $titles_selected = $titles->selectTitles();

                        $row = 1;

                        $worksheet->setCellValue('A'.$row, 'ID пользователя');
                        $worksheet->setCellValue('B'.$row, 'ФИО');
                        $worksheet->setCellValue('C'.$row, 'Всего подтверждений');

                        $col = 4;

                        foreach ($titles_selected as $title) {

                            if ($title['nmo'] !== '1') continue;

                            $worksheet->setCellValue(
                                $this->getColumnName($col).$row,
                                $title['id']
                            );

                            $col += 1;

                        }

                        $row += 1;

                        foreach ($users_data as $user_id => $values) {

                            $worksheet->setCellValue('A'.$row, $user_id);
                            $worksheet->setCellValue(
                                'B'.$row,
                                $values['Surname'].' '.$values['Name'].' '.$values['LastName']
                            );

                            $col = 4;

                            $confs_total = 0;

                            foreach ($titles_selected as $title) {

                                if ($title['nmo'] !== '1') continue;

                                $confs = 0;

                                if (isset($values['presence_times'])) {

                                    foreach ($values['presence_times'] as $datetime) {

                                        $datetime = strtotime($datetime);

                                        if ($datetime >= strtotime($title['datetime_start']) &&
                                            $datetime <= strtotime($title['datetime_end'])) {

                                            $confs += 1;
                                            $confs_total += 1;

                                        }

                                    }

                                }

                                $worksheet->setCellValue(
                                    $this->getColumnName($col).$row,
                                    $confs
                                );

                                $col += 1;

                            }

                            $worksheet->setCellValue('C'.$row, $confs_total);

                            $row += 1;

                        }

                    } catch (TitlesException $e) {}

                    break;

            }

        }

        return $worksheet;

    }

    /**
     * Calculates a column name by it's periodic number.
     * 
     * @param int $number
     * If $number lesser than 1 or bigger than 650,
     * the method will return an empty string.
     * 
     * @return string
     */
    protected function getColumnName(int $number) : string
    {

        $name = '';

        if ($number > 0) {

            $alphabet = range('A', 'Z');

            if ($number <= count($alphabet)) $name = $alphabet[$number - 1];
            else {

                $fi = 0;

                $dif = $number - count($alphabet);

                while ($dif > count($alphabet)) {

                    $fi += 1;

                    $dif = $dif - count($alphabet);

                }

                if ($fi <= count($alphabet)) {

                    $name .= $alphabet[$fi];
                    $name .= $alphabet[$dif - 1];

                }

            }

        }

        return $name;

    }

}
