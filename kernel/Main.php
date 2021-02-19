<?php
/**
 * Event Platform Statistics
 */
namespace EPStatistics;

use EPStatistics\Handlers\SpreadsheetFile;
use EPStatistics\Handlers\Participants;
use EPStatistics\Handlers\PresenceEffect;
use EPStatistics\Handlers\TitlesWorksheet;
use EPStatistics\Handlers\Demography;
use EPStatistics\Handlers\Attendance;
use EPStatistics\Exceptions\HandlerException;
use EPStatistics\Exceptions\SpreadsheetFileException;
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

        $this->clientJSInit();
        $this->shortcodesInit();
        $this->adminMenuInit();

        $this->visitWrite();

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

            if (isset($_POST['eps-titles-title-update'])) $this->titleUpdate();

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

        add_action('admin_enqueue_scripts', function() {

            wp_enqueue_style(
                'bootstrap-min',
                (
                    file_exists($this->path.'css/bootstrap.min.css') ?
                    $this->url.'css/bootstrap.min.css' :
                    'https://cdn.jsdelivr.net/npm/bootstrap@5.0.0-beta1/dist/css/bootstrap.min.css'
                ),
                [],
                '5.0.0-beta2'
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
                '5.0.0-beta2'
            );
        
            wp_enqueue_script(
                'eps-output',
                $this->url.'js/output.js',
                [],
                '1.0.0'
            );
        
        });

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
                        'Посещения'
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
                        'НМО (детализация)',
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

        add_action('wp_loaded', function() {

            $user_id = get_current_user_id();

            if ($user_id !== 0) {

                $tokens = new Tokens($this->wpdb);

                if ($tokens->userGetByToken(
                        (string)$_COOKIE['eps_api_token']
                    ) === 0) {

                    setcookie(
                        'eps_api_token',
                        $tokens->tokenGetGenerate($user_id),
                        0,
                        '/'
                    );

                }

            }

        });

    }

    private function apiTokenRemove() : void
    {

        add_action('clear_auth_cookie', function() {

            $user_id = get_current_user_id();

            $tokens = new Tokens($this->wpdb);

            if ($user_id !== 0) $tokens->tokenDeleteByUser($user_id);

            if (!empty($_COOKIE['eps_api_token'])) {

                $tokens->tokenDelete((string)$_COOKIE['eps_api_token']);

                setcookie('eps_api_token', '', 0, '/');

            }

        });

    }

    private function shortcodesInit() : void
    {

        add_shortcode('eps-presence-effect-button', function($atts, $content) {

            $atts = shortcode_atts([
                'list' => 'Общий',
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
<button type="button" id="<?= htmlspecialchars($atts['id']) ?>" class="<?= htmlspecialchars($atts['button-class']) ?>" style="<?= htmlspecialchars($atts['button-style']) ?>" onclick="epsPresenceConfirmationSend('<?= $atts['message-position'] ?>', '<?= htmlspecialchars($atts['message-class']) ?>', '<?= htmlspecialchars($atts['message-style']) ?>', '<?= htmlspecialchars($atts['id']) ?>', '<?= htmlspecialchars($atts['list']) ?>');"><?= htmlspecialchars($content) ?></button>
<?php

            return ob_get_clean();

        });

        add_shortcode('eps-title', function($atts, $content) {

            $atts = shortcode_atts([
                'list' => 'Общий',
                'tag' => 'h3',
                'id' => 'eps-title',
                'class' => '',
                'style' => ''
            ], $atts);

            if (empty($content)) $content = 'В данный момент ничего не происходит.';

            ob_start();

?>
<<?= htmlspecialchars($atts['tag']) ?> id="<?= htmlspecialchars($atts['id']) ?>" class="<?= htmlspecialchars($atts['class']) ?>" style="<?= htmlspecialchars($atts['style']) ?>"><?= htmlspecialchars($content) ?></<?= htmlspecialchars($atts['tag']) ?>>
<?php

            $output = ob_get_clean();

            ob_start();

?>
<script>
epsTitleGet('<?= $atts['id'] ?>', '<?= $atts['list'] ?>');
</script>
<?php

            return $output.ob_get_clean();

        });

    }

    private function clientJSInit() : void
    {

        add_action('wp_enqueue_scripts', function() {

            wp_enqueue_script(
                'eps-presence-effect',
                $this->url.'js/presence-effect-button.js',
                [],
                '2.0.0',
                true
            );

            wp_enqueue_script(
                'eps-titles-client',
                $this->url.'js/titles-client.js',
                [],
                '2.0.0',
                true
            );

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
                    trim($_POST['eps-titles-header']),
                    trim($_POST['eps-titles-list']),
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

        add_action('admin_enqueue_scripts', function() {

            wp_enqueue_style(
                'bootstrap-min',
                (
                    file_exists($this->path.'css/bootstrap.min.css') ?
                    $this->url.'css/bootstrap.min.css' :
                    'https://cdn.jsdelivr.net/npm/bootstrap@5.0.0-beta1/dist/css/bootstrap.min.css'
                ),
                [],
                '5.0.0-beta2'
            );
        
            wp_enqueue_style(
                'eps-titles',
                $this->url.'css/titles.css',
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
                '5.0.0-beta2'
            );
        
            wp_enqueue_script(
                'eps-titles',
                $this->url.'js/titles.js',
                [],
                '1.0.0'
            );
        
        });

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
<tr id="eps-title-<?= $title['id'] ?>">
    <td><?= $title['id'] ?></td>
    <td id="eps-title-title-<?= $title['id'] ?>"><?= htmlspecialchars($title['title']) ?></td>
    <td id="eps-title-list-name-<?= $title['id'] ?>"><?= htmlspecialchars($title['list_name']) ?></td>
    <td id="eps-title-datetime-start-<?= $title['id'] ?>"><?= date("d.m.Y H:i", strtotime($title['datetime_start'])) ?></td>
    <td id="eps-title-datetime-end-<?= $title['id'] ?>"><?= date("d.m.Y H:i", strtotime($title['datetime_end'])) ?></td>
    <td id="eps-title-nmo-<?= $title['id'] ?>"><?= $title['nmo'] === '1' ? 'Да' : 'Нет' ?></td>
    <td id="eps-title-update-button-<?= $title['id'] ?>"><a href="javascript:void(0)" onclick="epsTitlesUpdate(<?= $title['id'] ?>);">Редактировать</a></td>
    <td><a href="javascript:void(0)" onclick="epsTitlesDelete(<?= $title['id'] ?>);">Удалить</a></td>
</tr>
<?php

            }

            $GLOBALS['eps_titles_tbody'] = ob_get_clean();

        }

    }

    private function titleUpdate() : void
    {

        $titles = new Titles($this->wpdb);

        $timestamp_start = strtotime(
            $_POST['eps-titles-title-update-date-start'].' '.$_POST['eps-titles-title-update-time-start']
        );

        $timestamp_end = strtotime(
            $_POST['eps-titles-title-update-date-end'].' '.$_POST['eps-titles-title-update-time-end']
        );

        if ($timestamp_start >= $timestamp_end) $this->adminStatusSet(
            'danger',
            'Дата и время были указаны некорректно.'
        );
        else {

            try {

                if ($titles->titleUpdate(
                    (int)$_POST['eps-titles-title-update'],
                    trim($_POST['eps-titles-title-update-title']),
                    trim($_POST['eps-titles-title-update-list-name']),
                    $timestamp_start,
                    $timestamp_end,
                    (int)$_POST['eps-titles-title-update-nmo']
                )) $this->adminStatusSet('success', 'Элемент программы успешно отредактирован!');
                else $this->adminStatusSet('danger', 'Не удалось отредактировать элемент программы.');

            } catch (TitlesException $e) {

                $this->adminStatusSet(
                    'danger',
                    'Ошибка редактирования, код '.$e->getCode().': "'.$e->getMessage().'"'
                );

            }

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

    private function visitWrite() : void
    {

        add_action('template_redirect', function() {

            $user_id = get_current_user_id();

            $response_code = http_response_code();

            if ($user_id !== 0) {

                if (strpos($_SERVER['REQUEST_URI'], 'wp-admin') === false &&
                    strpos($_SERVER['REQUEST_URI'], 'wp-content') === false &&
                    strpos($_SERVER['REQUEST_URI'], 'wp-includes') === false &&
                    strpos($_SERVER['REQUEST_URI'], 'wp-json') === false &&
                    strpos($_SERVER['REQUEST_URI'], 'wp-login') === false &&
                    strpos($_SERVER['REQUEST_URI'], 'favicon.ico') === false &&
                    strpos($_SERVER['REQUEST_URI'], 'preview=true') === false &&
                    (int)$response_code < 400) {

                    $visits = new Visits($this->wpdb);

                    $visits->addVisit(
                        (empty($_SERVER['HTTPS']) ? 'http' : 'https').
                        '://'.$_SERVER['HTTP_HOST'].$_SERVER['REQUEST_URI'],
                        $user_id
                    );

                }

            }

        });

    }

}
