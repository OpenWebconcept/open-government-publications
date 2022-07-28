<div class="wrap">
    <h1><?php echo get_admin_page_title(); ?></h1>
    <table class="form-table">
        <tr>
            <th><?php _e('Last import', 'open-govpub'); ?></th>
            <td>
                <?php if ($lastImport) : ?>
                    <?php echo wp_date($lastImport, 'd-m-Y H:i'); ?>
                <?php else : ?>
                    <?php _e('never', 'open-govpub'); ?>
                <?php endif; ?>
            </td>
        </tr>
        <tr>
            <th><?php _e('Next import (check)', 'open-govpub'); ?></th>
            <td><?php echo $check_import_schedule; ?></td>
        </tr>
        <tr>
            <th><?php _e('Next import (task)', 'open-govpub'); ?></th>
            <td><?php echo $task_import_schedule; ?></td>
        </tr>
        <tr>
            <th><?php _e('Current import', 'open-govpub'); ?></th>
            <td id="open_govpub--import-string">
                <?php if ($totalImport && isset($totalImport['status'])) : ?>
                    <?php printf(
                        __('%s of %s items imported', 'open-govpub'),
                        $totalImport['status'],
                        $totalImport['max_num']
                    ); ?>
                <?php else : ?>
                    <?php _e('no import running', 'open-govpub'); ?>
                <?php endif; ?>
            </td>
        </tr>
        <tr class="open_govpub--progress">
            <th>Progress</th>
            <td><pre class="open_govpub--progress-output"></pre></td>
        </tr>
        <tr>
            <th></th>
            <td>
                <button type="button" id="open_govpub--start-import" class="button button-primary"><?php _e('Run manual import', 'open-govpub'); ?></button>
                <button type="button" id="open_govpub--halt-import" class="button"><?php _e('Abort manual import', 'open-govpub'); ?></button>
            </td>
        </tr>
    </table>
    <div class="open_govpub--import-wrap">
        <div class="open_govpub--import-bar">
            <div class="open_govpub--import-progress"><span></span></div>
        </div>
    </div>
</div>