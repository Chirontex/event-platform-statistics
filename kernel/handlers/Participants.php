<?php
/**
 * Event Platform Statistics
 */
namespace EPStatistics\Handlers;

use EPStatistics\Users;
use EPStatistics\Exceptions\ParticipantsException;
use EPStatistics\Traits\Randomizer;
use PhpOffice\PhpSpreadsheet\Spreadsheet;
use PhpOffice\PhpSpreadsheet\IOFactory;

class Participants extends Handler
{

    use Randomizer;

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

    public function getAllParticipants()
    {

        do {

            $pathfile = $this->path.'/'.$this->generateRandomString().'.xlsx';

        } while (!file_exists($pathfile));

        file_put_contents($pathfile, '');

        $spreadsheet = new Spreadsheet;

        $worksheet = $spreadsheet->getSheet(0);

        

    }

}
