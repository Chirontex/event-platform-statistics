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

    public function __construct(string $path)
    {

        $this->spreadsheet = new Spreadsheet;
        $this->worksheets_count = 0;
        
        parent::__construct($path);

    }

    public function worksheetAdd(Worksheet $worksheet) : int
    {

        $this->spreadsheet->addSheet($worksheet, $this->worksheets_count);

        $this->worksheets_count += 1;

        return $this->worksheets_count;

    }

    public function spreadsheetSave() : void
    {

        if (!$this->fileCreate()) throw new SpreadsheetFileException(
            'File creation failure.',
            -30
        );

        $writer = IOFactory::createWriter($this->spreadsheet, 'Xlsx');
        $writer->save($this->pathfile);

    }

}
