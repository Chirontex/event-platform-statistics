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
use EPStatistics\Exceptions\MetadataMatchingException;

class MainDownload extends AdminPage
{

    protected $metadata;

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
                '1.2.1'
            );
        
        });

        $this->metadata = new MetadataMatching($this->wpdb);

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

        } elseif (isset($_POST['eps-metadata-add-name']) &&
                    isset($_POST['eps-metadata-add-key'])) {

            add_action('plugins_loaded', function() {

                if (wp_verify_nonce(
                    $_POST['eps-metadata-wpnp'],
                    'eps-metadata-add'
                ) === false) $this->adminStatusSet(
                    'danger',
                    'Произошла ошибка отправки формы.'
                );
                else {

                    try {

                        $this->metadata->matchAdd(
                            (string)$_POST['eps-metadata-add-name'],
                            (string)$_POST['eps-metadata-add-key']
                        );

                        $this->adminStatusSet(
                            'success',
                            'Сопоставление метаданных успешно добавлено!'
                        );

                    } catch (MetadataMatchingException $e) {

                        $this->adminStatusSet(
                            'danger',
                            'Ошибка, код '.$e->getCode().': "'.$e->getMessage().'"'
                        );

                    }

                }

            });

        } elseif (isset($_POST['eps-metadata-update'])) {

            add_action('plugins_loaded', function() {

                if (wp_verify_nonce(
                    $_POST['eps-metadata-update-wpnp'],
                    'eps-metadata-update'
                ) === false) $this->adminStatusSet(
                    'danger',
                    'Произошла ошибка при отправке формы.'
                );
                else {

                    try {

                        $this->metadata->matchUpdate(
                            (int)$_POST['eps-metadata-update'],
                            (string)$_POST['eps-metadata-update-name'],
                            (string)$_POST['eps-metadata-update-key'],
                            (int)$_POST['eps-metadata-update-pn'],
                            (int)$_POST['eps-metadata-update-include']
                        );

                        $this->adminStatusSet(
                            'success',
                            'Сопоставление метаданных успешно изменено!'
                        );

                    } catch (MetadataMatchingException $e) {

                        $this->adminStatusSet(
                            'danger',
                            'Ошибка, код '.$e->getCode().': "'.$e->getMessage().'"'
                        );

                    }

                }

            });

        } elseif (isset($_POST['eps-metadata-delete'])) {

            add_action('plugins_loaded', function() {

                if (wp_verify_nonce(
                    $_POST['eps-metadata-delete-wpnp'],
                    'eps-metadata-delete'
                ) === false) $this->adminStatusSet(
                    'danger',
                    'Произошла ошибка при отправке формы.'
                );
                else {

                    try {

                        $this->metadata->matchDelete(
                            (int)$_POST['eps-metadata-delete']
                        );

                        $this->adminStatusSet(
                            'success',
                            'Сопоставление метаданных успешно удалено!'
                        );

                    } catch (MetadataMatchingException $e) {

                        $this->adminStatusSet(
                            'danger',
                            'Ошибка, код '.$e->getCode().': "'.$e->getMessage().'"'
                        );

                    }

                }

            });

        }

        add_filter('eps-metadata-datalist', function($content) {

            $keys = $this->metadata->keysAll();

            ob_start();

            foreach ($keys as $key) {

?>
<option value="<?= htmlspecialchars($key) ?>">
<?php

            }

            $content = ob_get_clean();

            return $content;

        });

        add_filter('eps-metadata-tbody', function($content) {

            $matches = $this->metadata->matchesAll();

            ob_start();

            foreach ($matches as $match) {

?>
<tr id="eps-metadata-match-<?= $match['id'] ?>">
    <td id="eps-metadata-match-name-<?= $match['id'] ?>" style="text-align: center;"><?= htmlspecialchars($match['name']) ?></td>
    <td id="eps-metadata-match-key-<?= $match['id'] ?>" style="text-align: center;"><?= htmlspecialchars($match['key']) ?></td>
    <td id="eps-metadata-match-pn-<?= $match['id'] ?>" style="text-align: center;"><?= $match['periodic_number'] ?></td>
    <td id="eps-metadata-match-include-<?= $match['id'] ?>" style="text-align: center;"><?= (int)$match['include'] === 1 ? 'Да' : 'Нет' ?></td>
    <td id="eps-metadata-match-update-<?= $match['id'] ?>" style="text-align: center;"><a href="javascript:void(0)" onclick="epsMetadataMatchUpdate(<?= $match['id'] ?>);">Изменить</a></td>
    <td id="eps-metadata-match-delete-<?= $match['id'] ?>" style="text-align: center;"><a href="javascript:void(0)" onclick="epsMetadataMatchDelete(<?= $match['id'] ?>);">Удалить</a></td>
</tr>
<?php

            }

            $content = ob_get_clean();

            return $content;

        });

    }

}
