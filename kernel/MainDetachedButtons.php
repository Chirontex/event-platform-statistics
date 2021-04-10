<?php
/**
 * @package Event Platform Statistics
 */
namespace EPStatistics;

/**
 * POE which initiates detached buttons admin page.
 * @final
 * 
 * @since 1.9.11
 */
final class MainDetachedButtons extends AdminPage
{

    /**
     * @since 1.9.14
     */
    public function init() : self
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
                '1.1.1'
            );

        });
        
        add_filter('eps-detached-buttons-tbody', function() {

            $detached_buttons = new DetachedButtons($this->wpdb);

            return $detached_buttons->selectAll();

        });

        if (isset(
                $_POST['eps-detached-buttons-add-button-id']
            ) &&
            isset(
                $_POST['eps-detached-buttons-add-date']
            ) &&
            isset(
                $_POST['eps-detached-buttons-add-time']
        )) $this->entryAdd();

        if (isset(
                $_POST['eps-detached-button-update-entry-id']
            ) &&
            isset(
                $_POST['eps-detached-button-update-button-id']
            ) &&
            isset(
                $_POST['eps-detached-button-update-date']
            ) &&
            isset(
                $_POST['eps-detached-button-update-time']
        )) $this->entryUpdate();

        if (isset(
            $_POST['eps-detached-button-delete-entry-id']
        )) $this->entryDelete();

        return $this;

    }

    /**
     * Add detached button entry.
     * @since 1.9.11
     * 
     * @return $this
     */
    protected function entryAdd() : self
    {

        date_default_timezone_set('Europe/Moscow');

        add_action('plugins_loaded', function() {

            if (wp_verify_nonce(
                $_POST['eps-detached-button-add-wpnp'],
                'eps-detached-button-add'
            ) === false) $this->adminPageNotice(
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
                )) $this->adminPageNotice(
                    'success',
                    'Разблокировка кнопки сохранена.'
                );
                else $this->adminPageNotice(
                    'danger',
                    'Не удалось сохранить разблокировку кнопки.'
                );

            }

        });

        return $this;

    }

    /**
     * Update the detached button entry.
     * @since 1.9.11
     * 
     * @return $this
     */
    protected function entryUpdate() : self
    {

        date_default_timezone_set('Europe/Moscow');

        add_action('plugins_loaded', function() {

            if (wp_verify_nonce(
                $_POST['eps-detached-button-update-wpnp'],
                'eps-detached-button-update'
            ) === false) $this->adminPageNotice(
                'danger',
                'Произошла ошибка при отправке формы. Пожалуйста, попробуйте ещё раз.'
            );
            else {

                $detached_buttons = new DetachedButtons($this->wpdb);

                if ($detached_buttons->update(
                    (int)$_POST['eps-detached-button-update-entry-id'],
                    trim($_POST['eps-detached-button-update-button-id']),
                    strtotime(
                        trim($_POST['eps-detached-button-update-date']).
                        ' '.trim($_POST['eps-detached-button-update-time'])
                    )
                )) $this->adminPageNotice(
                    'success',
                    'Изменения успешно сохранены.'
                );
                else $this->adminPageNotice(
                    'danger',
                    'Не удалось сохранить изменения.'
                );

            }

        });

        return $this;

    }

    /**
     * Delete the detached button entry.
     * @since 1.9.11
     * 
     * @return $this
     */
    protected function entryDelete() : self
    {

        add_action('plugins_loaded', function() {

            if (wp_verify_nonce(
                $_POST['eps-detached-button-delete-wpnp'],
                'eps-detached-button-delete'
            ) === false) $this->adminPageNotice(
                'danger',
                'Произошла ошибка при отправке формы. Пожалуйста, попробуйте ещё раз.'
            );
            else {

                $detached_buttons = new DetachedButtons($this->wpdb);

                if ($detached_buttons->deleteEntry(
                    (int)$_POST['eps-detached-button-delete-entry-id']
                )) $this->adminPageNotice(
                    'success',
                    'Удаление успешно произведено.'
                );
                else $this->adminPageNotice(
                    'danger',
                    'Удаление не было произведено.'
                );

            }

        });

        return $this;

    }

}
