<?php
/**
 * Event Platform Statistics
 */
if (!defined('ABSPATH')) die;

?>
<div class="container-fluid">
    <h1 class="h3 text-center my-5">Статистика</h1>
    <form class="eps-column mx-auto" action="" method="post">
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
        <input type="hidden" name="eps-download-init" value="true">
        <?php wp_nonce_field('eps-download-nonce', 'eps-download-wpnp') ?>
        <button type="submit" id="eps-admin-form-submit" class="button button-primary mx-auto">Скачать</button>
    </form>
</div>