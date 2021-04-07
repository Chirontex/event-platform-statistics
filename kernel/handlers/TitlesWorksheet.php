<?php
/**
 * Event Platform Statistics
 */
namespace EPStatistics\Handlers;

use EPStatistics\Titles;
use EPStatistics\Visits;
use EPStatistics\Interfaces\WorksheetHandler;
use PhpOffice\PhpSpreadsheet\Spreadsheet;
use PhpOffice\PhpSpreadsheet\Worksheet\Worksheet;
use PhpOffice\PhpSpreadsheet\Cell\DataType;

class TitlesWorksheet implements WorksheetHandler
{

    protected $titles;
    protected $visits;

    public function __construct(Titles $titles, Visits $visits)
    {
        
        $this->titles = $titles;

        $this->visits = $visits;

    }

    public function worksheetGet(Spreadsheet $spreadsheet, string $name, string $url_matching = ''): Worksheet
    {
        
        if (empty($name)) $name = 'Лист '.$spreadsheet->getSheetCount();

        $worksheet = new Worksheet($spreadsheet, $name);

        $titles_selected = $this->titles->selectTitles();

        if (!empty($url_matching)) {

            $url_matching = explode(PHP_EOL, $url_matching);

            $url_matching = array_map(function($match) {

                $match = explode(' - ', $match);
                $match[$match[1]] = $match[0];

                return $match;

            }, $url_matching);

        }

        $worksheet->setCellValue('A1', 'ID лекции');
        $worksheet->setCellValue('B1', 'Заголовок');
        $worksheet->setCellValue('C1', 'Зал');
        $worksheet->setCellValue('D1', 'Начало');
        $worksheet->setCellValue('E1', 'Конец');
        $worksheet->setCellValue('F1', 'Продолжительность');
        $worksheet->setCellValue('G1', 'Средняя продолжительность просмотра');
        $worksheet->setCellValue('H1', 'НМО');

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

            $duration = strtotime($title['datetime_end']) -
                strtotime($title['datetime_start']);

            $worksheet
                ->getCell('F'.$i)
                    ->setValueExplicit(
                        date("H:i:s", mktime(0, 0, $duration)),
                        DataType::TYPE_STRING
                    );

            $worksheet->setCellValue(
                'H'.$i,
                $title['nmo'] === '1' ?
                'Да' :
                'Нет'
            );

            $i += 1;

        }

        return $worksheet;

    }

}
