<?php
/**
 * Event Platform Statistics
 */
namespace EPStatistics\Handlers;

use EPStatistics\Exceptions\SpreadsheetFileException;
use PhpOffice\PhpSpreadsheet\Spreadsheet;
use PhpOffice\PhpSpreadsheet\Worksheet\Worksheet;
use PhpOffice\PhpSpreadsheet\IOFactory;

class SpreadsheetFile extends Handler
{

    protected $spreadsheet;
    protected $worksheets_count;
    protected $users_data;

    public function __construct(string $path)
    {

        $this->spreadsheet = new Spreadsheet;
        $this->spreadsheet->removeSheetByIndex(0);
        
        $this->worksheets_count = 0;

        $this->users_data = [];
        
        parent::__construct($path);

    }

    /**
     * Add worksheet to spreadsheet.
     * 
     * @param Worksheet $worksheet
     * 
     * @return int
     * Amount of worksheets in spreadsheet at this moment.
     */
    public function worksheetAdd(Worksheet $worksheet) : int
    {

        $this->spreadsheet->addSheet($worksheet, $this->worksheets_count);

        $this->worksheets_count += 1;

        return $this->worksheets_count;

    }

    /**
     * Return spreadsheet object.
     * 
     * @return Spreadsheet
     */
    public function spreadsheetGet() : Spreadsheet
    {

        return $this->spreadsheet;

    }

    /**
     * Save spreadsheet to file.
     * 
     * @return void
     * 
     * @throws SpreadsheetFileException
     */
    public function spreadsheetSave() : void
    {

        if (!$this->fileCreate()) throw new SpreadsheetFileException(
            SpreadsheetFileException::FILE_CREATION_FAILURE_MESSAGE,
            SpreadsheetFileException::FILE_CREATION_FAILURE_CODE
        );

        $writer = IOFactory::createWriter($this->spreadsheet, 'Xlsx');
        $writer->save($this->pathfile);

    }

    /**
     * Set users data.
     * 
     * @param array $users_data
     * 
     * @return void
     */
    public function usersDataSet(array $users_data) : void
    {

        $this->users_data = $users_data;

    }

    /**
     * Get users data.
     * 
     * @return array
     */
    public function usersDataGet() : array
    {

        return $this->users_data;

    }

}
