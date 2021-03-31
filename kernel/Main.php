<?php
/**
 * Event Platform Statistics
 */
namespace EPStatistics;

use EPStatistics\Handlers\PresenceEffect;
use EPStatistics\Exceptions\TitlesException;
use EPStatistics\Exceptions\DetachedButtonsException;

final class Main extends MainCluster
{

    private $output_script_file;
    private $titles_script_file;
    private $detached_buttons_script_file;

    public function __construct(string $path, string $url)
    {

        parent::__construct($path, $url);

        $this->output_script_file = 'event-platform-statistics-output.php';
        $this->titles_script_file = 'event-platform-statistics-titles.php';
        $this->detached_buttons_script_file = 'event-platform-statistics-detached-buttons.php';

        $this->apiRoutesInit();

        $main_api_token = new MainApiToken($this->path, $this->url);

        $main_api_token->apiTokenGet();
        $main_api_token->apiTokenRemove();

        $this->clientJSInit();
        $this->shortcodesInit();
        $this->adminMenuInit();

        $this->visitWrite();

        if (strpos(
                $_SERVER['REQUEST_URI'],
                'wp-admin'
            ) !== false &&
            strpos(
                $_GET['page'],
                $this->output_script_file
            ) !== false) {

            $main_download = new MainDownload($this->path, $this->url);

            $main_download->downloadInit();

        }

        if (strpos(
                $_SERVER['REQUEST_URI'],
                'wp-admin'
            ) !== false &&
            strpos(
                $_GET['page'],
                $this->titles_script_file
            ) !== false) {

            $main_titles = new MainTitles($this->path, $this->url);

            if (isset($_POST['eps-titles-header']) &&
                isset($_POST['eps-titles-list']) &&
                isset($_POST['eps-titles-start-date']) &&
                isset($_POST['eps-titles-start-time']) &&
                isset($_POST['eps-titles-end-date']) &&
                isset($_POST['eps-titles-end-time'])) $main_titles->titleAdd();

            if (isset($_POST['eps-titles-title-update'])) $main_titles->titleUpdate();

            if (isset($_POST['eps-titles-title-delete'])) $main_titles->titleDelete();

            $main_titles->titlesOutput();

        }

        if (strpos(
                $_SERVER['REQUEST_URI'],
                'wp-admin'
            ) !== false &&
            strpos(
                $_GET['page'],
                $this->detached_buttons_script_file
            ) !== false) {

            $main_detached_buttons = new MainDetachedButtons(
                $this->path,
                $this->url
            );

            if (isset(
                    $_POST['eps-detached-buttons-add-button-id']
                ) &&
                isset(
                    $_POST['eps-detached-buttons-add-date']
                ) &&
                isset(
                    $_POST['eps-detached-buttons-add-time']
            )) $main_detached_buttons->entryAdd();

            if (isset(
                    $_POST['eps-detached-button-update-entry-id']
                ) &&
                isset(
                    $_POST['eps-detached-button-update-button-id']
                ) &&
                isset(
                    $_POST['eps-detached-button-update-date']
                ) &&
                isset(
                    $_POST['eps-detached-button-update-time']
            )) $main_detached_buttons->entryUpdate();

            if (isset(
                $_POST['eps-detached-button-delete-entry-id']
            )) $main_detached_buttons->entryDelete();

            $main_detached_buttons->filterEntries();

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

            add_menu_page(
                'Отд. кнопки',
                'Отд. кнопки',
                8,
                $this->path.$this->detached_buttons_script_file
            );

        });

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
                '/presence-time/get-detached-button',
                [
                    'methods' => ['GET', 'POST'],
                    'callback' => function() {

                        date_default_timezone_set('Europe/Moscow');

                        $result = [];

                        if (isset($_REQUEST['buttonid'])) {

                            $button_id = trim($_REQUEST['buttonid']);

                            try {

                                $detached_buttons = new DetachedButtons($this->wpdb);

                                $select = $detached_buttons->selectButton($button_id);

                                if (empty($select)) $result = [
                                    'code' => 1,
                                    'message' => 'The answer is empty.'
                                ];
                                else {

                                    $enable = false;

                                    $dif = time() - strtotime($select[0]);

                                    if ($dif > 0 && $dif <= 60) $enable = true;

                                    $result = [
                                        'code' => 0,
                                        'message' => 'Success.',
                                        'data' => [
                                            'enable' => $enable ? 'yes' : 'no',
                                            'datetime' => $select[0]
                                        ]
                                    ];

                                }

                            } catch (DetachedButtonsException $e) {

                                $result = [
                                    'code' => $e->getCode(),
                                    'message' => $e->getMessage()
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
                                else {

                                    $title = $select[0]['title'];

                                    $tags = [
                                        '<br>', '<br />', '<br/>',
                                        '<b>', '</b>',
                                        '<i>', '</i>',
                                        '<u>', '</u>'
                                    ];
                    
                                    $placeholders = [
                                        '!!%EPS_PH_BR1%!!', '!!%EPS_PH_BR2%!!', '!!%EPS_PH_BR_3%!!',
                                        '!!%EPS_B_OPEN%!!', '!!%EPS_B_CLOSE%!!',
                                        '!!%EPS_I_OPEN%!!', '!!%EPS_I_CLOSE%!!',
                                        '!!%EPS_U_OPEN%!!', '!!%EPS_U_CLOSE%!!'
                                    ];

                                    $title = str_replace(
                                        $tags,
                                        $placeholders,
                                        $title
                                    );

                                    $title = htmlspecialchars($title);
                                    
                                    $result = [
                                        'code' => 0,
                                        'message' => 'Success.',
                                        'data' => [
                                            'title' => $title,
                                            'nmo' => (int)$select[0]['nmo']
                                        ]
                                    ];
                            
                                }

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

    private function shortcodesInit() : void
    {

        add_shortcode('eps-presence-effect-button', function($atts, $content) {

            $atts = shortcode_atts([
                'list' => 'Общий',
                'titles-detach' => 'no',
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
<button type="button" eps-peb-list="<?= $atts['titles-detach'] === 'no' ? htmlspecialchars($atts['list']) : '' ?>" name="eps-presence-effect-button" id="<?= htmlspecialchars($atts['id']) ?>" class="<?= htmlspecialchars($atts['button-class']) ?>" style="<?= htmlspecialchars($atts['button-style']) ?>" onclick="epsPresenceConfirmationSend('<?= $atts['message-position'] ?>', '<?= htmlspecialchars($atts['message-class']) ?>', '<?= htmlspecialchars($atts['message-style']) ?>', '<?= htmlspecialchars($atts['id']) ?>', '<?= htmlspecialchars($atts['list']) ?>');"><?= htmlspecialchars($content) ?></button>
<script>
window.eps_button_text_default['<?= htmlspecialchars($atts['id']) ?>'] = '<?= htmlspecialchars($content) ?>'
</script>
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
                '2.0.2'
            );

            wp_enqueue_script(
                'eps-titles-client',
                $this->url.'js/titles-client.js',
                [],
                '2.1.4'
            );

        });

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
