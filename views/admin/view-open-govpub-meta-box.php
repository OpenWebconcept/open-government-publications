<table class="widefat fixed striped">
    <tbody>
        <tr>
            <td><strong>Identifier</strong></td>
            <td><?php echo $publication->identifier(); ?></td>
        </tr>
        <tr>
            <td><strong>Permalink</strong></td>
            <td><a href="<?php echo $permalink; ?>" target="_blank"><?php echo $publication->permalink(); ?></a></td>
        </tr>
        <?php
            $meta = $publication->meta();
        if ($meta && !empty($meta)) {
            foreach ($meta as $meta_key => $meta_value) { ?>
                    <tr>
                        <td><strong>meta:<?php echo $meta_key; ?></strong></td>
                        <td><?php echo $meta_value; ?></td>
                    </tr>
            <?php }
        }
        ?>
    </tbody>
</table>