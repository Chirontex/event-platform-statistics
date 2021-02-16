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

        if (isset($_REQUEST['list'])) {

            $tokens = new Tokens($this->presence_times->wpdbGet());

            $user_id = $tokens->userGetByToken($_COOKIE['eps_api_token']);

            try {

                $add = $this->presence_times->add($user_id, trim($_REQUEST['list']));

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

        } else $result = [
            'code' => -99,
            'message' => 'Too few arguments for this request.'
        ];

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
                    $worksheet->setCellValue('C1', 'E-mail');
                    $worksheet->setCellValue('D1', 'Номер телефона');
                    $worksheet->setCellValue('E1', 'Дата рождения');
                    $worksheet->setCellValue('F1', 'Город');
                    $worksheet->setCellValue('G1', 'Организация');
                    $worksheet->setCellValue('H1', 'Специальность');
                    $worksheet->setCellValue('I1', 'Зал');
                    $worksheet->setCellValue('J1', 'Дата и время подтверждения');

                    $i = 2;

                    foreach ($users_data as $user_id => $values) {

                        if (isset($values['presence_times'])) {

                            foreach ($values['presence_times'] as $datetime) {

                                $worksheet->setCellValue('A'.$i, $user_id);
                                $worksheet->setCellValue(
                                    'B'.$i,
                                    $values['Surname'].' '.$values['Name'].' '.$values['LastName']
                                );
                                $worksheet->setCellValue('C'.$i, $values['email']);
                                $worksheet->setCellValue('D'.$i, $values['phone']);
                                $worksheet->setCellValue('E'.$i, $values['Date_of_Birth']);
                                $worksheet->setCellValue('F'.$i, $values['town']);
                                $worksheet->setCellValue('G'.$i, $values['Organization']);
                                $worksheet->setCellValue('H'.$i, $values['Specialty']);
                                $worksheet->setCellValue('I'.$i, $datetime['list']);
                                $worksheet->setCellValue('J'.$i, $datetime['datetime']);

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
                        $worksheet->setCellValue('C'.$row, 'E-mail');
                        $worksheet->setCellValue('D'.$row, 'Номер телефона');
                        $worksheet->setCellValue('E'.$row, 'Дата рождения');
                        $worksheet->setCellValue('F'.$row, 'Город');
                        $worksheet->setCellValue('G'.$row, 'Организация');
                        $worksheet->setCellValue('H'.$row, 'Специальность');
                        $worksheet->setCellValue('I'.$row, 'Всего релевантных подтверждений');

                        $col_base = 10;
                        $col = $col_base;

                        foreach ($titles_selected as $title) {

                            if ($title['nmo'] !== '1') continue;

                            $worksheet->setCellValue(
                                $this->getColumnName($col).$row,
                                'Лекция ID '.$title['id']
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
                            $worksheet->setCellValue('C'.$row, $values['email']);
                            $worksheet->setCellValue('D'.$row, $values['phone']);
                            $worksheet->setCellValue('E'.$row, $values['Date_of_Birth']);
                            $worksheet->setCellValue('F'.$row, $values['town']);
                            $worksheet->setCellValue('G'.$row, $values['Organization']);
                            $worksheet->setCellValue('H'.$row, $values['Specialty']);

                            $col = $col_base;

                            $confs_total = 0;

                            foreach ($titles_selected as $title) {

                                if ($title['nmo'] !== '1') continue;

                                $confs = 0;

                                if (isset($values['presence_times'])) {

                                    foreach ($values['presence_times'] as $presence_time) {

                                        $datetime = strtotime($presence_time['datetime']);

                                        if (($datetime >= strtotime($title['datetime_start']) &&
                                                $datetime <= strtotime($title['datetime_end'])) &&
                                            $presence_time['list'] === $title['list_name']) {

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

                            $worksheet->setCellValue(
                                $this->getColumnName($col_base - 1).$row,
                                $confs_total
                            );

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
