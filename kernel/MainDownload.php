<?php
/**
 * @package Event Platform Statistics
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

/**
 * POE which initiates statistics downloading.
 * @since 1.9.11
 * @final
 */
final class MainDownload extends AdminPage
{

    /**
     * @var MetadataMatching $metadata
     * @since 1.9.11
     */
    protected $metadata;

    /**
     * @since 1.9.11
     */
    protected function init() : self
    {

        date_default_timezone_set("Europe/Moscow");

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
                '1.0.2'
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
                '1.2.2'
            );
        
        });

        add_filter('eps-metadata-datalist', function() {

            return $this->metadata->keysAll();

        });

        add_filter('eps-metadata-tbody', function() {

            return $this->metadata->matchesAll();

        });

        $this->metadata = new MetadataMatching($this->wpdb);

        if (isset($_POST['eps-download-init'])) $this->download();
        elseif (isset($_POST['eps-metadata-add-name']) &&
            isset($_POST['eps-metadata-add-key'])) $this->metadataAdd();
        elseif (isset($_POST['eps-metadata-update'])) $this->metadataUpdate();
        elseif (isset($_POST['eps-metadata-delete'])) $this->metadataDelete();

        return $this;

    }

    /**
     * Fires statistics download.
     * @since 1.9.11
     * 
     * @return $this
     */
    protected function download() : self
    {

        add_action('plugins_loaded', function() {

            if (wp_verify_nonce(
                $_POST['eps-download-wpnp'],
                'eps-download-nonce'
            ) === false) $this->adminPageNotice(
                'danger',
                'Произошла ошибка при отправке формы.'
            );
            else {

                $spreadsheet_file = new SpreadsheetFile($this->path.'temp');

                if (isset($_POST['eps-download-participants'])) {

                    $participants = new Participants(
                        new Users($this->wpdb),
                        new Visits($this->wpdb)
                    );

                    if (isset($_POST['eps-download-nmo-trick'])) $participants->defineNmoTrick(true);

                    if (isset($_POST['eps-download-attendance-days'])) {

                        $attendance_days = explode(';', $_POST['eps-download-attendance-days']);

                        foreach ($attendance_days as $day) {

                            $day = explode('=>', trim($day));

                            $participants->addAttendanceDay(
                                strtotime(trim($day[0])),
                                strtotime(trim($day[1]))
                            );

                        }

                    }

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

                    $titles_worksheet = new TitlesWorksheet(
                        new Titles($this->wpdb),
                        new Visits($this->wpdb)
                    );

                    $spreadsheet_file->worksheetAdd(
                        $titles_worksheet->worksheetGet(
                            $spreadsheet_file->spreadsheetGet(),
                            'Программа',
                            isset($_POST['eps-download-url-matching']) ?
                                trim($_POST['eps-download-url-matching']) : ''
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

                    if (empty($filedata)) $this->adminPageNotice(
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

                if (isset($e)) $this->adminPageNotice(
                    'danger',
                    'Ошибка, код '.$e->getCode().': "'.$e->getMessage().'"'
                );

            }

        });

        return $this;

    }

    /**
     * Add metadata matching.
     * @since 1.9.11
     * 
     * @return $this
     */
    protected function metadataAdd() : self
    {

        add_action('plugins_loaded', function() {

            if (wp_verify_nonce(
                $_POST['eps-metadata-wpnp'],
                'eps-metadata-add'
            ) === false) $this->adminPageNotice(
                'danger',
                'Произошла ошибка отправки формы.'
            );
            else {

                try {

                    $this->metadata->matchAdd(
                        trim($_POST['eps-metadata-add-name']),
                        trim($_POST['eps-metadata-add-key'])
                    );

                    $this->adminPageNotice(
                        'success',
                        'Сопоставление метаданных успешно добавлено!'
                    );

                } catch (MetadataMatchingException $e) {

                    $this->adminPageNotice(
                        'danger',
                        'Ошибка, код '.$e->getCode().': "'.$e->getMessage().'"'
                    );

                }

            }

        });

        return $this;

    }

    /**
     * Update metadata matching.
     * @since 1.9.11
     * 
     * @return $this
     */
    protected function metadataUpdate() : self
    {

        add_action('plugins_loaded', function() {

            if (wp_verify_nonce(
                $_POST['eps-metadata-update-wpnp'],
                'eps-metadata-update'
            ) === false) $this->adminPageNotice(
                'danger',
                'Произошла ошибка при отправке формы.'
            );
            else {

                try {

                    $this->metadata->matchUpdate(
                        (int)$_POST['eps-metadata-update'],
                        trim($_POST['eps-metadata-update-name']),
                        trim($_POST['eps-metadata-update-key']),
                        (int)$_POST['eps-metadata-update-pn'],
                        (int)$_POST['eps-metadata-update-include']
                    );

                    $this->adminPageNotice(
                        'success',
                        'Сопоставление метаданных успешно изменено!'
                    );

                } catch (MetadataMatchingException $e) {

                    $this->adminPageNotice(
                        'danger',
                        'Ошибка, код '.$e->getCode().': "'.$e->getMessage().'"'
                    );

                }

            }

        });

        return $this;

    }

    /**
     * Delete metadata matching.
     * @since 1.9.11
     * 
     * @return $this
     */
    protected function metadataDelete() : self
    {

        add_action('plugins_loaded', function() {

            if (wp_verify_nonce(
                $_POST['eps-metadata-delete-wpnp'],
                'eps-metadata-delete'
            ) === false) $this->adminPageNotice(
                'danger',
                'Произошла ошибка при отправке формы.'
            );
            else {

                try {

                    $this->metadata->matchDelete(
                        (int)$_POST['eps-metadata-delete']
                    );

                    $this->adminPageNotice(
                        'success',
                        'Сопоставление метаданных успешно удалено!'
                    );

                } catch (MetadataMatchingException $e) {

                    $this->adminPageNotice(
                        'danger',
                        'Ошибка, код '.$e->getCode().': "'.$e->getMessage().'"'
                    );

                }

            }

        });

        return $this;

    }

}
