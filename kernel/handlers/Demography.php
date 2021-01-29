<?php
/**
 * Event Platform Statistics
 */
namespace EPStatistics\Handlers;

use EPStatistics\Users;
use EPStatistics\Interfaces\WorksheetHandler;
use PhpOffice\PhpSpreadsheet\Spreadsheet;
use PhpOffice\PhpSpreadsheet\Worksheet\Worksheet;

class Demography implements WorksheetHandler
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

        $worksheet->setCellValue('A1', 'Город');
        $worksheet->setCellValue('B1', 'Кол-во участников');

        $users_towns = $this->users->getUsersTowns();

        if (!empty($users_towns)) {

            $i = 2;

            foreach ($users_towns as $town => $ids) {

                if ($town === 'not_specified') $town = 'Не указано';

                $worksheet->setCellValue('A'.$i, $town);
                $worksheet->setCellValue('B'.$i, count($ids));

                $i += 1;

            }

        }

        return $worksheet;

    }

}
