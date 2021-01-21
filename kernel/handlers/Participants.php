<?php
/**
 * Event Platform Statistics
 */
namespace EPStatistics\Handlers;

use EPStatistics\Users;
use EPStatistics\Exceptions\ParticipantsException;
use PhpOffice\PhpSpreadsheet\Spreadsheet;
use PhpOffice\PhpSpreadsheet\IOFactory;

class Participants extends Handler
{

    protected $users;

    public function __construct(Users $users, string $path)
    {
        
        $this->users = $users;

        $path .= 'temp';

        if (!file_exists($path)) {
            
            if (!mkdir($path)) throw new ParticipantsException(
                'Directory creation failure.',
                -10
            );
        
        }

        parent::__construct($path);

    }

    public function getAll() : string
    {

        $result = '';

        do {

            $pathfile = $this->path.'/'.$this->generateRandomString().'.xlsx';

        } while (file_exists($pathfile));

        file_put_contents($pathfile, '');

        $spreadsheet = new Spreadsheet;

        $worksheet = $spreadsheet->getSheet(0);
        $worksheet->setTitle('Участники');

        $data = $this->users->getAllData();

        if (!empty($data)) {

            $worksheet->setCellValue('A1', 'E-mail');

            $i = 2;

            foreach ($data as $userdata) {

                $worksheet->setCellValue('A'.$i, $userdata['email']);

                $i += 1;

            }

        }

        $writer = IOFactory::createWriter($spreadsheet, 'Xlsx');
        $writer->save($pathfile);

        $result = file_get_contents($pathfile);

        if (!is_string($result)) throw new ParticipantsException(
            'Cannot read a saved file.',
            -11
        );

        unlink($pathfile);

        return $result;

    }

}
