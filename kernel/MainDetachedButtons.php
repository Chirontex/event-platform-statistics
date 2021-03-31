<?php
/**
 * Event Platform Statistics
 */
namespace EPStatistics;

class MainDetachedButtons extends AdminPage
{

    public function __construct(string $path, string $url)
    {
        
        parent::__construct($path, $url);

        $this->plugScriptsStyles();

    }

    public function plugScriptsStyles() : self
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
                '5.0.0-beta1'
            );

            wp_enqueue_style(
                'detached-buttons',
                $this->url.'css/detached-buttons.css',
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
                '5.0.0-beta1'
            );

            wp_enqueue_script(
                'detached-buttons',
                $this->url.'js/detached-buttons.js',
                [],
                '1.0.0'
            );

        });

        return $this;

    }

}
