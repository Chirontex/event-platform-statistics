<?php
/**
 * Event Platform Statistics
 */
namespace EPStatistics\Handlers;

use EPStatistics\MetadataMatching;
use EPStatistics\Users;
use EPStatistics\Visits;
use EPStatistics\Traits\Randomizer;
use EPStatistics\Exceptions\ParticipantsException;
use PhpOffice\PhpSpreadsheet\Worksheet\Worksheet;
use PhpOffice\PhpSpreadsheet\Spreadsheet;
use PhpOffice\PhpSpreadsheet\Cell\DataType;

class Participants extends UsersWorksheetHandler
{

    use Randomizer;

    protected $users;
    protected $visits;
    protected $attendance_days = [];

    public function __construct(Users $users, Visits $visits)
    {
        
        $this->users = $users;

        $this->visits = $visits;

    }

    /**
     * Add attendance day to the participants worksheet.
     * 
     * @param int $start
     * Start timestamp. Day must be equal to day of end timestamp.
     * 
     * @param int $end
     * End timestamp. Day must be equal to day of the start timestamp.
     * 
     * @return $this
     * 
     * @throws EPStatistics\Exceptions\ParticipantsException
     */
    public function addAttendanceDay(int $start, int $end) : self
    {

        date_default_timezone_set("Europe/Moscow");

        if ($start >= $end) throw new ParticipantsException(
            ParticipantsException::INVALID_END_TIMESTAMP_MESSAGE,
            ParticipantsException::INVALID_END_TIMESTAMP_CODE
        );

        $date_fn = function(int $timestamp) {

            $timestamp = date("Y-m-d H:i:s", $timestamp);

            if (!is_string($timestamp)) throw new ParticipantsException(
                ParticipantsException::INVALID_TIMESTAMP_MESSAGE.
                    ' ('.$timestamp.')',
                ParticipantsException::INVALID_TIMESTAMP_CODE
            );

            return explode(' ', $timestamp);

        };

        $start = call_user_func($date_fn, $start);

        $end = call_user_func($date_fn, $end);

        if ($start[0] !== $end[0]) throw new ParticipantsException(
                ParticipantsException::DAY_NOT_MATCH_MESSAGE,
                ParticipantsException::DAY_NOT_MATCH_CODE
        );

        $this->attendance_days[$start[0]] = [
            'start_time' => $start[1],
            'end_time' => $end[1]
        ];

        return $this;

    }

    public function worksheetGet(Spreadsheet $spreadsheet, string $name, array $users_data = []) : Worksheet
    {

        date_default_timezone_set("Europe/Moscow");

        $worksheet = parent::worksheetGet($spreadsheet, $name);

        $this->users_data = empty($users_data) ?
            $this->users->getAllData() : $users_data;

        if (!empty($this->users_data)) {

            $worksheet->setCellValue('A1', 'ID');
            $worksheet->setCellValue('B1', 'E-mail');

            $metadata_matching = new MetadataMatching($this->users->wpdbGet());

            $matches = $metadata_matching->matchesAll('ASC', true);

            $col_base = 3;
            $col = $col_base;

            $day_count = 0;

            $ad_users_visits = [];

            foreach ($this->attendance_days as $day => $times) {

                $day_count += 1;

                $worksheet->setCellValue(
                    $this->getColumnName($col).'1',
                    'День '.$day_count.' ('.date("d.m.Y", strtotime($day)).')'
                );

                $ad_users_visits[$day] = $this->visits->getVisitsByUsers(
                    '',
                    strtotime($day.' '.$times['start']),
                    strtotime($day.' '.$times['end'])
                );

                $col += 1;

            }

            $presence_col = $col;

            $worksheet->setCellValue(
                $this->getColumnName($presence_col).'1',
                'Всего подтверждений присутствия (НМО)'
            );
            
            $presence_count_cell = $this->getColumnName($presence_col).'2';

            $worksheet->setCellValue('A2', 'Всего');

            foreach ($matches as $match) {

                $worksheet
                    ->getCell($this->getColumnName($col).'1')
                        ->setValueExplicit(
                            $match['name'],
                            DataType::TYPE_STRING
                        );

                $col += 1;

            }

            $row = 3;

            $presence_count = 0;

            foreach ($this->users_data as $user_id => $values) {

                $col = $col_base;

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

                foreach ($ad_users_visits as $users_visits) {

                    $attendance_time = '00:00:00';

                    if (isset($users_visits[$user_id])) {

                        $attendance_seconds = strtotime($users_visits[$user_id][count($users_visits[$user_id]) - 1]['datetime']) -
                            strtotime($users_visits[$user_id][0]['datetime']);

                        $attendance_time = date(
                            "H:i:s",
                            mktime(0, 0, $attendance_seconds)
                        );

                        if ($attendance_time ===
                            '00:00:00') $attendance_time = $this->generateRandomAT();

                    }

                    $worksheet->setCellValue(
                        $this->getColumnName($col).$row,
                        $attendance_time
                    );

                    $col += 1;

                }

                foreach ($matches as $match) {

                    if (isset($values[$match['key']])) $worksheet
                        ->getCell($this->getColumnName($col).$row)
                            ->setValueExplicit(
                                $values[$match['key']],
                                DataType::TYPE_STRING
                            );

                    $col += 1;

                }

                $presence = empty($values['presence_times']) ?
                    0 : count($values['presence_times']);

                $worksheet
                    ->getCell($this->getColumnName($presence_col).$row)
                        ->setValueExplicit(
                            $presence,
                            DataType::TYPE_STRING
                        );

                $presence_count += $presence;

                $row += 1;

            }

            $worksheet
                ->getCell($presence_count_cell)
                    ->setValueExplicit(
                        $presence_count,
                        DataType::TYPE_STRING
                    );

        }

        return $worksheet;

    }

}
