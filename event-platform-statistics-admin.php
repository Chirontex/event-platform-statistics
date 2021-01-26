<link href="<?= plugin_dir_url(__FILE__) ?>css/bootstrap.min.css" rel="stylesheet">
<link rel="stylesheet" href="<?= plugin_dir_url(__FILE__) ?>css/admin.css">
<script src="<?= plugin_dir_url(__FILE__) ?>js/bootstrap.bundle.min.js"></script>
<script src="<?= plugin_dir_url(__FILE__) ?>js/admin.js"></script>
<div class="container-fluid">
    <h1 class="h3 text-center my-5">Статистика</h1>
<?php

if (!empty($eps_admin_status)) echo $eps_admin_status;

?>
    <form class="eps-column mx-auto text-center" action="" method="post">
        <div class="mb-5">
            <input type="checkbox" class="form-check-input" name="eps-download-participants" id="eps-download-participants" value="true" checked="true" onclick="epsAdminSubmitCheck();">
            <label for="eps-download-participants" class="form-check-label">Участники</label>
        </div>
        <input type="hidden" name="eps-download-init" value="true">
        <button type="submit" id="eps-admin-form-submit" class="btn btn-primary mx-auto">Скачать</button>
    </form>
</div>