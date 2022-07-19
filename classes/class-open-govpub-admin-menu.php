<?php
if( ! class_exists( 'openGovpubAdminMenu' ) ) {
    
    /**
     * openGovpub Admin menu options
     *
     * @since   1.0.0
     */
    class openGovpubAdminMenu {
        
        /**
         * The single instance of the class.
         *
         * @var openGovpubAdminMenu|null
         */
        protected static $instance = null;
        
        /**
         * Gets the main openGovpubAdminMenu Instance.
         *
         * @static
         * 
         * @return openGovpubAdminMenu Main instance
         */
        public static function instance() {
            
            if ( is_null( self::$instance ) ) {
                self::$instance = new self();
            }
            return self::$instance;

        }

        /**
         * openGovpubAdminMenu Constructor.
         */
        public function __construct()
        {

            // Add admin menu items
            add_action( 'admin_menu', array($this, 'add_admin_pages'), 10 );
            
        }

        public function add_admin_pages() {

        
            // Add import option to the menu
            add_submenu_page(
                'edit.php?post_type=open_govpub',
                __('Open Government Publications - import', 'open-govpub'),
                __('Import options', 'open-govpub'),
                'manage_options',
                'open-govpub',
                array($this, 'show_import_page')
            );

            // Add the settings page to the menu
            add_submenu_page(
                'edit.php?post_type=open_govpub',
                __('Open Government Publications - settings', 'open-govpub'),
                __('Settings', 'open-govpub'),
                'manage_options',
                'open-govpub-settings',
                array($this, 'show_settings_page')
            );


        }

        public function show_import_page() {

            // Include the import view
            include OPEN_GOVPUB_DIR . '/views/admin/view-open-govpub-import.php';

        }

         public function show_settings_page() {

            // Get the current tab
            $c_tab = (isset($_GET['tab']) ? sanitize_text_field($_GET['tab']) : false);

            if( $c_tab == 'reset' ) {
                
                // Get current rest action
                $c_reset = (isset($_GET['reset']) ? sanitize_text_field($_GET['reset']) : '');

                // Include the reset view
                include OPEN_GOVPUB_DIR . '/views/admin/view-open-govpub-reset.php';

            } else if( $c_tab == 'endpoints' ) {
                
                // Get the api args
                $search_args    = get_open_govpub_search_api_args();
                $types_args     = get_open_govpub_types_api_args();

                // Include the endpoints view
                include OPEN_GOVPUB_DIR . '/views/admin/view-open-govpub-endpoints.php';

            } else {

                // Include the settings view
                include OPEN_GOVPUB_DIR . '/views/admin/view-open-govpub-settings.php';

            } 

        }

    }
    new openGovpubAdminMenu();

}