<?php
/**
 * Event Platform Statistics
 */
namespace EPStatistics\Handlers;

use EPStatistics\Users;
use EPStatistics\Handlers\UsersWorksheetHandler;
use PhpOffice\PhpSpreadsheet\Worksheet\Worksheet;
use PhpOffice\PhpSpreadsheet\Spreadsheet;

class Participants extends UsersWorksheetHandler
{

    protected $users;

    public function __construct(Users $users)
    {
        
        $this->users = $users;

    }

    public function worksheetGet(Spreadsheet $spreadsheet, string $name, array $users_data = []) : Worksheet
    {

        $worksheet = parent::worksheetGet($spreadsheet, $name);

        $this->users_data = empty($users_data) ?
            $this->users->getAllData() : $users_data;

        if (!empty($this->users_data)) {

            $worksheet->setCellValue('A1', 'ID');
            $worksheet->setCellValue('B1', 'Фамилия');
            $worksheet->setCellValue('C1', 'Имя');
            $worksheet->setCellValue('D1', 'Отчество');
            $worksheet->setCellValue('E1', 'E-mail');
            $worksheet->setCellValue('F1', 'Номер телефона');
            $worksheet->setCellValue('G1', 'Дата рождения');
            $worksheet->setCellValue('H1', 'Организация');
            $worksheet->setCellValue('I1', 'Специальность');
            $worksheet->setCellValue('J1', 'Город');
            $worksheet->setCellValue('K1', 'Дано согласие');
            $worksheet->setCellValue('L1', 'Всего подтверждений присутствия');

            $worksheet->setCellValue('K2', 'По всем пользователям:');

            $i = 3;

            $nmo_count = 0;

            foreach ($this->users_data as $user_id => $values) {

                $worksheet->setCellValue('A'.$i, $user_id);

                $worksheet->setCellValue(
                    'B'.$i,
                    empty($values['Surname'])? '' : $values['Surname']
                );
                $worksheet->setCellValue(
                    'C'.$i,
                    empty($values['Name']) ? '' : $values['Name']
                );
                $worksheet->setCellValue(
                    'D'.$i,
                    empty($values['LastName']) ? '' : $values['LastName']
                );

                $worksheet->setCellValue('E'.$i,$values['email']);

                $worksheet->setCellValue(
                    'F'.$i,
                    empty($values['phone']) ? '' : $values['phone']
                );
                $worksheet->setCellValue(
                    'G'.$i,
                    empty($values['Date_of_Birth']) ? '' : $values['Date_of_Birth']
                );
                $worksheet->setCellValue(
                    'H'.$i,
                    empty($values['Organization']) ? '' : $values['Organization']
                );
                $worksheet->setCellValue(
                    'I'.$i,
                    empty($values['Specialty']) ? '' : $values['Specialty']
                );
                $worksheet->setCellValue(
                    'J'.$i,
                    empty($values['town']) ? '' : $values['town']
                );
                
                if (empty(
                    $values['Soglasye']
                )) $worksheet->setCellValue('K'.$i, 'Нет');
                else $worksheet->setCellValue('K'.$i, 'Да');

                $nmo = empty($values['presence_times']) ?
                    0 : count($values['presence_times']);

                $worksheet->setCellValue('L'.$i, $nmo);

                $nmo_count += $nmo;

                $i += 1;

            }

            $worksheet->setCellValue('L2', $nmo_count);

        }

        return $worksheet;

    }

}
