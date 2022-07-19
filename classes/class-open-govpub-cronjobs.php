<?php
if( ! class_exists( 'openGovpubCronjobs' ) ) {
    
    /**
     * openGovpub Initial setup
     *
     * @since   1.0.0
     */
    class openGovpubCronjobs {
        
        /**
         * The single instance of the class.
         *
         * @var openGovpubCronjobs|null
         */
        protected static $instance = null;
        
        /**
         * Gets the main openGovpubCronjobs Instance.
         *
         * @static
         * 
         * @return openGovpubCronjobs Main instance
         */
        public static function instance() {
            
            if ( is_null( self::$instance ) ) {
                self::$instance = new self();
            }
            return self::$instance;

        }

        /**
         * openGovpubCronjobs Constructor.
         */
        public function __construct()
        {

        	// On plugin activation add the cronjobs
            register_activation_hook( OPEN_GOVPUB_FILE, array($this, 'schedule_events') );
            
        }

        public function schedule_events()
        {

        	// Add the import organization schedule
			if (! wp_next_scheduled( 'open_govpub_import_organization' )) {
				wp_schedule_event(time(), 'daily', 'open_govpub_import_organization');
			}

			// Add the publications schedule thats queues the import
			if (! wp_next_scheduled( 'open_govpub_check_import_publications' )) {
				wp_schedule_event(time(), 'daily', 'open_govpub_check_import_publications');
			}

            // Add the import publications schedule
            if (! wp_next_scheduled( 'open_govpub_task_import_publications' )) {
                wp_schedule_event(time(), 'hourly', 'open_govpub_task_import_publications');
            }

        }

    }
    new openGovpubCronjobs();

}