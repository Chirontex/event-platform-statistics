<?php
/**
 * Plugin Name: Event Platform Statistics
 * Plugin URI: https://github.com/drnoisier/event-platform-statistics
 * Description: Плагин, реализующий сбор статистики на площадке мероприятий.
 * Version: 1.01
 * Author: Дмитрий Шумилин
 * Author URI: mailto://ds@brandpro.ru
 */
use EPStatistics\Main;

require_once __DIR__.'/event-platform-statistics-autoload.php';

if (!file_exists(__DIR__.'/vendor/autoload.php')) wp_die('Event Platform Statistics: отсутствуют необходимые модули.');

require_once __DIR__.'/vendor/autoload.php';

$eps_admin_status = '';
$eps_titles_tbody = '';

new Main(
    plugin_dir_path(__FILE__),
    plugin_dir_url(__FILE__)
);
