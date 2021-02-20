<?php
/**
 * Event Platform Statistics
 */
namespace EPStatistics\Handlers;

use EPStatistics\Interfaces\UsersWorksheet;
use PhpOffice\PhpSpreadsheet\Worksheet\Worksheet;
use PhpOffice\PhpSpreadsheet\Spreadsheet;

abstract class UsersWorksheetHandler implements UsersWorksheet
{

    protected $users_data;

    public function worksheetGet(Spreadsheet $spreadsheet, string $name, array $users_data = []): Worksheet
    {
        
        if (empty($name)) $name = 'Лист '.$spreadsheet->getSheetCount();

        return new Worksheet($spreadsheet, $name);

    }

    /**
     * Get users data.
     * 
     * @return array
     */
    public function usersDataGet() : array
    {

        if (!is_array($this->users_data)) $this->users_data = [];

        return $this->users_data;

    }

    /**
     * Calculates a column name by it's periodic number.
     * 
     * @param int $number
     * If $number lesser than 1 or bigger than 650,
     * the method will return an empty string.
     * 
     * @return string
     */
    protected function getColumnName(int $number) : string
    {

        $name = '';

        if ($number > 0) {

            $alphabet = range('A', 'Z');

            if ($number <= count($alphabet)) $name = $alphabet[$number - 1];
            else {

                $fi = 0;

                $dif = $number - count($alphabet);

                while ($dif > count($alphabet)) {

                    $fi += 1;

                    $dif = $dif - count($alphabet);

                }

                if ($fi <= count($alphabet)) {

                    $name .= $alphabet[$fi];
                    $name .= $alphabet[$dif - 1];

                }

            }

        }

        return $name;

    }

}
