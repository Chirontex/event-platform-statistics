<?php
/**
 * Event Platform Statistics
 */
namespace EPStatistics\Handlers;

use EPStatistics\Users;
use EPStatistics\Visits;
use EPStatistics\MetadataMatching;
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

            $worksheet->setCellValue(
                $this->getColumnName($col).'1',
                $match['name']
            );

            $col += 1;

        }

        $row = 2;
        
        $this->users_data = empty($users_data) ?
            $this->users->getAllData() : $users_data;

        $visits_data = $this->visits->getVisits();

        if (!empty($visits_data)) {

            foreach ($visits_data as $visit) {

                $worksheet->setCellValue('A'.$row, $visit['page_url']);
                
                $datetime = date(
                    "d.m.Y H:i:s",
                    strtotime($visit['datetime'])
                );
                $datetime = explode(' ', $datetime);

                $worksheet->setCellValue('B'.$row, $datetime[0]);
                $worksheet->setCellValue('C'.$row, $datetime[1]);
                $worksheet->setCellValue('D'.$row, $visit['user_id']);
                $worksheet->setCellValue(
                    'E'.$row,
                    $this->users_data[$visit['user_id']]['email']
                );

                $col = $col_base;

                foreach ($matches as $match) {

                    if (isset(
                        $this->users_data[$visit['user_id']][$match['key']]
                    )) $worksheet->setCellValue(
                        $this->getColumnName($col).$row,
                        $this->users_data[$visit['user_id']][$match['key']]
                    );

                    $col += 1;

                }

                $row += 1;

            }

        }

        return $worksheet;

    }

}
