<?php
/**
 * @package Event Platform Statistics
 */
if (!defined('ABSPATH')) die;

?>
<div class="container-fluid">
    <datalist id="eps-metadata-datalist">
    <?= apply_filters('eps-metadata-datalist', '') ?>
    </datalist>
    <h1 class="h3 text-center mt-5 mb-3">Статистика</h1>
    <div class="row">
        <div class="col-xs-12 col-sm-12 col-md-8 col-lg-8">
            <h4 class="text-center mb-3">Выгрузить статистику</h4>
            <form class="eps-column mx-auto" id="eps-download-form" action="" method="post">
                <div class="row mb-3">
                    <div class="col-xs-12 col-sm-12 col-md-6 col-lg-6">
                        <p class="mb-2">
                            <input type="checkbox" class="eps-admin-checkbox" name="eps-download-participants" id="eps-download-participants" value="true" checked="true" onclick="epsAdminSubmitCheck();">
                            <label for="eps-download-participants" class="form-check-label">Участники</label>
                        </p>
                        <p class="mb-2">
                            <input type="checkbox" class="eps-admin-checkbox" name="eps-download-demography" id="eps-download-demography" value="true" checked="true" onclick="epsAdminSubmitCheck();">
                            <label for="eps-download-demography" class="form-check-label">Демография</label>
                        </p>
                        <p class="mb-2">
                            <input type="checkbox" class="eps-admin-checkbox" name="eps-download-visits" id="eps-download-visits" value="true" checked="true" onclick="epsAdminSubmitCheck();">
                            <label for="eps-download-visits" class="form-check-label">Посещения</label>
                        </p>
                        <p class="mb-2">
                            <input type="checkbox" class="eps-admin-checkbox" name="eps-download-nmo-titles" id="eps-download-nmo-titles" value="true" checked="true" onclick="epsAdminSubmitCheck();">
                            <label for="eps-download-nmo-titles" class="form-check-label">НМО</label>
                        </p>
                        <p class="mb-5">
                            <input type="checkbox" class="eps-admin-checkbox" name="eps-download-nmo-raw" id="eps-download-nmo-raw" value="true" checked="true" onclick="epsAdminSubmitCheck();">
                            <label for="eps-download-nmo-raw" class="form-check-label">НМО (детализация)</label>
                        </p>
                    </div>
                    <div class="col-xs-12 col-sm-12 col-md-6 col-lg-6">
                        <p><label for="eps-download-url-matching" class="form-check-label">Сопоставление URL залам. Новый URL — с новой строки.</label></p>
                        <textarea name="eps-download-url-matching" id="eps-download-url-matching" cols="40" rows="3" class="form-control" placeholder="Пример: https://site.ru/hall-1/ ::: ЗАЛ 1"></textarea>
                    </div>
                </div>
                <input type="hidden" name="eps-download-init" value="true">
                <?php wp_nonce_field('eps-download-nonce', 'eps-download-wpnp') ?>
                <div class="text-center">
                    <button type="submit" id="eps-admin-form-submit" class="button button-primary">Скачать</button>
                </div>
            </form>
        </div>
        <div class="col-xs-12 col-sm-12 col-md-4 col-lg-4">
            <h4 class="text-center mb-3">Сопоставление метаданных пользователей</h4>
            <form class="eps-metadata-form mx-auto" action="" method="post">
                <div class="form-group mb-3">
                    <input type="text" class="form-control form-control-sm" id="eps-metadata-add-name" name="eps-metadata-add-name" placeholder="Введите название" required="true" oninput="epsMetadataSubmitCheck();">
                </div>
                <div class="form-group mb-3">
                    <input type="text" class="form-control form-control-sm" id="eps-metadata-add-key" name="eps-metadata-add-key" list="eps-metadata-datalist" placeholder="Укажите ключ" required="true" oninput="epsMetadataSubmitCheck();">
                </div>
                <?php wp_nonce_field('eps-metadata-add', 'eps-metadata-wpnp') ?>
                <div class="form-group text-center">
                    <button type="submit" class="button button-primary" id="eps-metadata-add-submit" disabled="true">Сохранить</button>
                </div>
            </form>
        </div>
    </div>
    <h4 class="text-center mt-5 mb-3">Сопоставленные метаданные</h4>
    <table class="table table-striped table-sm">
        <thead>
            <tr>
                <th style="text-align: center;">Название</th>
                <th style="text-align: center;">Ключ</th>
                <th style="text-align: center;">Порядковый номер</th>
                <th style="text-align: center;">Включать в отчёт</th>
                <th style="text-align: center;"></th>
                <th style="text-align: center;"></th>
            </tr>
        </thead>
        <tbody>
        <?= apply_filters('eps-metadata-tbody', '') ?>
        </tbody>
    </table>
    <form action="" method="post" hidden="true">
        <input type="hidden" id="eps-metadata-update" name="eps-metadata-update" required="true">
        <?php wp_nonce_field('eps-metadata-update', 'eps-metadata-update-wpnp') ?>
        <button type="submit" id="eps-metadata-update-submit"></button>
    </form>
    <form action="" method="post" hidden="true">
        <input type="hidden" id="eps-metadata-delete" name="eps-metadata-delete" required="true">
        <?php wp_nonce_field('eps-metadata-delete', 'eps-metadata-delete-wpnp') ?>
        <button type="submit" id="eps-metadata-delete-submit"></button>
    </form>
</div>