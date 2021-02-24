<?php
/**
 * Event Platform Statistics
 */
namespace EPStatistics\Handlers;

use EPStatistics\MetadataMatching;
use EPStatistics\Users;
use PhpOffice\PhpSpreadsheet\Worksheet\Worksheet;
use PhpOffice\PhpSpreadsheet\Spreadsheet;

class Participants extends UsersWorksheetHandler
{

    protected $users;

    public function __construct(Users $users)
    {
        
        $this->users = $users;

    }

    public function worksheetGet(Spreadsheet $spreadsheet, string $name, array $users_data = []) : Worksheet
    {

        $worksheet = parent::worksheetGet($spreadsheet, $name);

        $this->users_data = empty($users_data) ?
            $this->users->getAllData() : $users_data;

        if (!empty($this->users_data)) {

            $worksheet->setCellValue('A1', 'ID');
            $worksheet->setCellValue('B1', 'E-mail');

            $metadata_matching = new MetadataMatching($this->users->wpdbGet());

            $matches = $metadata_matching->matchesAll('ASC', true);

            $col_base = 3;
            $col = $col_base;

            foreach ($matches as $match) {

                $worksheet->setCellValue(
                    $this->getColumnName($col).'1', $match['name']);

                $col += 1;

            }

            $worksheet->setCellValue(
                $this->getColumnName($col).'1',
                'Всего подтверждений присутствия'
            );

            $worksheet->setCellValue(
                $this->getColumnName($col - 1).'2',
                'По всем пользователям:'
            );
            
            $presence_count_cell = $this->getColumnName($col).'2';

            $row = 3;

            $presence_count = 0;

            foreach ($this->users_data as $user_id => $values) {

                $col = $col_base;

                $worksheet->setCellValue('A'.$row, $user_id);
                $worksheet->setCellValue('B'.$row, $values['email']);

                foreach ($matches as $match) {

                    if (isset($values[$match['key']])) $worksheet->setCellValue(
                        $this->getColumnName($col).$row,
                        $values[$match['key']]
                    );

                    $col += 1;

                }

                $presence = empty($values['presence_times']) ?
                    0 : count($values['presence_times']);

                $worksheet->setCellValue(
                    $this->getColumnName($col).$row,
                    $presence
                );

                $presence_count += $presence;

                $row += 1;

            }

            $worksheet->setCellValue($presence_count_cell, $presence_count);

        }

        return $worksheet;

    }

}
