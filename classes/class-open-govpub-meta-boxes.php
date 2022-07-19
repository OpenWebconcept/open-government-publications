<?php
if( ! class_exists( 'openGovpubMetaBoxes' ) ) {
    
    /**
     * openGovpub Meta Boxes setup
     *
     * @since   1.0.0
     */
    class openGovpubMetaBoxes {
        
        /**
         * The single instance of the class.
         *
         * @var openGovpubMetaBoxes|null
         */
        protected static $instance = null;
        
        /**
         * Gets the main openGovpubMetaBoxes Instance.
         *
         * @static
         * 
         * @return openGovpubMetaBoxes Main instance
         */
        public static function instance() {
            
            if ( is_null( self::$instance ) ) {
                self::$instance = new self();
            }
            return self::$instance;

        }

        /**
         * openGovpubMetaBoxes Constructor.
         */
        public function __construct()
        {
            
            // Register the meta boxes
        	add_action( 'add_meta_boxes', array($this, 'register_meta_boxes') );

        }

        public function register_meta_boxes()
        {

        	// Register the meta box
        	add_meta_box(
				'opengovpub-meta-box',
				__('Metadata'),
				array($this, 'render_meta_box'),
				'open_govpub',
				'normal',
				'default'
			);

            // Register the search meta box
            add_meta_box(
                'opengovpub-search-meta-box',
                __('Search string'),
                array($this, 'render_search_meta_box'),
                'open_govpub',
                'normal',
                'default'
            );

        }

        public function render_meta_box( $post )
        {

        	// Set the variable
        	$identifier = get_post_meta($post->ID, 'open_govpub_identifier', true);
        	$permalink 	= get_post_meta($post->ID, 'open_govpub_permalink', true);
        	$meta 		= get_post_meta($post->ID, 'open_govpub_meta', true);

        	// Include the view
        	include OPEN_GOVPUB_DIR . '/views/admin/view-open-govpub-meta-box.php';

        }

        public function render_search_meta_box( $post )
        {

            // Get the search meta
            $search_meta = get_post_meta($post->ID, 'search_meta', true);

            // Include the view
            include OPEN_GOVPUB_DIR . '/views/admin/view-open-govpub-meta-box-search.php';

        }

    }
    new openGovpubMetaBoxes();

}