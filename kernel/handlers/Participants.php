<?php
/**
 * Event Platform Statistics
 */
namespace EPStatistics\Handlers;

use EPStatistics\Users;
use EPStatistics\Interfaces\WorksheetHandler;
use PhpOffice\PhpSpreadsheet\Worksheet\Worksheet;
use PhpOffice\PhpSpreadsheet\Spreadsheet;

class Participants implements WorksheetHandler
{

    protected $users;

    public function __construct(Users $users)
    {
        
        $this->users = $users;

    }

    public function worksheetGet(Spreadsheet $spreadsheet, string $name) : Worksheet
    {
        
        if (empty($name)) $name = 'Лист '.$spreadsheet->getSheetCount();

        $worksheet = new Worksheet($spreadsheet, $name);

        $data = $this->users->getAllData();

        if (!empty($data)) {

            $worksheet->setCellValue('A1', 'ID');
            $worksheet->setCellValue('B1', 'Фамилия');
            $worksheet->setCellValue('C1', 'Имя');
            $worksheet->setCellValue('D1', 'Отчество');
            $worksheet->setCellValue('E1', 'E-mail');
            $worksheet->setCellValue('F1', 'Номер телефона');
            $worksheet->setCellValue('G1', 'Дата рождения');
            $worksheet->setCellValue('H1', 'Организация');
            $worksheet->setCellValue('I1', 'Специальность');

            $i = 2;

            foreach ($data as $user_id => $userdata) {

                $worksheet->setCellValue('A'.$i, $user_id);
                $worksheet->setCellValue('B'.$i, $userdata['Surname']);
                $worksheet->setCellValue('C'.$i, $userdata['Name']);
                $worksheet->setCellValue('D'.$i, $userdata['LastName']);
                $worksheet->setCellValue('E'.$i, $userdata['email']);
                $worksheet->setCellValue('F'.$i, $userdata['phone']);
                $worksheet->setCellValue('G'.$i, $userdata['Date_of_Birth']);
                $worksheet->setCellValue('H'.$i, $userdata['Organization']);
                $worksheet->setCellValue('I'.$i, $userdata['Specialty']);

                $i += 1;

            }

        }

        return $worksheet;

    }

}