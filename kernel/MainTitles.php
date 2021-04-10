<?php
/**
 * @package Event Platform Statistics
 */
namespace EPStatistics;

use EPStatistics\Exceptions\TitlesException;

/**
 * POE which initiates titles admin page.
 * @final
 * 
 * @since 1.9.11
 */
final class MainTitles extends AdminPage
{

    /**
     * @since 1.9.13
     */
    protected function init() : self
    {

        date_default_timezone_set('Europe/Moscow');

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
                'eps-titles',
                $this->url.'css/titles.css',
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
                'eps-titles',
                $this->url.'js/titles.js',
                [],
                '1.0.1'
            );
        
        });

        add_filter('eps-titles-tbody', function() {

            $titles = new Titles($this->wpdb);

            return $titles->selectTitles();

        });

        if (isset($_POST['eps-titles-header']) &&
            isset($_POST['eps-titles-list']) &&
            isset($_POST['eps-titles-start-date']) &&
            isset($_POST['eps-titles-start-time']) &&
            isset($_POST['eps-titles-end-date']) &&
            isset($_POST['eps-titles-end-time'])) $this->titleAdd();
        elseif (isset($_POST['eps-titles-title-update'])) $this->titleUpdate();
        elseif (isset($_POST['eps-titles-title-delete'])) $this->titleDelete();

        return $this;

    }

    /**
     * Add a title.
     * @since 1.9.11
     * 
     * @return $this
     * 
     * @throws EPStatistics\Exceptions\TitlesException
     */
    protected function titleAdd() : self
    {

        date_default_timezone_set('Europe/Moscow');

        add_action('plugins_loaded', function() {

            if (wp_verify_nonce(
                $_POST['eps-titles-wpnp'],
                'eps-titles-add'
            ) === false) $this->adminPageNotice(
                'danger',
                'Произошла ошибка отправки формы.'
            );
            else {

                $timestamp_start = strtotime(
                    $_POST['eps-titles-start-date'].' '.$_POST['eps-titles-start-time']
                );

                $timestamp_end = strtotime(
                    $_POST['eps-titles-end-date'].' '.$_POST['eps-titles-end-time']
                );

                if (isset($_POST['eps-titles-nmo'])) $nmo = 1;
                else $nmo = 0;

                if ($timestamp_start === false ||
                    $timestamp_end === false ||
                    $timestamp_start >= $timestamp_end) $this->adminPageNotice(
                        'danger',
                        'Дата и время были указаны некорректно.'
                    );
                else {

                    $titles = new Titles($this->wpdb);

                    try {

                        $add = $titles->titleAdd(
                            trim($_POST['eps-titles-header']),
                            trim($_POST['eps-titles-list']),
                            $timestamp_start,
                            $timestamp_end,
                            $nmo
                        );

                        if ($add) $this->adminPageNotice(
                            'success',
                            'Элемент программы успешно сохранён!'
                        );
                        else $this->adminPageNotice(
                            'danger',
                            'Не удалось сохранить элемент программы.'
                        );

                    } catch (TitlesException $e) {

                        $this->adminPageNotice(
                            'danger',
                            'Ошибка, код '.$e->getCode().': "'.$e->getMessage().'"'
                        );

                    }

                }

            }

        });

        return $this;

    }

    /**
     * Update the title.
     * @since 1.9.11
     * 
     * @return $this
     * 
     * @throws EPStatistics\Exceptions\TitlesException
     */
    protected function titleUpdate() : self
    {

        date_default_timezone_set('Europe/Moscow');

        add_action('plugins_loaded', function() {

            if (wp_verify_nonce(
                $_POST['eps-titles-update-wpnp'],
                'eps-titles-update'
            ) === false) $this->adminPageNotice(
                'danger',
                'Произошла ошибка при отправке формы.'
            );
            else {

                $titles = new Titles($this->wpdb);

                $timestamp_start = strtotime(
                    $_POST['eps-titles-title-update-date-start'].' '.$_POST['eps-titles-title-update-time-start']
                );

                $timestamp_end = strtotime(
                    $_POST['eps-titles-title-update-date-end'].' '.$_POST['eps-titles-title-update-time-end']
                );

                if ($timestamp_start >= $timestamp_end) $this->adminPageNotice(
                    'danger',
                    'Дата и время были указаны некорректно.'
                );
                else {

                    try {

                        if ($titles->titleUpdate(
                            (int)$_POST['eps-titles-title-update'],
                            trim($_POST['eps-titles-title-update-title']),
                            trim($_POST['eps-titles-title-update-list-name']),
                            $timestamp_start,
                            $timestamp_end,
                            (int)$_POST['eps-titles-title-update-nmo']
                        )) $this->adminPageNotice('success', 'Элемент программы успешно отредактирован!');
                        else $this->adminPageNotice('danger', 'Не удалось отредактировать элемент программы.');

                    } catch (TitlesException $e) {

                        $this->adminPageNotice(
                            'danger',
                            'Ошибка редактирования, код '.$e->getCode().': "'.$e->getMessage().'"'
                        );

                    }

                }

            }

        });

        return $this;

    }

    /**
     * Delete the title.
     * @since 1.9.11
     * 
     * @return $this
     * 
     * @throws EPStatistics\Exceptions\TitlesException
     */
    protected function titleDelete() : self
    {

        add_action('plugins_loaded', function() {

            if (wp_verify_nonce(
                $_POST['eps-title-delete-wpnp'],
                'eps-titles-delete'
            ) === false) $this->adminPageNotice(
                'danger',
                'Произошла ошибка при отправке формы.'
            );
            else {

                $titles = new Titles($this->wpdb);

                try {

                    $delete = $titles->titleDelete((int)$_POST['eps-titles-title-delete']);

                } catch (TitlesException $e) {

                    $this->adminPageNotice(
                        'danger',
                        'Ошибка удаления элемента программы, код '.$e->getCode().': "'.$e->getMessage().'"'
                    );

                }

                if (!isset($e)) {

                    if ($delete) $this->adminPageNotice(
                        'success',
                        'Элемент программы успешно удалён!'
                    );
                    else $this->adminPageNotice(
                        'danger',
                        'Не удалось удалить элемент программы'
                    );

                }

            }

        });

        return $this;

    }

}
