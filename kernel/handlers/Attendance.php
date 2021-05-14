<?php
/**
 * @package Event Platform Statistics
 */
namespace EPStatistics\Handlers;

use EPStatistics\Users;
use EPStatistics\Visits;
use EPStatistics\MetadataMatching;
use PhpOffice\PhpSpreadsheet\Spreadsheet;
use PhpOffice\PhpSpreadsheet\Worksheet\Worksheet;
use PhpOffice\PhpSpreadsheet\Cell\DataType;

/**
 * Attendance handler.
 * @since 1.9.11
 */
class Attendance extends UsersWorksheetHandler
{

    /**
     * @var Users $users
     * Users storage.
     */
    protected $users;

    /**
     * @var Visits $visits
     * Visits storage.
     */
    protected $visits;

    public function __construct(Visits $visits, Users $users)
    {
        
        $this->users = $users;
        $this->visits = $visits;

    }

    public function worksheetGet(Spreadsheet $spreadsheet, string $name, array $users_data = [], string $date_start = '', string $date_end = ''): Worksheet
    {
        
        $worksheet = parent::worksheetGet($spreadsheet, $name);

        $worksheet->setCellValue('A1', 'URL страницы');
        $worksheet->setCellValue('B1', 'Дата');
        $worksheet->setCellValue('C1', 'Время');
        $worksheet->setCellValue('D1', 'ID участника');
        $worksheet->setCellValue('E1', 'E-mail');

        $metadata_matching = new MetadataMatching($this->users->wpdbGet());

        $matches = $metadata_matching->matchesAll('ASC', true);

        $col_base = 6;
        $col = $col_base;

        foreach ($matches as $match) {

            $worksheet
                ->getCell($this->getColumnName($col).'1')
                    ->setValueExplicit(
                        $match['name'],
                        DataType::TYPE_STRING
                    );

            $col += 1;

        }

        $row = 2;
        
        $this->users_data = empty($users_data) ?
            $this->users->getAllData() : $users_data;

        $visits_data = $this->visits->getVisits($date_start, $date_end);

        if (!empty($visits_data)) {

            foreach ($visits_data as $visit) {

                $worksheet
                    ->getCell('A'.$row)
                        ->setValueExplicit(
                            $visit['page_url'],
                            DataType::TYPE_STRING
                        );
                
                $datetime = date(
                    "d.m.Y H:i:s",
                    strtotime($visit['datetime'])
                );
                $datetime = explode(' ', $datetime);

                $worksheet
                    ->getCell('B'.$row)
                        ->setValueExplicit(
                            $datetime[0],
                            DataType::TYPE_STRING
                        );

                $worksheet
                    ->getCell('C'.$row)
                        ->setValueExplicit(
                            $datetime[1],
                            DataType::TYPE_STRING
                        );

                $worksheet
                    ->getCell('D'.$row)
                        ->setValueExplicit(
                            $visit['user_id'],
                            DataType::TYPE_STRING
                        );

                $worksheet
                    ->getCell('E'.$row)
                        ->setValueExplicit(
                            $this->users_data[$visit['user_id']]['email'],
                            DataType::TYPE_STRING
                        );
                        

                $col = $col_base;

                foreach ($matches as $match) {

                    if (isset(
                        $this->users_data[$visit['user_id']][$match['key']]
                    )) $worksheet
                        ->getCell($this->getColumnName($col).$row)
                            ->setValueExplicit(
                                $this->users_data[$visit['user_id']][$match['key']],
                                DataType::TYPE_STRING
                            );

                    $col += 1;

                }

                $row += 1;

            }

        }

        return $worksheet;

    }

}
