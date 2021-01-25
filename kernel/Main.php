<?php
/**
 * Event Platform Statistics
 */
namespace EPStatistics;

use EPStatistics\Handlers\SpreadsheetFile;
use EPStatistics\Handlers\Participants;
use EPStatistics\Handlers\PresenceEffect;
use EPStatistics\Exceptions\HandlerException;
use EPStatistics\Exceptions\SpreadsheetFileException;

final class Main
{

    private $path;
    private $url;
    private $wpdb;
    private $admin_script_file;

    public function __construct(string $path, string $url)
    {

        global $wpdb;

        $this->wpdb = $wpdb;
        
        $this->path = $path;
        $this->url = $url;

        $this->admin_script_file = 'event-platform-statistics-admin.php';

        $this->apiRoutesInit();
        $this->adminPageInit();

        if (strpos(
                $_GET['page'],
                $this->admin_script_file
            ) !== false) $this->downloadInit();

    }

    private function adminPageInit() : void
    {

        add_action('admin_menu', function() {

            add_menu_page(
                'Статистика',
                'Статистика',
                8,
                $this->path.$this->admin_script_file
            );

        });

    }

    private function downloadInit() : void
    {

        if (isset($_POST['eps-download-init'])) {

            $spreadsheet_file = new SpreadsheetFile($this->path.'temp');

            if (isset($_POST['eps-download-participants'])) {

                $participants = new Participants(new Users($this->wpdb));

                $spreadsheet_file->worksheetAdd(
                    $participants->worksheetGet(
                        $spreadsheet_file->spreadsheetGet(),
                        'Участники'
                    )
                );

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

                    header('Content-type: application/vnd.ms-excel; charset=utf-8');
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

    }

    private function adminStatusSet(string $alert_type, string $text) : void
    {

        ob_start();

?>
<div class="alert alert-<?= $alert_type ?> text-center mb-5 mx-auto eps-column"><?= $text ?></div>
<?php

        $GLOBALS['eps_admin_status'] = ob_get_clean();

    }

    private function apiRoutesInit() : void
    {

        add_action('rest_api_init', function() {

            register_rest_route(
                'event-platform-statistics/v1',
                '/presence-time/add',
                [
                    'methods' => ['GET', 'POST'],
                    'callback' => function() {

                        $presence_effect = new PresenceEffect(
                            new PresenceTimes($this->wpdb)
                        );

                        return $presence_effect->apiAddPresenceTime();

                    },
                    'permission_callback' => function() {

                        return true;

                    }
                ]
            );

        });

    }

}
