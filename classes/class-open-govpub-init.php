<?php
if( ! class_exists( 'openGovpubInit' ) ) {
    
    /**
     * openGovpub Initial setup
     *
     * @since   1.0.0
     */
    class openGovpubInit {
        
        /**
         * The single instance of the class.
         *
         * @var openGovpubInit|null
         */
        protected static $instance = null;
        
        /**
         * Gets the main openGovpubInit Instance.
         *
         * @static
         * 
         * @return openGovpubInit Main instance
         */
        public static function instance() {
            
            if ( is_null( self::$instance ) ) {
                self::$instance = new self();
            }
            return self::$instance;

        }

        /**
         * openGovpubInit Constructor.
         */
        public function __construct()
        {

            // Import organizations on activation and daily by cronjob
            register_activation_hook( OPEN_GOVPUB_FILE, array($this, 'import_organization') );
            add_action( 'open_govpub_import_organization', array($this, 'import_organization') );

            // Enqueue admin styles and scripts
            add_action( 'admin_enqueue_scripts', array($this, 'admin_enqueue_scripts') );
            
        }

        public function import_organization()
        {

            // Get the organizations from source
            $list = get_open_govpub_source_organizations();

            // Check if list is not empty
            if( !empty($list) ) {

                // Save the list
                update_option( 'open_govpub_organization', $list );

            }

            return true;

        }

        public function admin_enqueue_scripts()
        {

            // Enqueue admin plugin style
            wp_enqueue_style(
                'open_govpub',
                OPEN_GOVPUB_URL . '/assets/dist/admin/css/style.min.css',
                array(),
                OPEN_GOVPUB_VERSION
            );

            // Enqueue admin plugin script
            wp_enqueue_script(
                'open_govpub',
                OPEN_GOVPUB_URL . '/assets/dist/admin/js/base.min.js',
                array('jquery'),
                OPEN_GOVPUB_VERSION,
                true
            );
            
            // Localize the script
            wp_localize_script( 'open_govpub', 'open_govpub', array( 
                'ajaxurl' => admin_url( 'admin-ajax.php' )
            ));        

        }

    }
    new openGovpubInit();

}