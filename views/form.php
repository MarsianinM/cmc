<?php if(!empty($response)):?>
    <form method="post" action="<?php echo esc_url( admin_url('admin-post.php') ); ?>" class="section-inner js_form_cmc">
        <input type="hidden" name="action" value="covert_form">
        <div class="row">
            <div class="input-group col-5">
                <div class="row">
                    <div class="col-6 pr-0">
                        <input type="text" class="form-control " name="amount" value="1">
                    </div>
                    <!--<div class="input-group-append-form col-7 pl-0">
                        <span class="input-group-text">BTC</span>
                    </div>-->
                    <div class="input-group-append-form col-6 pl-0">
                        <select name="symbol">
                            <?php foreach ($response->json as $item): ?>
                                    <option value="<?= $item->symbol?>"><?= $item->name?></option>
                            <?php endforeach; ?>
                        </select>
                    </div>
                </div>
            </div>
            <div class="direction-switch col-1 text-center">
                <span>
                    =
                </span>
            </div>
            <div class="input-group col-6">
                <div class="row">
                    <div class="col-8 pr-0">
                        <input type="text" class="form-control " name="covert_sum" value="1">
                    </div>
                    <div class="input-group-append-form col-4 pl-0">
                        <select name="convert">
                            <?php foreach ($response->json as $item): ?>
                                <option value="<?= $item->symbol?>"><?= $item->name?></option>
                            <?php endforeach; ?>
                        </select>
                    </div>
                </div>
            </div>
        </div>
        <input type="submit" class="submit-btn" value="ok">
    </form>
<?php endif; ?>
