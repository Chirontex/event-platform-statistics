<?php
/**
 * @package Event Platform Statistics
 */
namespace EPStatistics;

use EPStatistics\Handlers\PresenceEffect;
use EPStatistics\Exceptions\TitlesException;
use EPStatistics\Exceptions\DetachedButtonsException;

/**
 * Main POE.
 * @since 1.9.11
 * 
 * @final
 */
final class Main extends MainCluster
{

    protected $output_admin_page = 'event-platform-statistics-output.php';
    protected $titles_admin_page = 'event-platform-statistics-titles.php';
    protected $detached_buttons_admin_page = 'event-platform-statistics-detached-buttons.php';

    /**
     * @since 1.9.17
     */
    protected function init() : self
    {

        new MainApiToken($this->path, $this->url);

        $this
            ->apiRoutesInit()
            ->clientJSInit()
            ->shortcodesInit()
            ->adminMenuInit()
            ->visitWrite();

        if (strpos(
                $_SERVER['REQUEST_URI'],
                'wp-admin'
            ) !== false &&
            strpos(
                $_GET['page'],
                $this->output_admin_page
            ) !== false) new MainDownload($this->path, $this->url);

        if (strpos(
                $_SERVER['REQUEST_URI'],
                'wp-admin'
            ) !== false &&
            strpos(
                $_GET['page'],
                $this->titles_admin_page
            ) !== false) new MainTitles($this->path, $this->url);

        if (strpos(
                $_SERVER['REQUEST_URI'],
                'wp-admin'
            ) !== false &&
            strpos(
                $_GET['page'],
                $this->detached_buttons_admin_page
            ) !== false) new MainDetachedButtons($this->path, $this->url);

        return $this;

    }

    /**
     * Add pages to admin menu.
     * @since 1.9.11
     * 
     * @return $this
     */
    protected function adminMenuInit() : self
    {

        add_action('admin_menu', function() {

            add_menu_page(
                'Статистика',
                'Статистика',
                8,
                $this->path.$this->output_admin_page
            );

            add_menu_page(
                'Титры',
                'Титры',
                8,
                $this->path.$this->titles_admin_page
            );

            add_menu_page(
                'Отд. кнопки',
                'Отд. кнопки',
                8,
                $this->path.$this->detached_buttons_admin_page
            );

        });

        return $this;

    }

    /**
     * Add API routes.
     * @since 1.9.11
     * 
     * @return $this
     */
    protected function apiRoutesInit() : self
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

                                    foreach ($select as $datetime) {

                                        $dif = time() - strtotime($datetime);

                                        if ($dif > 0 && $dif <= 60) {

                                            $result = [
                                                'code' => 0,
                                                'message' => 'Success.',
                                                'datetime' => $datetime
                                            ];

                                            break;

                                        }

                                    }

                                    if (empty($result)) $result = [
                                        'code' => 2,
                                        'message' => 'No actual datetimes.'
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

        return $this;

    }

    /**
     * Add shortcodes.
     * @since 1.9.11
     * 
     * @return $this
     */
    protected function shortcodesInit() : self
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
<button type="button" eps-peb-list="<?= $atts['titles-detach'] === 'no' ? htmlspecialchars($atts['list']) : '' ?>" name="eps-presence-effect-button" id="<?= htmlspecialchars($atts['id']) ?>" class="<?= htmlspecialchars($atts['button-class']) ?>" style="<?= htmlspecialchars($atts['button-style']) ?>" onclick="epsPresenceConfirmationSend('<?= $atts['message-position'] ?>', '<?= htmlspecialchars($atts['message-class']) ?>', '<?= htmlspecialchars($atts['message-style']) ?>', '<?= htmlspecialchars($atts['id']) ?>', '<?= htmlspecialchars($atts['list']) ?>'); <?= $atts['titles-detach'] === 'no' ? '' : 'epsPresenceDetachedButtonSended(\''.htmlspecialchars($atts['id']).'\');' ?>"><?= htmlspecialchars($content) ?></button>
<script>
window.eps_button_text_default['<?= htmlspecialchars($atts['id']) ?>'] = '<?= htmlspecialchars($content) ?>';
<?php

            if ($atts['titles-detach'] !== 'no') {

?>

document.getElementById('<?= htmlspecialchars($atts['id']) ?>').setAttribute('disabled', 'true');
document.getElementById('<?= htmlspecialchars($atts['id']) ?>').innerHTML = 'Подтверждение не требуется';

epsPresenceDetachedButtonGet('<?= htmlspecialchars($atts['id']) ?>');
<?php

            }

?>
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

        return $this;

    }

    /**
     * Add client JS scripts to enqueue.
     * @since 1.9.11
     * 
     * @return $this
     */
    protected function clientJSInit() : self
    {

        add_action('wp_enqueue_scripts', function() {

            wp_enqueue_script(
                'eps-presence-effect',
                $this->url.'js/presence-effect-button.js',
                [],
                '2.5.0'
            );

            wp_enqueue_script(
                'eps-titles-client',
                $this->url.'js/titles-client.js',
                [],
                '2.5.0'
            );

        });

        return $this;

    }

    /**
     * Writing visits.
     * @since 1.9.11
     * 
     * @return $this
     */
    protected function visitWrite() : self
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

        return $this;

    }

}
