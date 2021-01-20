<?php
/**
 * Event Platform Statistics
 */
namespace EPStatistics;

use EPStatistics\Handlers\Participants;
use EPStatistics\Exceptions\ParticipantsException;

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

        $this->adminPageInit();

        if (strpos($_GET['page'], $this->admin_script_file) !== false) {
            
            $this->downloadParticipantsInit();
        
        }

    }

    private function adminPageInit() : void
    {

        add_action('admin_menu', function() {

            add_menu_page(
                'Статистика платформы',
                'Статистика платформы',
                8,
                $this->path.$this->admin_script_file
            );

        });

    }

    private function downloadParticipantsInit() : void
    {

        if (isset($_POST['eps-download-participants'])) {

            $participants = new Participants(
                new Users($this->wpdb),
                $this->path
            );

            try {

                $filedata = $participants->getAll();

                if (empty($filedata)) $this->adminStatusSet(
                    'danger',
                    'Произошла неизвестная ошибка.'
                );
                else {

                    header('Content-type: application/vnd.ms-excel; charset=utf-8');
                    header('Content-disposition: attachment; filename=Participants.xlsx');

                    echo $filedata;

                    die;

                }

            } catch (ParticipantsException $e) {

                $this->adminStatusSet(
                    'danger',
                    'Ошибка, код '.$e->getCode().': "'.$e->getMessage().'"'
                );

            }

        }

    }

    private function adminStatusSet(string $alert_type, string $text) : void
    {

        ob_start();

?>
<div class="alert alert-<?= $alert_type ?> text-center mb-5 mx-auto" style="max-width: 300px;"><?= $text ?></div>
<?php

        $GLOBALS['eps_admin_status'] = ob_get_clean();

    }

}
