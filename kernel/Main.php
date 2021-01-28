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
use EPStatistics\Exceptions\TokensException;
use EPStatistics\Exceptions\TitlesException;

final class Main
{

    private $path;
    private $url;
    private $wpdb;
    private $output_script_file;
    private $titles_script_file;

    public function __construct(string $path, string $url)
    {

        global $wpdb;

        $this->wpdb = $wpdb;
        
        $this->path = $path;
        $this->url = $url;

        $this->output_script_file = 'event-platform-statistics-output.php';
        $this->titles_script_file = 'event-platform-statistics-titles.php';

        $this->apiRoutesInit();

        $this->apiTokenGet();
        $this->apiTokenRemove();

        $this->shortcodeInit();
        $this->adminMenuInit();

        if (strpos(
                $_GET['page'],
                $this->output_script_file
            ) !== false) $this->downloadInit();

        if (strpos(
                $_GET['page'],
                $this->titles_script_file
            ) !== false) {

            if (isset($_POST['eps-titles-header']) &&
                isset($_POST['eps-titles-list']) &&
                isset($_POST['eps-titles-start-date']) &&
                isset($_POST['eps-titles-start-time']) &&
                isset($_POST['eps-titles-end-date']) &&
                isset($_POST['eps-titles-end-time'])) $this->titleAdd();

            if (isset($_POST['eps-titles-title-delete'])) $this->titleDelete();

            $this->titlesOutput();

        }

    }

    private function adminMenuInit() : void
    {

        add_action('admin_menu', function() {

            add_menu_page(
                'Статистика',
                'Статистика',
                8,
                $this->path.$this->output_script_file
            );

            add_menu_page(
                'Титры',
                'Титры',
                8,
                $this->path.$this->titles_script_file
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

            if (isset($_POST['eps-download-nmo-raw'])) {

                $presence_effect = new PresenceEffect(
                    new PresenceTimes($this->wpdb)
                );

                $spreadsheet_file->worksheetAdd(
                    $presence_effect->worksheetGet(
                        $spreadsheet_file->spreadsheetGet(),
                        'НМО (перечень)'
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

    private function apiTokenGet() : void
    {

        add_action('wp_login', function($user_login, $user) {

            $tokens = new Tokens($this->wpdb);

            try {

                $token = $tokens->tokenGetGenerate($user->ID);

            } catch (TokensException $e) {

                wp_die('Event Platform Statistics error, '.$e->getCode().': '.$e->getMessage());

            }

            setcookie('eps_api_token', $token);

        }, 10, 2);

    }

    private function apiTokenRemove() : void
    {

        add_action('wp_logout', function() {

            $tokens = new Tokens($this->wpdb);

            $tokens->tokenDelete($_COOKIE['eps_api_token']);

        });

    }

    private function shortcodeInit() : void
    {

        add_shortcode('eps-presence-effect-button', function($atts, $content) {

            $atts = shortcode_atts([
                'button-class' => '',
                'button-style' => '',
                'message-position' => 'after',
                'message-class' => '',
                'message-style' => ''
            ], $atts);

            if ($atts['message-position'] !== 'before' &&
                $atts['message-position'] !== 'after') $atts['message'] = 'after';

            if (empty($content)) $content = 'Подтвердите Ваше присутствие';

            ob_start();

?>
<button type="button" id="eps-presence-effect-button" class="<?= htmlspecialchars($atts['button-class']) ?>" style="<?= htmlspecialchars($atts['button-style']) ?>" onclick="epsPresenceConfirmationSend('<?= $atts['message-position'] ?>', '<?= htmlspecialchars($atts['message-class']) ?>', '<?= htmlspecialchars($atts['message-style']) ?>');"><?= htmlspecialchars($content) ?></button>
<script src="<?= $this->url ?>js/presence-effect-button.js"></script>
<script>
if (!window.jQuery)
{
    let eps_jquery_init = document.createElement('script');
    eps_jquery_init.setAttribute('src', '<?= file_exists($this->path.'js/jquery-3.5.1.min.js') ? $this->url.'js' : 'https://code.jquery.com' ?>/jquery-3.5.1.min.js');

    document.getElementById('eps-presence-effect-button').parentNode.insertBefore(eps_jquery_init, document.getElementById('eps-presence-effect-button'));
}
</script>
<?php

            return ob_get_clean();

        });

    }

    private function titleAdd() : void
    {

        $timestamp_start = strtotime(
            $_POST['eps-titles-start-date'].' '.$_POST['eps-titles-start-time']
        );

        $timestamp_end = strtotime(
            $_POST['eps-titles-end-date'].' '.$_POST['eps-titles-end-time']
        );

        if (isset($_POST['eps-titles-nmo'])) $nmo = 1;
        else $nmo = 0;

        if ($timestamp_start === false ||
            $timestamp_end === false) $this->adminStatusSet(
                'danger',
                'Дата и время были указаны некорректно.'
            );
        else {

            $titles = new Titles($this->wpdb);

            try {

                $add = $titles->titleAdd(
                    $_POST['eps-titles-header'],
                    $_POST['eps-titles-list'],
                    $timestamp_start,
                    $timestamp_end,
                    $nmo
                );

                if ($add) $this->adminStatusSet(
                    'success',
                    'Элемент программы успешно сохранён!'
                );
                else $this->adminStatusSet(
                    'danger',
                    'Не удалось сохранить элемент программы.'
                );

            } catch (TitlesException $e) {

                $this->adminStatusSet(
                    'danger',
                    'Ошибка, код '.$e->getCode().': "'.$e->getMessage().'"'
                );

            }

        }

    }

    private function titlesOutput() : void
    {

        $titles = new Titles($this->wpdb);

        try {

            $select = $titles->selectTitles();

        } catch (TitlesException $e) {

            $this->adminStatusSet(
                'danger',
                'Ошибка загрузки титров, '.$e->getCode().': "'.$e->getMessage().'"'
            );

        }

        if (!isset($e) && !empty($select)) {

            ob_start();

            foreach ($select as $title) {

?>
<tr>
    <td><?= htmlspecialchars($title['title']) ?></td>
    <td><?= htmlspecialchars($title['list_name']) ?></td>
    <td><?= $title['datetime_start'] ?></td>
    <td><?= $title['datetime_end'] ?></td>
    <td><?= $title['nmo'] === '1' ? 'Да' : 'Нет' ?></td>
    <td><a href="javascript:void(0)" onclick="epsTitlesDelete(<?= $title['id'] ?>);">Удалить</a></td>
</tr>
<?php

            }

            $GLOBALS['eps_titles_tbody'] = ob_get_clean();

        }

    }

    private function titleDelete() : void
    {

        $titles = new Titles($this->wpdb);

        try {

            $delete = $titles->titleDelete((int)$_POST['eps-titles-title-delete']);

        } catch (TitlesException $e) {

            $this->adminStatusSet(
                'danger',
                'Ошибка удаления элемента программы, код '.$e->getCode().': "'.$e->getMessage().'"'
            );

        }

        if (!isset($e)) {

            if ($delete) $this->adminStatusSet(
                'success',
                'Элемент программы успешно удалён!'
            );
            else $this->adminStatusSet(
                'danger',
                'Не удалось удалить элемент программы'
            );

        }

    }

}
