<select name="open_govpub_settings[creator]">
    <option value=""> - <?php _e('Select a option', 'open-govpub'); ?> - </option>
    <?php foreach ($organizations as $organization) : ?>
        <option value="<?= esc_attr($organization) ?>" <?php selected($organization, $currentCreator, true); ?>>
            <?= sanitize_text_field($organization); ?>
        </option>
    <?php endforeach; ?>
</select>

<p class="description">
    <?php _e(
        'Warning: please reset all publications after changing this field if a import has taken place',
        'open-govpub'
    ); ?>
</p>