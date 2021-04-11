<?php
/**
 * @package Event Platform Statistics
 */
namespace EPStatistics\Interfaces;

use PhpOffice\PhpSpreadsheet\Spreadsheet;
use PhpOffice\PhpSpreadsheet\Worksheet\Worksheet;

/**
 * Worksheet handler interface.
 * @since 1.9.11
 */
interface WorksheetHandler
{

    /**
     * Return worksheet with handled data.
     * @since 1.9.11
     * 
     * @param PhpOffice\PhpSpreadsheet\Spreadsheet $spreadsheet
     * @param string $name
     * 
     * @return PhpOffice\PhpSpreadsheet\Worksheet\Worksheet
     */
    public function worksheetGet(Spreadsheet $spreadsheet, string $name) : Worksheet;

}
