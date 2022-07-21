<select name="<?php echo $name; ?>">
    <option value=""> - <?php _e('Select a option', 'open-govpub'); ?> - </option>
    <?php foreach ($options as $key => $option) { ?>
        <option value="<?php echo $key; ?>" <?php selected($value, $key, true); ?>>
            <?php echo $option; ?>
        </option>
    <?php } ?>
</select>

<?php if (isset($desc) && $desc) : ?>
    <p class="description"><?php echo $desc; ?></p>
<?php endif; ?>