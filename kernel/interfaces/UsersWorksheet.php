<?php
/**
 * @package Event Platform Statistics
 */
namespace EPStatistics\Interfaces;

use PhpOffice\PhpSpreadsheet\Spreadsheet;
use PhpOffice\PhpSpreadsheet\Worksheet\Worksheet;

/**
 * Handler interface which uses users data.
 * @since 1.9.11
 */
interface UsersWorksheet extends WorksheetHandler
{

    /**
     * Return worksheet with handled users data.
     * @since 1.9.11
     * 
     * @param PhpOffice\PhpSpreadsheet\Spreadsheet $spreadsheet
     * PhpOffice spreadsheet object.
     * 
     * @param string $name
     * Worksheet name.
     * 
     * @param array $users_data
     * Users data.
     * 
     * @return PhpOffice\PhpSpreadsheet\Worksheet\Worksheet
     */
    public function worksheetGet(Spreadsheet $spreadsheet, string $name, array $users_data = []) : Worksheet;

}
