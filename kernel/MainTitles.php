<?php
/**
 * Event Platform Statistics
 */
namespace EPStatistics;

use EPStatistics\Exceptions\TitlesException;

class MainTitles extends AdminPage
{

    protected $titles_tbody;

    /**
     * Output the titles.
     * 
     * @return void
     * 
     * @throws EPStatistics\Exceptions\TitlesException
     */
    public function titlesOutput() : void
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
                '1.0.0'
            );
        
        });

        add_action('init', function() {

            $titles = new Titles($this->wpdb);

            try {

                $select = $titles->selectTitles();

            } catch (TitlesException $e) {

                $this->adminStatusSet(
                    'danger',
                    'Ошибка загрузки титров, '.$e->getCode().': "'.$e->getMessage().'"'
                );

            }

            if (!isset($e) && !empty($select)) {

                ob_start();

                foreach ($select as $title) {

    ?>
    <tr id="eps-title-<?= $title['id'] ?>">
        <td><?= $title['id'] ?></td>
        <td id="eps-title-title-<?= $title['id'] ?>"><?= htmlspecialchars($title['title']) ?></td>
        <td id="eps-title-list-name-<?= $title['id'] ?>"><?= htmlspecialchars($title['list_name']) ?></td>
        <td id="eps-title-datetime-start-<?= $title['id'] ?>"><?= date("d.m.Y H:i", strtotime($title['datetime_start'])) ?></td>
        <td id="eps-title-datetime-end-<?= $title['id'] ?>"><?= date("d.m.Y H:i", strtotime($title['datetime_end'])) ?></td>
        <td id="eps-title-nmo-<?= $title['id'] ?>"><?= $title['nmo'] === '1' ? 'Да' : 'Нет' ?></td>
        <td id="eps-title-update-button-<?= $title['id'] ?>"><a href="javascript:void(0)" onclick="epsTitlesUpdate(<?= $title['id'] ?>);">Редактировать</a></td>
        <td><a href="javascript:void(0)" onclick="epsTitlesDelete(<?= $title['id'] ?>);">Удалить</a></td>
    </tr>
    <?php

                }

                $this->titles_tbody = ob_get_clean();

                add_filter('eps-titles-tbody', function() {

                    return $this->titles_tbody;

                });

            }

        });

    }

    /**
     * Add a title.
     * 
     * @return void
     * 
     * @throws EPStatistics\Exceptions\TitlesException
     */
    public function titleAdd() : void
    {

        add_action('plugins_loaded', function() {

            if (wp_verify_nonce(
                $_POST['eps-titles-wpnp'],
                'eps-titles-add'
            ) === false) $this->adminStatusSet(
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
                    $timestamp_start >= $timestamp_end) $this->adminStatusSet(
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

                        if ($add) $this->adminStatusSet(
                            'success',
                            'Элемент программы успешно сохранён!'
                        );
                        else $this->adminStatusSet(
                            'danger',
                            'Не удалось сохранить элемент программы.'
                        );

                    } catch (TitlesException $e) {

                        $this->adminStatusSet(
                            'danger',
                            'Ошибка, код '.$e->getCode().': "'.$e->getMessage().'"'
                        );

                    }

                }

            }

        });

    }

    /**
     * Update the title.
     * 
     * @return void
     * 
     * @throws EPStatistics\Exceptions\TitlesException
     */
    public function titleUpdate() : void
    {

        add_action('plugins_loaded', function() {

            if (wp_verify_nonce(
                $_POST['eps-titles-update-wpnp'],
                'eps-titles-update'
            ) === false) $this->adminStatusSet(
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

                if ($timestamp_start >= $timestamp_end) $this->adminStatusSet(
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
                        )) $this->adminStatusSet('success', 'Элемент программы успешно отредактирован!');
                        else $this->adminStatusSet('danger', 'Не удалось отредактировать элемент программы.');

                    } catch (TitlesException $e) {

                        $this->adminStatusSet(
                            'danger',
                            'Ошибка редактирования, код '.$e->getCode().': "'.$e->getMessage().'"'
                        );

                    }

                }

            }

        });

    }

    /**
     * Delete the title.
     * 
     * @return void
     * 
     * @throws EPStatistics\Exceptions\TitlesException
     */
    public function titleDelete() : void
    {

        add_action('plugins_loaded', function() {

            if (wp_verify_nonce(
                $_POST['eps-title-delete-wpnp'],
                'eps-titles-delete'
            ) === false) $this->adminStatusSet(
                'danger',
                'Произошла ошибка при отправке формы.'
            );
            else {

                $titles = new Titles($this->wpdb);

                try {

                    $delete = $titles->titleDelete((int)$_POST['eps-titles-title-delete']);

                } catch (TitlesException $e) {

                    $this->adminStatusSet(
                        'danger',
                        'Ошибка удаления элемента программы, код '.$e->getCode().': "'.$e->getMessage().'"'
                    );

                }

                if (!isset($e)) {

                    if ($delete) $this->adminStatusSet(
                        'success',
                        'Элемент программы успешно удалён!'
                    );
                    else $this->adminStatusSet(
                        'danger',
                        'Не удалось удалить элемент программы'
                    );

                }

            }

        });

    }

}
