<?php
/**
 * Event Platform Statistics
 */
namespace EPStatistics\Handlers;

use EPStatistics\MetadataMatching;
use EPStatistics\Users;
use PhpOffice\PhpSpreadsheet\Worksheet\Worksheet;
use PhpOffice\PhpSpreadsheet\Spreadsheet;
use PhpOffice\PhpSpreadsheet\Cell\DataType;

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

                $worksheet
                    ->getCell($this->getColumnName($col).'1')
                        ->setValueExplicit(
                            $match['name'],
                            DataType::TYPE_STRING
                        );

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

                $worksheet
                    ->getCell('A'.$row)
                        ->setValueExplicit(
                            $user_id,
                            DataType::TYPE_STRING
                        );

                $worksheet
                    ->getCell('B'.$row)
                        ->setValueExplicit(
                            $values['email'],
                            DataType::TYPE_STRING
                        );

                foreach ($matches as $match) {

                    if (isset($values[$match['key']])) $worksheet
                        ->getCell($this->getColumnName($col).$row)
                            ->setValueExplicit(
                                $values[$match['key']],
                                DataType::TYPE_STRING
                            );

                    $col += 1;

                }

                $presence = empty($values['presence_times']) ?
                    0 : count($values['presence_times']);

                $worksheet
                    ->getCell($this->getColumnName($col).$row)
                        ->setValueExplicit(
                            $presence,
                            DataType::TYPE_STRING
                        );

                $presence_count += $presence;

                $row += 1;

            }

            $worksheet
                ->getCell($presence_count_cell)
                    ->setValueExplicit(
                        $presence_count,
                        DataType::TYPE_STRING
                    );

        }

        return $worksheet;

    }

}
