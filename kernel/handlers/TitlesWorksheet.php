<?php
/**
 * Event Platform Statistics
 */
namespace EPStatistics\Handlers;

use EPStatistics\Titles;
use EPStatistics\Interfaces\WorksheetHandler;
use PhpOffice\PhpSpreadsheet\Spreadsheet;
use PhpOffice\PhpSpreadsheet\Worksheet\Worksheet;

class TitlesWorksheet implements WorksheetHandler
{

    protected $titles;

    public function __construct(Titles $titles)
    {
        
        $this->titles = $titles;

    }

    public function worksheetGet(Spreadsheet $spreadsheet, string $name): Worksheet
    {
        
        if (empty($name)) $name = 'Лист '.$spreadsheet->getSheetCount();

        $worksheet = new Worksheet($spreadsheet, $name);

        $titles_selected = $this->titles->selectTitles();

        $worksheet->setCellValue('A1', 'ID');
        $worksheet->setCellValue('B1', 'Заголовок');
        $worksheet->setCellValue('C1', 'Зал');
        $worksheet->setCellValue('D1', 'Начало');
        $worksheet->setCellValue('E1', 'Конец');
        $worksheet->setCellValue('F1', 'НМО');

        $i = 2;

        foreach ($titles_selected as $title) {

            $worksheet->setCellValue('A'.$i, $title['id']);
            $worksheet->setCellValue('B'.$i, $title['title']);
            $worksheet->setCellValue('C'.$i, $title['list_name']);
            $worksheet->setCellValue('D'.$i, $title['datetime_start']);
            $worksheet->setCellValue('E'.$i, $title['datetime_end']);
            $worksheet->setCellValue(
                'F'.$i,
                $title['nmo'] === '1' ?
                'Да' :
                'Нет'
            );

            $i += 1;

        }

        return $worksheet;

    }

}
