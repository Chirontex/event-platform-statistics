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

}
