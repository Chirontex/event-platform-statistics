<?php
/**
 * Event Platform Statistics
 */
namespace EPStatistics\Handlers;

use EPStatistics\Users;
use EPStatistics\Visits;
use EPStatistics\Exceptions\VisitsException;
use EPStatistics\Interfaces\WorksheetHandler;
use PhpOffice\PhpSpreadsheet\Spreadsheet;
use PhpOffice\PhpSpreadsheet\Worksheet\Worksheet;

class Attendance implements WorksheetHandler
{

    protected $users;
    protected $visits;

    public function __construct(Visits $visits, Users $users)
    {
        
        $this->users = $users;
        $this->visits = $visits;

    }

    public function worksheetGet(Spreadsheet $spreadsheet, string $name): Worksheet
    {
        
        if (empty($name)) $name = 'Лист '.$spreadsheet->getSheetCount();

        $worksheet = new Worksheet($spreadsheet, $name);

        $worksheet->setCellValue('A1', 'Адрес');
        $worksheet->setCellValue('B1', 'Дата');
        $worksheet->setCellValue('C1', 'Время');
        $worksheet->setCellValue('D1', 'ID');
        $worksheet->setCellValue('E1', 'ФИО');
        $worksheet->setCellValue('F1', 'E-mail');
        $worksheet->setCellValue('G1', 'Номер телефона');
        $worksheet->setCellValue('H1', 'Дата рождения');
        $worksheet->setCellValue('I1', 'Город');
        $worksheet->setCellValue('J1', 'Организация');
        $worksheet->setCellValue('K1', 'Специальность');

        $i = 2;
        
        $users_data = $this->users->getAllData();

        try {

            $visits_data = $this->visits->getVisits();

            if (!empty($visits_data)) {

                foreach ($visits_data as $visit) {

                    $worksheet->setCellValue('A'.$i, $visit['page_url']);
                    
                    $datetime = date(
                        "d.m.Y H:i:s",
                        strtotime($visit['datetime'])
                    );
                    $datetime = explode(' ', $datetime);

                    $worksheet->setCellValue('B'.$i, $datetime[0]);
                    $worksheet->setCellValue('C'.$i, $datetime[1]);
                    $worksheet->setCellValue('D'.$i, $visit['user_id']);
                    $worksheet->setCellValue(
                        'E'.$i,
                        $users_data[$visit['user_id']]['Surname'].' '.
                        $users_data[$visit['user_id']]['Name'].' '.
                        $users_data[$visit['user_id']]['LastName']
                    );
                    $worksheet->setCellValue(
                        'F'.$i,
                        $users_data[$visit['user_id']]['email']
                    );
                    $worksheet->setCellValue(
                        'G'.$i,
                        $users_data[$visit['user_id']]['phone']
                    );
                    $worksheet->setCellValue(
                        'H'.$i,
                        $users_data[$visit['user_id']]['Date_of_Birth']
                    );
                    $worksheet->setCellValue(
                        'I'.$i,
                        $users_data[$visit['user_id']]['town']
                    );
                    $worksheet->setCellValue(
                        'J'.$i,
                        $users_data[$visit['user_id']]['Organization']
                    );
                    $worksheet->setCellValue(
                        'K'.$i,
                        $users_data[$visit['user_id']]['Specialty']
                    );

                    $i += 1;

                }

            }

        } catch (VisitsException $e) {}

        return $worksheet;

    }

}
