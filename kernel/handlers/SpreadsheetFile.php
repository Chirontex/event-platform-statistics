<?php
/**
 * @package Event Platform Statistics
 */
namespace EPStatistics\Handlers;

use EPStatistics\Exceptions\SpreadsheetFileException;
use PhpOffice\PhpSpreadsheet\Spreadsheet;
use PhpOffice\PhpSpreadsheet\Worksheet\Worksheet;
use PhpOffice\PhpSpreadsheet\IOFactory;

/**
 * Spreadsheet handler.
 * @since 1.9.11
 */
class SpreadsheetFile extends Handler
{

    /**
     * @var \PhpOffice\PhpSpreadsheet\Spreadsheet $spreadsheet
     * PhpOffice spreadsheet object.
     */
    protected $spreadsheet;

    /**
     * @var int $worksheets_count
     * Worksheet counter.
     */
    protected $worksheets_count;

    /**
     * @var array $users_data
     * Users data.
     */
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
     * @since 1.9.11
     * 
     * @param PhpOffice\PhpSpreadsheet\Worksheet\Worksheet $worksheet
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
     * @since 1.9.11
     * 
     * @return PhpOffice\PhpSpreadsheet\Spreadsheet
     */
    public function spreadsheetGet() : Spreadsheet
    {

        return $this->spreadsheet;

    }

    /**
     * Save spreadsheet to file.
     * @since 1.9.11
     * 
     * @return $this
     * 
     * @throws EPStatistics\Exceptions\SpreadsheetFileException
     */
    public function spreadsheetSave() : self
    {

        if (!$this->fileCreate()) throw new SpreadsheetFileException(
            SpreadsheetFileException::FILE_CREATION_FAILURE_MESSAGE,
            SpreadsheetFileException::FILE_CREATION_FAILURE_CODE
        );

        $writer = IOFactory::createWriter($this->spreadsheet, 'Xlsx');
        $writer->save($this->pathfile);

        return $this;

    }

    /**
     * Set users data.
     * @since 1.9.11
     * 
     * @param array $users_data
     * 
     * @return $this
     */
    public function usersDataSet(array $users_data) : self
    {

        $this->users_data = $users_data;

        return $this;

    }

    /**
     * Get users data.
     * @since 1.9.11
     * 
     * @return array
     */
    public function usersDataGet() : array
    {

        return $this->users_data;

    }

}
