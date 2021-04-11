<?php
/**
 * @package Event Platform Statistics
 */
namespace EPStatistics\Handlers;

use EPStatistics\Users;
use EPStatistics\Tokens;
use EPStatistics\Titles;
use EPStatistics\PresenceTimes;
use EPStatistics\MetadataMatching;
use EPStatistics\Exceptions\PresenceTimesException;
use PhpOffice\PhpSpreadsheet\Spreadsheet;
use PhpOffice\PhpSpreadsheet\Worksheet\Worksheet;
use PhpOffice\PhpSpreadsheet\Cell\DataType;

/**
 * Presence effect handler.
 * @since 1.9.11
 */
class PresenceEffect extends UsersWorksheetHandler
{

    /**
     * @var PresenceTimes $presence_times
     * Presence times storage.
     */
    protected $presence_times;

    const WORKSHEET_MODE_RAW = 'raw';
    const WORKSHEET_MODE_TITLES = 'titles';

    public function __construct(PresenceTimes $presence_times)
    {
        
        $this->presence_times = $presence_times;

    }

    /**
     * Implementation of /event-platform-statistics/v1/presence-time/add route.
     * @since 1.9.11
     * 
     * @return array
     */
    public function apiAddPresenceTime() : array
    {

        $result = [];

        if (isset($_REQUEST['list'])) {

            $tokens = new Tokens($this->presence_times->wpdbGet());

            $user_id = $tokens->userGetByToken($_COOKIE['eps_api_token']);

            try {

                $add = $this->presence_times->add($user_id, trim($_REQUEST['list']));

            } catch (PresenceTimesException $e) {

                $result = [
                    'code' => $e->getCode(),
                    'message' => $e->getMessage()
                ];

            }

            if (empty($result)) {

                if ($add) $result = [
                    'code' => 0,
                    'message' => 'Success.'
                ];
                else $result = [
                    'code' => -33,
                    'message' => 'Presence time adding to database failure.'
                ];

            }

        } else $result = [
            'code' => -99,
            'message' => 'Too few arguments for this request.'
        ];

        return $result;

    }

    public function worksheetGet(Spreadsheet $spreadsheet, string $name, array $users_data = [], string $mode = 'raw') : Worksheet
    {

        if ($mode !== 'raw' && $mode !== 'titles') $mode = 'raw';

        $worksheet = parent::worksheetGet($spreadsheet, $name);

        if (empty($users_data)) {

            $users = new Users($this->presence_times->wpdbGet());

            $this->users_data = $users->getAllData();

        } else $this->users_data = $users_data;

        if (!empty($this->users_data)) {

            switch ($mode) {

                case 'raw':
                    $worksheet = $this->worksheetRaw($worksheet);
                    break;

                case 'titles':
                    $worksheet = $this->worksheetTitles($worksheet);
                    break;

            }

        }

        return $worksheet;

    }

    /**
     * Add raw data to worksheet.
     * @since 1.9.11
     * 
     * @param PhpOffice\PhpSpreadsheet\Worksheet\Worksheet $worksheet
     * 
     * @return PhpOffice\PhpSpreadsheet\Worksheet\Worksheet
     */
    protected function worksheetRaw(Worksheet $worksheet) : Worksheet
    {

        $worksheet->setCellValue('A1', 'ID');
        $worksheet->setCellValue('B1', 'E-mail');

        $metadata_matching = new MetadataMatching($this->presence_times->wpdbGet());

        $matches = $metadata_matching->matchesAll('ASC', true);

        $col_base = 3;
        $col = $col_base;

        foreach ($matches as $match) {

            $worksheet
                ->getCell($this->getColumnName($col).'1')
                    ->setValueExplicit(
                        $match['name'],
                        DataType::TYPE_STRING
                    );

            $col += 1;

        }

        $worksheet->setCellValue(
            $this->getColumnName($col).'1',
            'Зал'
        );

        $worksheet->setCellValue(
            $this->getColumnName($col + 1).'1',
            'Дата и время подтверждения'
        );

        $row = 2;

        foreach ($this->users_data as $user_id => $values) {

            if (isset($values['presence_times'])) {

                foreach ($values['presence_times'] as $datetime) {

                    $worksheet
                        ->getCell('A'.$row)
                            ->setValueExplicit(
                                $user_id,
                                DataType::TYPE_STRING
                            );

                    $worksheet
                            ->getCell('B'.$row)
                                ->setValueExplicit(
                                    $values['email'],
                                    DataType::TYPE_STRING
                                );

                    $col = $col_base;

                    foreach ($matches as $match) {

                        if (isset($values[$match['key']])) $worksheet
                            ->getCell($this->getColumnName($col).$row)
                                ->setValueExplicit(
                                    $values[$match['key']],
                                    DataType::TYPE_STRING
                                );

                        $col += 1;

                    }

                    if (isset($datetime['list'])) $worksheet
                        ->getCell($this->getColumnName($col).$row)
                            ->setValueExplicit(
                                $datetime['list'],
                                DataType::TYPE_STRING
                            );

                    if (isset($datetime['datetime'])) $worksheet
                        ->getCell($this->getColumnName($col + 1).$row)
                            ->setValueExplicit(
                                $datetime['datetime'],
                                DataType::TYPE_STRING
                            );

                    $row += 1;

                }

            }

        }

        return $worksheet;

    }

    /**
     * Add data with titles to worksheet.
     * @since 1.9.11
     * 
     * @param PhpOffice\PhpSpreadsheet\Worksheet\Worksheet $worksheet
     * 
     * @return PhpOffice\PhpSpreadsheet\Worksheet\Worksheet
     */
    protected function worksheetTitles(Worksheet $worksheet) : Worksheet
    {

        $titles = new Titles($this->presence_times->wpdbGet());

        $titles_selected = $titles->selectTitles();

        $worksheet->setCellValue('A1', 'ID пользователя');
        $worksheet->setCellValue('B1', 'E-mail');

        $metadata_matching = new MetadataMatching($this->presence_times->wpdbGet());

        $matches = $metadata_matching->matchesAll('ASC', true);

        $col_base = 3;
        $col = $col_base;

        foreach ($matches as $match) {

            $worksheet
                ->getCell($this->getColumnName($col).'1')
                    ->setValueExplicit(
                        $match['name'],
                        DataType::TYPE_STRING
                    );

            $col += 1;

        }

        $worksheet->setCellValue(
            $this->getColumnName($col).'1',
            'Всего релевантных подтверждений'
        );

        $titles_col_base = $col + 1;
        $titles_col = $titles_col_base;

        foreach ($titles_selected as $title) {

            if ((int)$title['nmo'] === 1) {

                $worksheet
                    ->getCell($this->getColumnName($titles_col).'1')
                        ->setValueExplicit(
                            'Лекция ID '.$title['id'],
                            DataType::TYPE_STRING
                        );

                $titles_col += 1;

            }

        }

        $row = 2;

        foreach ($this->users_data as $user_id => $values) {

            $worksheet
                ->getCell('A'.$row)
                    ->setValueExplicit(
                        $user_id,
                        DataType::TYPE_STRING
                    );

            $worksheet
                    ->getCell('B'.$row)
                        ->setValueExplicit(
                            $values['email'],
                            DataType::TYPE_STRING
                        );

            $col = $col_base;

            foreach ($matches as $match) {

                if (isset($values[$match['key']])) $worksheet
                    ->getCell($this->getColumnName($col).$row)
                        ->setValueExplicit(
                            $values[$match['key']],
                            DataType::TYPE_STRING
                        );

                $col += 1;

            }

            $presence_total_cell = $this->getColumnName($col).$row;

            $presence_total = 0;

            $titles_col = $titles_col_base;

            foreach ($titles_selected as $title) {

                if ((int)$title['nmo'] === 1) {

                    $presence = 0;

                    if (isset($values['presence_times'])) {

                        foreach ($values['presence_times'] as $presence_time) {

                            $datetime = strtotime($presence_time['datetime']);

                            if (($datetime >= strtotime($title['datetime_start']) &&
                                    $datetime <= strtotime($title['datetime_end'])) &&
                                $presence_time['list'] === $title['list_name']) {

                                $presence += 1;
                                $presence_total += 1;

                            }

                        }

                    }

                    $worksheet
                        ->getCell($this->getColumnName($titles_col).$row)
                            ->setValueExplicit(
                                $presence,
                                DataType::TYPE_STRING
                            );

                    $titles_col += 1;

                }

            }

            $worksheet
                ->getCell($presence_total_cell)
                    ->setValueExplicit(
                        $presence_total,
                        DataType::TYPE_STRING
                    );

            $row += 1;

        }

        return $worksheet;

    }

}
