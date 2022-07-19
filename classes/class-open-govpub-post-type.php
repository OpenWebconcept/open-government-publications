<?php
if( ! class_exists( 'openGovpubPostType' ) ) {
    
    /**
     * openGovpub PostType setup
     *
     * @since   1.0.0
     */
    class openGovpubPostType {
        
        /**
         * The single instance of the class.
         *
         * @var openGovpubPostType|null
         */
        protected static $instance = null;
        
        /**
         * Gets the main openGovpubPostType Instance.
         *
         * @static
         * 
         * @return openGovpubPostType Main instance
         */
        public static function instance() {
            
            if ( is_null( self::$instance ) ) {
                self::$instance = new self();
            }
            return self::$instance;

        }

        /**
         * openGovpubPostType Constructor.
         */
        public function __construct()
        {

        	// Register custom post type
            add_action( 'init', array($this, 'register_post_type'), 10 );

            // Register custom taxonomy
            add_action( 'init', array($this, 'register_taxonomy'), 10 );
            
        }

        public function register_post_type()
        {

        	// If debug is true, allow access to the post edit page
			if( WP_DEBUG === true || OPEN_GOVPUB_DEBUG === true ) {
				
				// Set empty capabilities
				$capabilities = array();

			} else {
				
				// Don't allow access to the post
				$capabilities = array(
					'create_posts' => 'do_not_allow'
				);

			}

        	// Set UI labels for Custom Post Type
			$labels = array(
				'name'                => __( 'Open Government Publications', 'open-govpub' ),
				'singular_name'       => __( 'Open Government Publications', 'open-govpub' ),
				'menu_name'           => __( 'Open Publications', 'open-govpub' ),
				'parent_item_colon'   => __( 'Parent Publications', 'open-govpub' ),
				'all_items'           => __( 'All Publications', 'open-govpub' ),
				'view_item'           => __( 'View Open Government Publication', 'open-govpub' ),
				'add_new_item'        => __( 'Add New Publication', 'open-govpub' ),
				'add_new'             => __( 'Add New', 'open-govpub' ),
				'edit_item'           => __( 'Edit Publication', 'open-govpub' ),
				'update_item'         => __( 'Update Publication', 'open-govpub' ),
				'search_items'        => __( 'Search Open Government Publication', 'open-govpub' ),
				'not_found'           => __( 'Not Found', 'open-govpub' ),
				'not_found_in_trash'  => __( 'Not found in Trash', 'open-govpub' ),
			);

			// Set other options for Custom Post Type
			$args = array(
				'label'               => __( 'Open Government Publications', 'open-govpub' ),
				'description'         => __( 'Open Government Publications', 'open-govpub' ),
				'labels'              => $labels,
				'supports'            => array(
					'title'
				),
				'hierarchical'        	=> false,
				'public'              	=> false,
				'show_ui'             	=> true,
				'show_in_menu'        	=> true,
				'show_in_nav_menus'   	=> true,
				'show_in_admin_bar'   	=> true,
				'menu_position'       	=> 20,
				'menu_icon' 			=> 'dashicons-text-page',
				'can_export'          	=> true,
				'has_archive'         	=> false,
				'exclude_from_search' 	=> true,
				'publicly_queryable'  	=> false,
				'capability_type' 		=> 'page',
				'capabilities' 			=> $capabilities,
			);

			// Registering Custom Post Type
			register_post_type( 'open_govpub', $args );

        }

        public function register_taxonomy()
        {

        	// Set UI labels for Custom Taxonomy
			$labels = array(
				'name' 				=> __( 'Type', 'open-govpub' ),
				'singular_name' 	=> __( 'Type', 'open-govpub' ),
				'search_items' 		=> __( 'Search Types', 'open-govpub' ),
				'all_items' 		=> __( 'All Types', 'open-govpub' ),
				'parent_item' 		=> __( 'Parent Type', 'open-govpub' ),
				'parent_item_colon' => __( 'Parent Type:', 'open-govpub' ),
				'edit_item' 		=> __( 'Edit Type', 'open-govpub' ), 
				'update_item' 		=> __( 'Update Type', 'open-govpub' ),
				'add_new_item' 		=> __( 'Add New Type', 'open-govpub' ),
				'new_item_name' 	=> __( 'New Type Name', 'open-govpub' ),
				'menu_name' 		=> __( 'Types', 'open-govpub' ),
			); 	

			// Register the taxonomy
			register_taxonomy( 'open_govpub_type', 'open_govpub', array(
				'hierarchical' 		=> true,
				'labels' 			=> $labels,
				'show_ui' 			=> true,
				'show_admin_column' => true,
				'query_var' 		=> true
			));

        }

    }
    new openGovpubPostType();

}