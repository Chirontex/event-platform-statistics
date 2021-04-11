<?php
/**
 * @package Event Platform Statistics
 */
namespace EPStatistics\Handlers;

use EPStatistics\Titles;
use EPStatistics\Visits;
use EPStatistics\Interfaces\WorksheetHandler;
use PhpOffice\PhpSpreadsheet\Spreadsheet;
use PhpOffice\PhpSpreadsheet\Worksheet\Worksheet;
use PhpOffice\PhpSpreadsheet\Cell\DataType;

/**
 * Titles worksheet handler.
 * @since 1.9.11
 */
class TitlesWorksheet implements WorksheetHandler
{

    /**
     * @var Titles $titles
     * Titles storage.
     */
    protected $titles;

    /**
     * @var Visits $visits
     * Visits storage.
     */
    protected $visits;

    public function __construct(Titles $titles, Visits $visits)
    {
        
        $this->titles = $titles;

        $this->visits = $visits;

    }

    /**
     * @param string $url_matching
     * Url matching with hall ID.
     */
    public function worksheetGet(Spreadsheet $spreadsheet, string $name, string $url_matching = ''): Worksheet
    {

        date_default_timezone_set('Europe/Moscow');
        
        if (empty($name)) $name = 'Лист '.$spreadsheet->getSheetCount();

        $worksheet = new Worksheet($spreadsheet, $name);

        $titles_selected = $this->titles->selectTitles();

        if (!empty($url_matching)) {

            $url_matching_exp = explode(PHP_EOL, $url_matching);

            $url_matching = [];

            foreach ($url_matching_exp as $row) {

                $row = explode(':::', $row);

                $url_matching[trim($row[1])] = trim($row[0]);

            }

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

            if (empty($url_matching)) $worksheet->setCellValue(
                'G'.$i,
                'неизвестно'
            );
            else {

                $average_viewing = '00:00:00';

                if (isset($url_matching[$title['list_name']])) {

                    $page_visits = $this->visits->getVisitsByUsers(
                        $url_matching[$title['list_name']],
                        strtotime($title['datetime_start']),
                        strtotime($title['datetime_end'])
                    );

                    if (!empty($page_visits)) {

                        $viewers_count = count($page_visits);

                        $time_full = 0;

                        foreach ($page_visits as $views) {

                            $time_full += strtotime($title['datetime_end']) -
                                strtotime($views[0]['datetime']);

                        }

                        $time_average = (int)round($time_full/$viewers_count);

                        $average_viewing = date(
                            "H:i:s",
                            mktime(0, 0, $time_average)
                        );

                    }

                }

                $worksheet
                    ->getCell('G'.$i)
                        ->setValueExplicit(
                            $average_viewing,
                            DataType::TYPE_STRING
                        );

            }

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
