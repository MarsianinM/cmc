<?php if(!empty($response)):?>
    <table>
        <thead>
            <tr>
                <th>#</th>
                <th><?php _e('Валюта') ?></th>
                <th><?php _e('Количество') ?></th>
                <th><?php _e('Сумма') ?></th>
                <th><?php _e('Дата') ?></th>
            </tr>
        </thead>
        <tbody>
            <?php $i = 1; ?>
            <?php foreach ($response as $item): ?>
            <tr>
                <td><?= $i; ?></td>
                <td><?= $item['symbol_group']; ?></td>
                <td><?= $item['data']->amount; ?></td>
                <td>
                    <?php foreach ($item['data']->quote as $symbol): ?>
                    <?= $symbol->price; ?>
                    <?php endforeach; ?>
                </td>
                <td><?= $item['created_at']; ?></td>
            </tr>
            <?php $i++; ?>
            <?php endforeach; ?>
        </tbody>
    </table>
<?php endif; ?>