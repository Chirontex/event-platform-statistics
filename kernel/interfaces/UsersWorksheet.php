<?php
/**
 * Event Platform Statistics
 */
namespace EPStatistics\Interfaces;

use PhpOffice\PhpSpreadsheet\Spreadsheet;
use PhpOffice\PhpSpreadsheet\Worksheet\Worksheet;

interface UsersWorksheet extends WorksheetHandler
{

    /**
     * Return worksheet with handled users data.
     * 
     * @param PhpOffice\PhpSpreadsheet\Spreadsheet $spreadsheet
     * 
     * @param string $name
     * 
     * @param array $users_data
     * 
     * @return PhpOffice\PhpSpreadsheet\Worksheet\Worksheet
     */
    public function worksheetGet(Spreadsheet $spreadsheet, string $name, array $users_data = []) : Worksheet;

}
