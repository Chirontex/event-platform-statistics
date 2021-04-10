<?php
/**
 * @package Event Platform Statistics
 */
namespace EPStatistics;

/**
 * POE which initiates admin page.
 * @since 1.9.11
 */
class AdminPage extends MainCluster
{

    /**
     * @var array $admin_notice
     * Status data.
     * @since 1.9.11
     */
    protected $admin_notice = [
        'type' => '',
        'text' => ''
    ];

    /**
     * Display notice at admin page.
     * @since 1.9.11
     * 
     * @param string $type
     * Notice type. Available: 'success', 'warning', 'error' (or 'danger').
     * 
     * @param string $text
     * Notice text.
     * 
     * @return $this
     */
    protected function adminPageNotice(string $type, string $text) : self
    {

        if ($type === 'danger') $type = 'error';

        $this->admin_notice = [
            'type' => $type,
            'text' => $text
        ];

        add_action('admin_notices', function($prev_notices) {

            ob_start();

?>
<div class="notice notice-<?= $this->admin_notice['type'] ?> is-dismissible" style="max-width: 500px; margin-left: auto; margin-right: auto;">
    <p style="text-align: center;"><?= $this->admin_notice['text'] ?></p>
</div>
<?php

            echo $prev_notices.ob_get_clean();

        });

        return $this;

    }

}
