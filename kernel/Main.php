<?php
/**
 * Event Platform Statistics
 */
namespace EPStatistics;

use EPStatistics\Handlers\SpreadsheetFile;
use EPStatistics\Handlers\Participants;
use EPStatistics\Handlers\PresenceEffect;
use EPStatistics\Handlers\TitlesWorksheet;
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

        $this->shortcodesInit();
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
                        $presence_effect::WORKSHEET_MODE_TITLES
                    )
                );

            }

            if (isset($_POST['eps-download-nmo-raw'])) {

                if (!($presence_effect instanceof PresenceEffect)) $presence_effect = new PresenceEffect(
                    new PresenceTimes($this->wpdb)
                );

                $spreadsheet_file->worksheetAdd(
                    $presence_effect->worksheetGet(
                        $spreadsheet_file->spreadsheetGet(),
                        'НМО (перечень)',
                        $presence_effect::WORKSHEET_MODE_RAW
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

            register_rest_route(
                'event-platform-statistics/v1',
                '/titles/get-actual-title',
                [
                    'methods' => ['GET', 'POST'],
                    'callback' => function() {

                        $result = [];

                        if (isset($_REQUEST['list'])) {

                            $titles = new Titles($this->wpdb);

                            $list_name = trim($_REQUEST['list']);

                            try {

                                $select = $titles->selectTitles($list_name, true);

                            } catch (TitlesException $e) {

                                $result = [
                                    'code' => $e->getCode(),
                                    'message' => $e->getMessage()
                                ];

                            }

                            if (empty($result)) {

                                if (empty($select)) $result = [
                                    'code' => 1,
                                    'message' => 'The answer is empty.'
                                ];
                                else $result = [
                                    'code' => 0,
                                    'message' => 'Success.',
                                    'data' => htmlspecialchars($select[0]['title'])
                                ];

                            }

                        } else $result = [
                            'code' => -99,
                            'message' => 'Too few arguments for this request.'
                        ];

                        return $result;

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

    private function shortcodesInit() : void
    {

        add_shortcode('eps-presence-effect-button', function($atts, $content) {

            $atts = shortcode_atts([
                'button-class' => '',
                'button-style' => '',
                'message-position' => 'after',
                'id' => 'eps-presence-effect-button',
                'message-class' => '',
                'message-style' => ''
            ], $atts);

            if ($atts['message-position'] !== 'before' &&
                $atts['message-position'] !== 'after') $atts['message'] = 'after';

            if (empty($content)) $content = 'Подтвердите Ваше присутствие';

            ob_start();

?>
<button type="button" id="<?= htmlspecialchars($atts['id']) ?>" class="<?= htmlspecialchars($atts['button-class']) ?>" style="<?= htmlspecialchars($atts['button-style']) ?>" onclick="epsPresenceConfirmationSend('<?= $atts['message-position'] ?>', '<?= htmlspecialchars($atts['message-class']) ?>', '<?= htmlspecialchars($atts['message-style']) ?>', '<?= htmlspecialchars($atts['id']) ?>');"><?= htmlspecialchars($content) ?></button>
<script src="<?= $this->url ?>js/presence-effect-button.js"></script>
<?php

            return ob_get_clean().$this->jqueryInitCheck($atts['id']);

        });

        add_shortcode('eps-title', function($atts, $content) {

            $atts = shortcode_atts([
                'list' => '',
                'tag' => 'h3',
                'id' => 'eps-title',
                'class' => '',
                'style' => ''
            ], $atts);

            if (empty($content)) $content = 'В данный момент ничего не происходит.';

            ob_start();

?>
<<?= htmlspecialchars($atts['tag']) ?> id="<?= htmlspecialchars($atts['id']) ?>" class="<?= htmlspecialchars($atts['class']) ?>" style="<?= htmlspecialchars($atts['style']) ?>"><?= htmlspecialchars($content) ?></<?= htmlspecialchars($atts['tag']) ?>>
<script src="<?= file_exists($this->path.'js/jquery-3.5.1.min.js') ? $this->url.'js' : 'https://code.jquery.com' ?>/jquery-3.5.1.min.js"></script>
<script src="<?= $this->url ?>js/titles-client.js"></script>
<?php

            $output = ob_get_clean().$this->jqueryInitCheck($atts['id']);

            ob_start();

?>
<script>
epsTitleGet('<?= $atts['id'] ?>', '<?= $atts['list'] ?>');
</script>
<?php

            return $output.ob_get_clean();

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
            $timestamp_end === false ||
            $timestamp_start >= $timestamp_end) $this->adminStatusSet(
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

    private function jqueryInitCheck(string $element_id) : string
    {

        ob_start();

?>
<script>
(function() {

let jquery_url = '<?= file_exists($this->path.'js/jquery-3.5.1.min.js') ? $this->url.'js' : 'https://code.jquery.com' ?>/jquery-3.5.1.min.js';

let scripts = document.getElementsByTagName('script');

let jquery_loaded = false;

for (let i = 0; i < scripts.length; i++)
{
    if (scripts[i].hasAttribute('src'))
    {
        if (scripts[i].getAttribute('src') == jquery_url)
        {
            jquery_loaded = true;
            break;
        }
    }
}

if (!jquery_loaded)
{
    let jquery_init = document.createElement('script');
    jquery_init.setAttribute('src', jquery_url);

    let element_node = document.getElementById('<?= $element_id ?>');

    element_node.parentNode.insertBefore(jquery_init, element_node.nextSibling);
}

})
</script>
<?php

        return ob_get_clean();

    }

}
