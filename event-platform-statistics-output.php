<div class="container-fluid">
    <h1 class="h3 text-center my-5">Статистика</h1>
    <?= apply_filters('eps-admin-status', '') ?>
    <form class="eps-column mx-auto" action="" method="post">
        <div class="mb-2">
            <input type="checkbox" name="eps-download-participants" id="eps-download-participants" value="true" checked="true" onclick="epsAdminSubmitCheck();">
            <label for="eps-download-participants" class="form-check-label">Участники</label>
        </div>
        <div class="mb-2">
            <input type="checkbox" name="eps-download-demography" id="eps-download-demography" value="true" checked="true" onclick="epsAdminSubmitCheck();">
            <label for="eps-download-demography" class="form-check-label">Демография</label>
        </div>
        <div class="mb-2">
            <input type="checkbox" name="eps-download-visits" id="eps-download-visits" value="true" checked="true" onclick="epsAdminSubmitCheck();">
            <label for="eps-download-visits" class="form-check-label">Посещения</label>
        </div>
        <div class="mb-2">
            <input type="checkbox" name="eps-download-nmo-titles" id="eps-download-nmo-titles" value="true" checked="true" onclick="epsAdminSubmitCheck();">
            <label for="eps-download-nmo-titles" class="form-check-label">НМО</label>
        </div>
        <div class="mb-5">
            <input type="checkbox" name="eps-download-nmo-raw" id="eps-download-nmo-raw" value="true" checked="true" onclick="epsAdminSubmitCheck();">
            <label for="eps-download-nmo-raw" class="form-check-label">НМО (детализация)</label>
        </div>
        <input type="hidden" name="eps-download-init" value="true">
        <button type="submit" id="eps-admin-form-submit" class="button button-primary mx-auto">Скачать</button>
    </form>
</div>