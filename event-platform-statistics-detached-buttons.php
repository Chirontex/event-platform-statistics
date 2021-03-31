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
        <?php wp_nonce_field('eps-detached-button-add', 'eps-detached-button-wpnp') ?>
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
</div>