<?php
/**
 * Plugin Name: Event Platform Statistics
 * Plugin URI: https://github.com/drnoisier/event-platform-statistics
 * Description: Плагин, реализующий сбор статистики на площадке мероприятий.
 * Version: 0.03
 * Author: Дмитрий Шумилин
 * Author URI: mailto://ds@brandpro.ru
 */
use EPStatistics\Main;

require_once __DIR__.'/event-platform-statistics-autoload.php';

if (!file_exists(__DIR__.'/vendor/autoload.php')) exec('composer install');

require_once __DIR__.'/vendor/autoload.php';

global $wpdb;

if (!($wpdb instanceof wpdb)) $wpdb = new wpdb(DB_USER, DB_PASSWORD, DB_NAME, DB_HOST);

new Main(
    plugin_dir_path(__FILE__),
    plugin_dir_url(__FILE__),
    $wpdb
);
