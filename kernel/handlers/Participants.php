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

    public function worksheetGet(Spreadsheet $spreadsheet, string $name): Worksheet
    {
        
        if (empty($name)) $name = 'Лист '.$spreadsheet->getSheetCount();

        $worksheet = new Worksheet($spreadsheet, $name);

        $data = $this->users->getAllData();

        if (!empty($data)) {

            $worksheet->setCellValue('A1', 'E-mail');

            $i = 2;

            foreach ($data as $userdata) {

                $worksheet->setCellValue('A'.$i, $userdata['email']);

                $i += 1;

            }

        }

        return $worksheet;

    }

}
