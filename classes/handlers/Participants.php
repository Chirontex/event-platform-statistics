<?php
/**
 * Event Platform Statistics
 */
namespace EPStatistics\Handlers;

use EPStatistics\Users;
use PhpOffice\PhpSpreadsheet\IOFactory;

class Participants
{

    protected $users;

    public function __construct(Users $users)
    {
        
        $this->users = $users;

    }

    public function getAllParticipants()
    {

        

    }

}
