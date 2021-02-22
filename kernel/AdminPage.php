<?php
/**
 * Event Platform Statistics
 */
namespace EPStatistics;

class AdminPage extends MainCluster
{

    protected $admin_status;

    public function __construct(string $path, string $url)
    {
        
        $this->admin_status = ['type' => '', 'text' => ''];

        parent::__construct($path, $url);

    }

    /**
     * Display notice at admin page.
     * 
     * @param string $alert_type
     * 
     * @param string $text
     * 
     * @return void
     */
    protected function adminStatusSet(string $alert_type, string $text) : void
    {

        if ($alert_type === 'danger') $alert_type = 'error';

        $this->admin_status = [
            'type' => $alert_type,
            'text' => $text
        ];

        add_action('admin_notices', function($prev_notices) {

            ob_start();

?>
<div class="notice notice-<?= $this->admin_status['type'] ?> is-dismissible" style="max-width: 500px; margin-left: auto; margin-right: auto;">
    <p style="text-align: center;"><?= $this->admin_status['text'] ?></p>
</div>
<?php

            echo $prev_notices.ob_get_clean();

        });

    }

}
