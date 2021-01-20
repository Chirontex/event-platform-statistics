<?php
/**
 * Event Platform Statistics
 */
namespace EPStatistics;

final class Main
{

    private $path;
    private $url;
    private $wpdb;

    public function __construct(string $path, string $url)
    {

        global $wpdb;

        $this->wpdb = $wpdb;
        
        $this->path = $path;
        $this->url = $url;

        $this->adminPageInit();

    }

    private function adminPageInit() : void
    {

        add_action('admin_menu', function() {

            add_menu_page(
                'Статистика платформы',
                'Статистика платформы',
                8,
                $this->path.'event-platform-statistics-admin.php'
            );

        });

    }

}
