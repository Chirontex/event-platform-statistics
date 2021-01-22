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
            $worksheet->setCellValue('B1', 'E-mail');
            $worksheet->setCellValue('C1', 'Имя');
            $worksheet->setCellValue('D1', 'Фамилия');

            $i = 2;

            foreach ($data as $user_id => $userdata) {

                $worksheet->setCellValue('A'.$i, $user_id);
                $worksheet->setCellValue('B'.$i, $userdata['email']);
                $worksheet->setCellValue('C'.$i, $userdata['first_name']);
                $worksheet->setCellValue('D'.$i, $userdata['last_name']);

                $i += 1;

            }

        }

        return $worksheet;

    }

}
