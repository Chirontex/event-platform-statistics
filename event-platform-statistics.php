<?php
/**
 * Plugin Name: Event Platform Statistics
 * Plugin URI: https://github.com/chirontex/event-platform-statistics
 * Description: Плагин, реализующий сбор статистики на площадке мероприятий.
 * Version: 1.7.6
 * Author: Дмитрий Шумилин
 * Author URI: mailto://ds@brandpro.ru
 */
use EPStatistics\Main;

require_once __DIR__.'/event-platform-statistics-autoload.php';

if (!file_exists(__DIR__.'/vendor/autoload.php')) wp_die(
    'Event Platform Statistics: отсутствуют необходимые модули. Выполните команду "composer install" в папке плагина или обратитесь к администратору.'
);

require_once __DIR__.'/vendor/autoload.php';

if (!defined('ABSPATH')) die;

new Main(
    plugin_dir_path(__FILE__),
    plugin_dir_url(__FILE__)
);
