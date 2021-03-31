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
                '1.0.6'
            );

        });

        return $this;

    }

    public function entryAdd() : self
    {

        add_action('plugins_loaded', function() {

            if (wp_verify_nonce(
                $_POST['eps-detached-button-add-wpnp'],
                'eps-detached-button-add'
            ) === false) $this->adminStatusSet(
                'danger',
                'Произошла ошибка при отправке формы. Пожалуйста, попробуйте ещё раз.'
            );
            else {

                $detached_buttons = new DetachedButtons($this->wpdb);

                if ($detached_buttons->add(
                    trim($_POST['eps-detached-buttons-add-button-id']),
                    strtotime(
                        trim($_POST['eps-detached-buttons-add-date']).
                        ' '.trim($_POST['eps-detached-buttons-add-time'])
                    )
                )) $this->adminStatusSet(
                    'success',
                    'Разблокировка кнопки сохранена.'
                );
                else $this->adminStatusSet(
                    'danger',
                    'Не удалось сохранить разблокировку кнопки.'
                );

            }

        });

        return $this;

    }

    public function filterEntries() : self
    {

        add_filter('eps-detached-buttons-tbody', function() {

            $detached_buttons = new DetachedButtons($this->wpdb);

            return $detached_buttons->selectAll();

        });

        return $this;

    }

}
