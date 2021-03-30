<?php
/**
 * Event Platform Statistics
 */
namespace EPStatistics\Handlers;

use EPStatistics\Titles;
use EPStatistics\Interfaces\WorksheetHandler;
use PhpOffice\PhpSpreadsheet\Spreadsheet;
use PhpOffice\PhpSpreadsheet\Worksheet\Worksheet;
use PhpOffice\PhpSpreadsheet\Cell\DataType;

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

        $worksheet->setCellValue('A1', 'ID лекции');
        $worksheet->setCellValue('B1', 'Заголовок');
        $worksheet->setCellValue('C1', 'Зал');
        $worksheet->setCellValue('D1', 'Начало');
        $worksheet->setCellValue('E1', 'Конец');
        $worksheet->setCellValue('F1', 'НМО');

        $i = 2;

        foreach ($titles_selected as $title) {

            $worksheet
                ->getCell('A'.$i)
                    ->setValueExplicit(
                        $title['id'],
                        DataType::TYPE_STRING
                    );

            $worksheet
                ->getCell('B'.$i)
                    ->setValueExplicit(
                        $title['title'],
                        DataType::TYPE_STRING
                    );

            $worksheet
                ->getCell('C'.$i)
                    ->setValueExplicit(
                        $title['list_name'],
                        DataType::TYPE_STRING
                    );

            $worksheet
                ->getCell('D'.$i)
                    ->setValueExplicit(
                        $title['datetime_start'],
                        DataType::TYPE_STRING
                    );

            $worksheet
                ->getCell('E'.$i)
                    ->setValueExplicit(
                        $title['datetime_end'],
                        DataType::TYPE_STRING
                    );

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
