<?php
/**
 * @package Event Platform Statistics
 */
namespace EPStatistics\Handlers;

use EPStatistics\Users;
use EPStatistics\Interfaces\WorksheetHandler;
use PhpOffice\PhpSpreadsheet\Spreadsheet;
use PhpOffice\PhpSpreadsheet\Worksheet\Worksheet;
use PhpOffice\PhpSpreadsheet\Cell\DataType;

/**
 * Demography handler.
 * @since 1.9.11
 */
class Demography implements WorksheetHandler
{

    /**
     * @var Users $users
     * Users storage.
     */
    protected $users;

    public function __construct(Users $users)
    {
        
        $this->users = $users;

    }

    public function worksheetGet(Spreadsheet $spreadsheet, string $name) : Worksheet
    {
        
        if (empty($name)) $name = 'Лист '.$spreadsheet->getSheetCount();

        $worksheet = new Worksheet($spreadsheet, $name);

        $users_countries = $this->users->getUsersCountries();

        if (empty($users_countries)) {

            $worksheet->setCellValue('A1', 'Город');
            $worksheet->setCellValue('B1', 'Кол-во участников');

            $users_towns = $this->users->getUsersTowns();

            if (!empty($users_towns)) {

                $i = 2;

                foreach ($users_towns as $town => $ids) {

                    $worksheet
                        ->getCell('A'.$i)
                            ->setDataType(DataType::TYPE_STRING)
                            ->setValue($town);

                    $worksheet->setCellValue('B'.$i, count($ids));

                    $i += 1;

                }

            }

        } else {

            $worksheet->setCellValue('A1', 'Страна');
            $worksheet->setCellValue('B1', 'Регион');
            $worksheet->setCellValue('C1', 'Город');
            $worksheet->setCellValue('D1', 'Кол-во участников');

            $i = 2;

            foreach ($users_countries as $country => $regions) {

                foreach ($regions as $region => $cities) {

                    foreach ($cities as $city => $users) {

                        if (is_array($users)) {

                            $worksheet
                                ->getCell('A'.$i)
                                    ->setValueExplicit(
                                        $country,
                                        DataType::TYPE_STRING
                                    );

                            $worksheet
                                ->getCell('B'.$i)
                                    ->setValueExplicit(
                                        $region,
                                        DataType::TYPE_STRING
                                    );

                            $worksheet
                                ->getCell('C'.$i)
                                    ->setValueExplicit(
                                        $city,
                                        DataType::TYPE_STRING
                                    );

                            $worksheet->setCellValue('D'.$i, count($users));

                            $i += 1;

                        }

                    }

                }

            }

        }

        return $worksheet;

    }

}
