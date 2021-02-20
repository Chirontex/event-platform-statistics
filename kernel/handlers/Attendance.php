<?php
/**
 * Event Platform Statistics
 */
namespace EPStatistics\Handlers;

use EPStatistics\Users;
use EPStatistics\Visits;
use PhpOffice\PhpSpreadsheet\Spreadsheet;
use PhpOffice\PhpSpreadsheet\Worksheet\Worksheet;

class Attendance extends UsersWorksheetHandler
{

    protected $users;
    protected $visits;

    public function __construct(Visits $visits, Users $users)
    {
        
        $this->users = $users;
        $this->visits = $visits;

    }

    public function worksheetGet(Spreadsheet $spreadsheet, string $name, array $users_data = []): Worksheet
    {
        
        $worksheet = parent::worksheetGet($spreadsheet, $name);

        $worksheet->setCellValue('A1', 'Адрес');
        $worksheet->setCellValue('B1', 'Дата');
        $worksheet->setCellValue('C1', 'Время');
        $worksheet->setCellValue('D1', 'ID участника');
        $worksheet->setCellValue('E1', 'ФИО');
        $worksheet->setCellValue('F1', 'E-mail');
        $worksheet->setCellValue('G1', 'Номер телефона');
        $worksheet->setCellValue('H1', 'Дата рождения');
        $worksheet->setCellValue('I1', 'Город');
        $worksheet->setCellValue('J1', 'Организация');
        $worksheet->setCellValue('K1', 'Специальность');

        $i = 2;
        
        $this->users_data = empty($users_data) ?
            $this->users->getAllData() : $users_data;

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

                $fio = [];

                if (!empty(
                    $this->users_data[$visit['user_id']]['Surname']
                )) $fio[] = $this->users_data[$visit['user_id']]['Surname'];

                if (!empty(
                    $this->users_data[$visit['user_id']]['Name']
                )) $fio[] = $this->users_data[$visit['user_id']]['Name'];

                if (!empty(
                    $this->users_data[$visit['user_id']]['LastName']
                )) $fio[] = $this->users_data[$visit['user_id']]['LastName'];

                $fio = implode(" ", $fio);

                $worksheet->setCellValue('E'.$i, $fio);

                $worksheet->setCellValue(
                    'F'.$i,
                    empty($this->users_data[$visit['user_id']]['email']) ?
                        '' : $this->users_data[$visit['user_id']]['email']
                );
                $worksheet->setCellValue(
                    'G'.$i,
                    empty($this->users_data[$visit['user_id']]['phone']) ?
                        '' : $this->users_data[$visit['user_id']]['phone']
                );
                $worksheet->setCellValue(
                    'H'.$i,
                    empty($this->users_data[$visit['user_id']]['Date_of_Birth']) ?
                        '' : $this->users_data[$visit['user_id']]['Date_of_Birth']
                );
                $worksheet->setCellValue(
                    'I'.$i,
                    empty($this->users_data[$visit['user_id']]['town']) ?
                        '' : $this->users_data[$visit['user_id']]['town']
                );
                $worksheet->setCellValue(
                    'J'.$i,
                    empty($this->users_data[$visit['user_id']]['Organization']) ?
                        '' : $this->users_data[$visit['user_id']]['Organization']
                );
                $worksheet->setCellValue(
                    'K'.$i,
                    empty($this->users_data[$visit['user_id']]['Specialty']) ?
                        '' : $this->users_data[$visit['user_id']]['Specialty']
                );

                $i += 1;

            }

        }

        return $worksheet;

    }

}
