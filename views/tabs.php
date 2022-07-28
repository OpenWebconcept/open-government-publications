<nav class="nav-tab-wrapper wp-clearfix" aria-label="Secondair menu">
    <?php
        $currentTab = sanitize_text_field($_GET['tab'] ?? '');

        printf(
            '<a href="%s" class="nav-tab %s">%s</a>',
            add_query_arg(['tab' => '', 'deleted_i' => false]),
            ($currentTab === '' ? 'nav-tab-active' : ''),
            __('Settings', 'open-govpub')
        );

        printf(
            '<a href="%s" class="nav-tab %s">%s</a>',
            add_query_arg(['tab' => 'reset', 'deleted_i' => false]),
            ($currentTab  === 'reset' ? 'nav-tab-active' : ''),
            __('Reset', 'open-govpub')
        );

        printf(
            '<a href="%s" class="nav-tab %s">%s</a>',
            add_query_arg(['tab' => 'endpoints', 'deleted_i' => false]),
            ($currentTab  === 'endpoints' ? 'nav-tab-active' : ''),
            __('Endpoints', 'open-govpub')
        );
        ?>
</nav>
