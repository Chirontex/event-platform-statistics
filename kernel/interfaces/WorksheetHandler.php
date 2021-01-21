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
     * @param Spreadsheet $spreadsheet
     * @param string $name
     * 
     * @return Worksheet
     */
    public function worksheetGet(Spreadsheet $spreadsheet, string $name) : Worksheet;

}
