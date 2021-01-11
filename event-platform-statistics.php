<?php
/**
 * Plugin Name: Event Platform Statistics
 * Plugin URI: https://github.com/drnoisier/event-platform-statistics
 * Description: Плагин, реализующий сбор статистики на площадке мероприятий.
 * Version: 0.01
 * Author: Дмитрий Шумилин
 * Author URI: mailto://ds@brandpro.ru
 */
use EPStatistics\Main;

require_once __DIR__.'/autoload.php';

new Main(
    plugin_dir_path(__FILE__),
    plugin_dir_url(__FILE__)
);
