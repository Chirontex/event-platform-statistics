<?php

if (file_exists(plugin_dir_path(__FILE__).'css/bootstrap.min.css')) {

?>
<link href="<?= plugin_dir_url(__FILE__) ?>css/bootstrap.min.css" rel="stylesheet">
<?php

} else {

?>
<link href="https://cdn.jsdelivr.net/npm/bootstrap@5.0.0-beta1/dist/css/bootstrap.min.css" rel="stylesheet" integrity="sha384-giJF6kkoqNQ00vy+HMDP7azOuL0xtbfIcaT9wjKHr8RbDVddVHyTfAAsrekwKmP1" crossorigin="anonymous">
<?php

}

?>
<link rel="stylesheet" href="<?= plugin_dir_url(__FILE__) ?>css/titles.css">
<?php

if (file_exists(plugin_dir_path(__FILE__).'js/bootstrap.bundle.min.js')) {

?>
<script src="<?= plugin_dir_url(__FILE__) ?>js/bootstrap.bundle.min.js"></script>
<?php

} else {

?>
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.0.0-beta1/dist/js/bootstrap.bundle.min.js" integrity="sha384-ygbV9kiqUc6oa4msXn9868pTtWMgiQaeYH7/t7LECLbyPA2x65Kgf80OJFdroafW" crossorigin="anonymous"></script>
<?php

}

?>
<script src="<?= plugin_dir_url(__FILE__) ?>js/titles.js"></script>
<div class="container-fluid">
    <h1 class="h3 text-center my-5">Титры</h1>
<?php

if (!empty($eps_admin_status)) echo $eps_admin_status;

?>
    <form action="" method="post" class="eps-titles-form mx-auto">
        <h4 class="text-center mb-4">Добавить новый элемент программы:</h4>
        <div class="form-group mb-3">
            <input type="text" class="form-control" id="eps-titles-header" name="eps-titles-header" placeholder="Введите заголовок" required="true" onclick="epsTitlesSubmitCheck();">
        </div>
        <p>Дата и время начала:</p>
        <div class="row mb-3">
            <div class="col-xs-12 col-sm-12 col-md-6 col-lg-6">
                <input type="date" class="form-control" id="eps-titles-start-date" name="eps-titles-start-date" required="true" onclick="epsTitlesSubmitCheck();">
            </div>
            <div class="col-xs-12 col-sm-12 col-md-6 col-lg-6">
                <input type="time" class="form-control" id="eps-titles-start-time" name="eps-titles-start-time" required="true" onclick="epsTitlesSubmitCheck();" onclick="epsTitlesSubmitCheck();">
            </div>
        </div>
        <p>Дата и время конца:</p>
        <div class="row mb-3">
            <div class="col-xs-12 col-sm-12 col-md-6 col-lg-6">
                <input type="date" class="form-control" id="eps-titles-end-date" name="eps-titles-end-date" required="true" onclick="epsTitlesSubmitCheck();">
            </div>
            <div class="col-xs-12 col-sm-12 col-md-6 col-lg-6">
                <input type="time" class="form-control" id="eps-titles-end-time" name="eps-titles-end-time" required="true" onclick="epsTitlesSubmitCheck();">
            </div>
        </div>
        <div class="form-group mb-3">
            <input type="checkbox" class="form-check-input" id="eps-titles-nmo" name="eps-titles-nmo" value="true">
            <label for="eps-titles-nmo">Нужно учитывать в статистике для НМО</label>
        </div>
        <div class="form-group text-center">
            <button type="submit" class="btn btn-primary" id="eps-titles-form-submit" disabled="true">Сохранить</button>
        </div>
    </form>
    <h4 class="text-center my-4">Сохранённые элементы программы</h4>
    <table class="table table-striped">
        <thead>
            <tr>
                <th>Заголовок</th>
                <th>Дата и время начала</th>
                <th>Дата и время конца</th>
                <th>НМО</th>
                <th></th>
            </tr>
        </thead>
        <tbody id="eps-titles-table"><?= $eps_titles_tbody ?></tbody>
    </table>
    <form action="" method="post" hidden="true">
        <input type="hidden" id="eps-titles-title-delete" name="eps-titles-title-delete" required="true">
        <button type="submit" id="eps-titles-delete-submit"></button>
    </form>
</div>