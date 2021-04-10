<?php
/**
 * @package Event Platform Statistics
 */
if (!defined('ABSPATH')) die;

?>
<div class="container-fluid">
    <h1 class="h3 text-center my-5">Титры</h1>
    <form action="" method="post" class="eps-titles-form mx-auto">
        <h5 class="text-center mb-4">Добавить новый элемент программы:</h5>
        <div class="form-group mb-3">
            <input type="text" class="form-control form-control-sm" id="eps-titles-header" name="eps-titles-header" placeholder="Введите заголовок" required="true" oninput="epsTitlesSubmitCheck();">
        </div>
        <div class="form-group mb-3">
            <input type="text" class="form-control form-control-sm" id="eps-titles-list" name="eps-titles-list" placeholder="Укажите обозначение зала" required="true" oninput="epsTitlesSubmitCheck();">
        </div>
        <p>Дата и время начала:</p>
        <div class="row mb-3">
            <div class="col-xs-12 col-sm-12 col-md-6 col-lg-6 mb-2">
                <input type="date" class="form-control form-control-sm" id="eps-titles-start-date" name="eps-titles-start-date" required="true" oninput="epsTitlesSubmitCheck();">
            </div>
            <div class="col-xs-12 col-sm-12 col-md-6 col-lg-6 mb-2">
                <input type="time" class="form-control form-control-sm" id="eps-titles-start-time" name="eps-titles-start-time" required="true" oninput="epsTitlesSubmitCheck();">
            </div>
        </div>
        <p>Дата и время конца:</p>
        <div class="row mb-3">
            <div class="col-xs-12 col-sm-12 col-md-6 col-lg-6 mb-2">
                <input type="date" class="form-control form-control-sm" id="eps-titles-end-date" name="eps-titles-end-date" required="true" oninput="epsTitlesSubmitCheck();">
            </div>
            <div class="col-xs-12 col-sm-12 col-md-6 col-lg-6 mb-2">
                <input type="time" class="form-control form-control-sm" id="eps-titles-end-time" name="eps-titles-end-time" required="true" oninput="epsTitlesSubmitCheck();">
            </div>
        </div>
        <p class="form-group mb-3">
            <input type="checkbox" id="eps-titles-nmo" name="eps-titles-nmo" value="true">
            <label for="eps-titles-nmo">Нужно учитывать в статистике для НМО</label>
        </p>
        <?php wp_nonce_field('eps-titles-add', 'eps-titles-wpnp') ?>
        <div class="form-group text-center">
            <button type="submit" class="button button-primary" id="eps-titles-form-submit" disabled="true">Сохранить</button>
        </div>
    </form>
    <h5 class="text-center my-4">Сохранённые элементы программы</h5>
    <table class="table table-striped table-sm">
        <thead>
            <tr>
                <th>ID</th>
                <th>Заголовок</th>
                <th>Зал</th>
                <th>Дата и время начала</th>
                <th>Дата и время конца</th>
                <th>НМО</th>
                <th></th>
                <th></th>
            </tr>
        </thead>
        <tbody>
        <?= apply_filters('eps-titles-tbody', '') ?>
        </tbody>
    </table>
    <form action="" method="post" hidden="true">
        <input type="hidden" id="eps-titles-title-delete" name="eps-titles-title-delete" required="true">
        <?php wp_nonce_field('eps-titles-delete', 'eps-title-delete-wpnp') ?>
        <button type="submit" id="eps-titles-delete-submit"></button>
    </form>
    <form action="" method="post" hidden="true">
        <input type="hidden" id="eps-titles-title-update" name="eps-titles-title-update" required="true">
        <?php wp_nonce_field('eps-titles-update', 'eps-titles-update-wpnp') ?>
        <button type="submit" id="eps-titles-update-submit"></button>
    </form>
</div>