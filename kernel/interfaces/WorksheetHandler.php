<?php
/**
 * Event Platform Statistics
 */
namespace EPStatistics\Interfaces;

use PhpOffice\PhpSpreadsheet\Spreadsheet;
use PhpOffice\PhpSpreadsheet\Worksheet\Worksheet;

interface WorksheetHandler
{

    /**
     * Return worksheet with handled data.
     * 
     * @param PhpOffice\PhpSpreadsheet\Spreadsheet $spreadsheet
     * @param string $name
     * 
     * @return PhpOffice\PhpSpreadsheet\Worksheet\Worksheet
     */
    public function worksheetGet(Spreadsheet $spreadsheet, string $name) : Worksheet;

}
