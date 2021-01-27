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
<script src="<?= plugin_dir_url(__FILE__) ?>js/admin.js"></script>
<div class="container-fluid">
    <h1 class="h3 text-center my-5">Титры</h1>
<?php

if (!empty($eps_titles_status)) echo $eps_titles_status;

?>
    <form action="" method="post" class="eps-titles-form mx-auto">
        <h4 class="text-center mb-4">Добавить новый элемент программы:</h4>
        <div class="form-group mb-2">
            <input type="text" class="form-control" id="eps-titles-header" name="eps-titles-header" placeholder="Введите заголовок">
        </div>
        <div class="row">
            
        </div>
    </form>
</div>