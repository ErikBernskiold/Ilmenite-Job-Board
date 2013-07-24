<?php
/**
 * Post Types Class
 *
 * Sets up custom post types and taxonomies to match.
 */

/**
 * Ilmenite_Job_Board_Post_Types class
 */
class Ilmenite_Job_Board_Post_Types {

	/**
	 * Constructor
	 */
	public function __construct() {

		// Load the post type
		add_action( 'init', array( $this, 'register_post_type' ) );
		add_action( 'init', array( $this, 'register_status' ) );

	}

	/**
	 * Registers the post type for the job board
	 */
	public function register_post_type() {

		// If the post type already exists, quit.
		if( post_type_exists( 'il_job_board' ) )
			return;

		/**
		 * Job Board Post Type
		 */

		// Label Defaults
		$plural_label = __( 'Job Listings', 'iljobboard' );
		$singular_label = __( 'Job Listing', 'iljobboard' );

		// Register the post type
		register_post_type( 'il_job_board', array(
			'labels' 				=> array(
				'name'               => $plural_label,
				'singular_name'      => $singular_label,
				'menu_name'          => $plural_label,
				'all_items'          => sprintf( __('All %s', 'iljobboard'), $plural_label ),
				'add_new'            => __( 'Add New', 'iljobboard' ),
				'add_new_item'       => sprintf( __('Add New %s', 'iljobboard'), $singular_label ),
				'edit'               => __( 'Edit', 'iljobboard' ),
				'edit_item'          => sprintf( __('Edit %s', 'iljobboard'), $singular_label ),
				'new_item'           => sprintf( __('New %s', 'iljobboard'), $singular_label ),
				'view'               => sprintf( __('View %s', 'iljobboard'), $singular_label ),
				'view_item'          => sprintf( __('View %s', 'iljobboard'), $singular_label ),
				'search_items'       => sprintf( __('Search %s', 'iljobboard'), $plural_label ),
				'not_found'          => sprintf( __('No %s found', 'iljobboard'), $plural_label ),
				'not_found_in_trash' => sprintf( __('No %s found in trash', 'iljobboard'), $plural_label ),
				'parent'             => sprintf( __('Parent %s', 'iljobboard'), $singular_label ),
			),
			'description'         => __( 'Create and manage job listings for the job board.', 'iljobboard' ),
			'public'              => true,
			'show_ui'             => true,
			'capability_type'     => 'post',
			'publicly_queryable'  => true,
			'exclude_from_search' => false,
			'hierarchical'        => false,
			'rewrite'             => array(
				'slug'       => _x( 'jobs', 'post type single slug', 'iljobboard' ),
				'with_front' => false,
				'feeds'      => true,
				'pages'      => false,
			),
			'query_var'           => true,
			'supports'            => array(
				'title',
				'editor',
				'custom-fields'
			),
			'has_archive'		    => false,
//			'has_archive'         => _x( 'jobs', 'post type archive slug', 'iljobboard' ),
			'show_in_nav_menus'   => false,
		));

	}

	/**
	 * Register Post Status
	 */
	public function register_status() {

		/**
		 * Custom Post Status: Expired
		 *
		 * Used when a post listing is expired and shouldn't
		 * be listed publicly.
		 */
		register_post_status( 'expired', array(
			'label'                     => _x( 'Expired', 'job listing post status', 'iljobboard' ),
			'public'                    => true,
			'exclude_from_search'       => false,
			'show_in_admin_all_list'    => true,
			'show_in_admin_status_list' => true,
			'label_count'               => _n_noop( 'Expired <span class="count">(%s)</span>', 'Expired <span class="count">(%s)</span>', 'iljobboard' ),
		));

	}


}