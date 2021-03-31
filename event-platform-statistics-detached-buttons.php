<?php
/**
 * Event Platform Statistics
 */
if (!defined('ABSPATH')) die;

?>
<div class="container-fluid">
    <h1 class="h1 text-center my-5">Отдельные кнопки</h1>
    <div class="eps-column mx-auto">
        <form action="" method="post">
        <?php wp_nonce_field('eps-detached-button-add', 'eps-detached-button-add-wpnp') ?>
            <div class="mb-3">
                <input type="text" class="form-control form-control-sm" name="eps-detached-buttons-add-button-id" id="eps-detached-buttons-add-button-id" placeholder="ID кнопки" required="true" oninput="EPSDetachedButtons.checkSubmit();">
            </div>
            <p>Дата и время разблокировки:</p>
            <div class="row mb-3">
                <div class="col-xs-12 col-sm-12 col-md-6 col-lg-6 mb-2">
                    <input type="date" class="form-control form-control-sm" name="eps-detached-buttons-add-date" id="eps-detached-buttons-add-date" required="true" oninput="EPSDetachedButtons.checkSubmit();">
                </div>
                <div class="col-xs-12 col-sm-12 col-md-6 col-lg-6 mb-2">
                    <input type="time" class="form-control form-control-sm" name="eps-detached-buttons-add-time" id="eps-detached-buttons-add-time" required="true" oninput="EPSDetachedButtons.checkSubmit();">
                </div>
            </div>
            <div class="form-group text-center">
                <button type="submit" class="button button-primary" id="eps-detached-buttons-add-submit" disabled="true">Добавить</button>
            </div>
        </form>
    </div>
    <h5 class="text-center my-4">Сохранённые разблокировки кнопок</h5>
    <table class="table table-striped table-sm">
        <thead>
            <tr>
                <th>ID кнопки</th>
                <th>Дата и время разблокировки</th>
                <th></th>
                <th></th>
            </tr>
        </thead>
        <tbody>
<?php

foreach (apply_filters('eps-detached-buttons-tbody', []) as $row) {

?>
            <tr id="eps-detached-button-entry-<?= $row['id'] ?>">
                <td id="eps-detached-button-entry-<?= $row['id'] ?>-button-id"><?= htmlspecialchars($row['button_id']) ?></td>
                <td id="eps-detached-button-entry-<?= $row['id'] ?>-enable-datetime"><?= date("d.m.Y H:i", strtotime($row['enable_datetime'])) ?></td>
                <td id="eps-detached-button-entry-<?= $row['id'] ?>-update">Редактировать</td>
                <td id="eps-detached-button-entry-<?= $row['id'] ?>-delete">Удалить</td>
            </tr>
<?php

}

?>
        </tbody>
    </table>
    <form action="" method="post" id="eps-detached-button-entry-delete-form">
    <?php wp_nonce_field('eps-detached-button-delete', 'eps-detached-button-delete-wpnp') ?>
    </form>
    <form action="" method="post" id="eps-detached-button-entry-update-form">
    <?php wp_nonce_field('eps-detached-button-update', 'eps-detached-button-update-wpnp') ?>
    </form>
</div>