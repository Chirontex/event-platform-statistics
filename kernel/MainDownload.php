<?php
/**
 * Event Platform Statistics
 */
namespace EPStatistics;

use EPStatistics\Handlers\SpreadsheetFile;
use EPStatistics\Handlers\Participants;
use EPStatistics\Handlers\Demography;
use EPStatistics\Handlers\Attendance;
use EPStatistics\Handlers\TitlesWorksheet;
use EPStatistics\Handlers\PresenceEffect;
use EPStatistics\Exceptions\HandlerException;
use EPStatistics\Exceptions\SpreadsheetFileException;

class MainDownload extends AdminPage
{

    public function downloadInit() : void
    {

        add_action('admin_enqueue_scripts', function() {

            wp_enqueue_style(
                'bootstrap-min',
                (
                    file_exists($this->path.'css/bootstrap.min.css') ?
                    $this->url.'css/bootstrap.min.css' :
                    'https://cdn.jsdelivr.net/npm/bootstrap@5.0.0-beta1/dist/css/bootstrap.min.css'
                ),
                [],
                '5.0.0-beta1'
            );
        
            wp_enqueue_style(
                'eps-output',
                $this->url.'css/output.css',
                [],
                '1.0.0'
            );
        
            wp_enqueue_script(
                'bootstrap-bundle-min',
                (
                    file_exists($this->path.'js/bootstrap.bundle.min.js') ?
                    $this->url.'js/bootstrap.bundle.min.js' :
                    'https://cdn.jsdelivr.net/npm/bootstrap@5.0.0-beta1/dist/js/bootstrap.bundle.min.js'
                ),
                [],
                '5.0.0-beta1'
            );
        
            wp_enqueue_script(
                'eps-output',
                $this->url.'js/output.js',
                [],
                '1.0.1'
            );
        
        });

        add_filter('eps-metadata-datalist', function($content) {

            $metadata = new MetadataMatching($this->wpdb);

            $keys = $metadata->keysAll();

            ob_start();

            foreach ($keys as $key) {

?>
<option value="<?= htmlspecialchars($key) ?>">
<?php

            }

            $content = ob_get_clean();

            return $content;

        });

        if (isset($_POST['eps-download-init'])) {

            add_action('plugins_loaded', function() {

                if (wp_verify_nonce(
                    $_POST['eps-download-wpnp'],
                    'eps-download-nonce'
                ) === false) $this->adminStatusSet(
                    'danger',
                    'Произошла ошибка при отправке формы.'
                );
                else {

                    $spreadsheet_file = new SpreadsheetFile($this->path.'temp');

                    if (isset($_POST['eps-download-participants'])) {

                        $participants = new Participants(new Users($this->wpdb));

                        $spreadsheet_file->worksheetAdd(
                            $participants->worksheetGet(
                                $spreadsheet_file->spreadsheetGet(),
                                'Участники',
                                $spreadsheet_file->usersDataGet()
                            )
                        );

                        $spreadsheet_file->usersDataSet($participants->usersDataGet());

                    }

                    if (isset($_POST['eps-download-demography'])) {

                        $demography = new Demography(new Users($this->wpdb));

                        $spreadsheet_file->worksheetAdd(
                            $demography->worksheetGet(
                                $spreadsheet_file->spreadsheetGet(),
                                'Демография'
                            )
                        );

                    }

                    if (isset($_POST['eps-download-visits'])) {

                        $attendance = new Attendance(
                            new Visits($this->wpdb),
                            new Users($this->wpdb)
                        );

                        $spreadsheet_file->worksheetAdd(
                            $attendance->worksheetGet(
                                $spreadsheet_file->spreadsheetGet(),
                                'Посещения',
                                $spreadsheet_file->usersDataGet()
                            )
                        );

                        $spreadsheet_file->usersDataSet($attendance->usersDataGet());

                    }

                    if (isset($_POST['eps-download-nmo-titles'])) {

                        $titles_worksheet = new TitlesWorksheet(new Titles($this->wpdb));

                        $spreadsheet_file->worksheetAdd(
                            $titles_worksheet->worksheetGet(
                                $spreadsheet_file->spreadsheetGet(),
                                'Программа'
                            )
                        );

                        $presence_effect = new PresenceEffect(
                            new PresenceTimes($this->wpdb)
                        );

                        $spreadsheet_file->worksheetAdd(
                            $presence_effect->worksheetGet(
                                $spreadsheet_file->spreadsheetGet(),
                                'НМО',
                                $spreadsheet_file->usersDataGet(),
                                $presence_effect::WORKSHEET_MODE_TITLES
                            )
                        );

                        $spreadsheet_file->usersDataSet($presence_effect->usersDataGet());

                    }

                    if (isset($_POST['eps-download-nmo-raw'])) {

                        if (!($presence_effect instanceof PresenceEffect)) $presence_effect = new PresenceEffect(
                            new PresenceTimes($this->wpdb)
                        );

                        $spreadsheet_file->worksheetAdd(
                            $presence_effect->worksheetGet(
                                $spreadsheet_file->spreadsheetGet(),
                                'НМО (детализация)',
                                $spreadsheet_file->usersDataGet(),
                                $presence_effect::WORKSHEET_MODE_RAW
                            )
                        );

                        $spreadsheet_file->usersDataSet($presence_effect->usersDataGet());

                    }

                    try {

                        $spreadsheet_file->spreadsheetSave();

                        $filedata = $spreadsheet_file->fileGetUnlink();

                        if (empty($filedata)) $this->adminStatusSet(
                            'danger',
                            'Произошла неизвестная ошибка.'
                        );
                        else {

                            date_default_timezone_set('Europe/Moscow');

                            header('Content-type: application; charset=utf-8');
                            header('Content-disposition: attachment; filename=Statistics_'.date("Y-m-d_H-i-s").'.xlsx');

                            echo $filedata;

                            die;

                        }

                    } catch (HandlerException $e) {}
                    catch (SpreadsheetFileException $e) {}

                    if (isset($e)) $this->adminStatusSet(
                        'danger',
                        'Ошибка, код '.$e->getCode().': "'.$e->getMessage().'"'
                    );

                }

            });

        }

    }

}
