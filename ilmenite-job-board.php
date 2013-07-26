<?php
/**
 * Plugin Name: Ilmenite Job Board
 * Plugin URI: https://github.com/ErikBernskiold/Ilmenite-Job-Board
 * Author: XLD Studios
 * Author URI: http://www.xldstudios.com/
 * Description: A Job Board for WordPress.
 * Version: 1.0
 * Requires at least: 3.5
 * Tested up to: 3.5.2
 * License: GPL2
 */

// Exit if accessed directly
if ( ! defined( 'ABSPATH' ) )
	exit;

/**
 * Ilmenite Job Board Class
 */
class Ilmenite_Job_Board {

	/**
	 * Constructor
	 */
	public function __construct() {

		// Define some constants
		define( 'IL_JOB_BOARD_VERSION', '1.0' );
		define( 'IL_JOB_BOARD_PLUGIN_DIR', untrailingslashit( plugin_dir_path( __FILE__ ) ) );
		define( 'IL_JOB_BOARD_PLUGIN_URL', untrailingslashit( plugins_url( basename( plugin_dir_path( __FILE__ ) ), basename( __FILE__ ) ) ) );

		// Include classes and functions
		include( 'ilmenite-job-board-functions.php' );

		include( 'inc/class-ilmenite-job-board-post-types.php' );
		include( 'inc/class-ilmenite-job-board-shortcodes.php' );
		include( 'inc/class-ilmenite-job-board-users.php' );
		include( 'inc/class-ilmenite-job-board-forms.php' );

		// Initialize classes...
		$this->post_types = new Ilmenite_Job_Board_Post_Types();
		$this->forms      = new Ilmenite_Job_Board_Forms();

		// Run these at activation...
		register_activation_hook( basename( dirname( __FILE__ ) ) . '/' . basename( __FILE__ ), create_function( "", "include( 'includes/class-ilmenite-job-board-install.php' );" ), 10 );
		register_activation_hook( basename( dirname( __FILE__ ) ) . '/' . basename( __FILE__ ), 'flush_rewrite_rules', 15 );

		// Add the textdomain and support translation
		add_action( 'plugins_loaded', array( $this, 'add_textdomain' ) );

		// Add Stylesheets
		add_action( 'wp_enqueue_scripts', array( $this, 'stylesheets' ) );

		// Add Scripts
		add_action( 'wp_enqueue_scripts', array( $this, 'scripts' ) );

		// Add plugin updater
		add_action( 'init', array( $this, 'plugin_update' ) );
	}

	/**
	 * Add textdomain for plugin
	 */
	public function add_textdomain() {
		load_plugin_textdomain( 'iljobboard', false, dirname( plugin_basename( __FILE__ ) ) . '/languages/' );
	}

	/**
	 * Auto Update Support
	 *
	 * Adds support for auto-updating from GitHub repository.
	 */
	public function plugin_update() {

		// Include updater class
		include( 'inc/class-github-updater.php' );

		define( 'WP_GITHUB_FORCE_UPDATE', true );

		if ( is_admin() ) { // note the use of is_admin() to double check that this is happening in the admin

			$config = array(
				'slug'               => plugin_basename( __FILE__ ),
				'proper_folder_name' => 'ilmenite-job-board',
				'api_url'            => 'https://api.github.com/repos/ErikBernskiold/Ilmenite-Job-Board',
				'raw_url'            => 'https://raw.github.com/ErikBernskiold/Ilmenite-Job-Board/master',
				'github_url'         => 'https://github.com/ErikBernskiold/Ilmenite-Job-Board',
				'zip_url'            => 'https://github.com/ErikBernskiold/Ilmenite-Job-Board/archive/master.zip',
				'sslverify'          => true,
				'requires'           => '3.5',
				'tested'             => '3.5.2',
				'readme'             => 'README.md',
			);

			new WP_GitHub_Updater( $config );

		}

	}

	/**
	 * Load Styles
	 */
	public function stylesheets() {

		wp_register_style( 'job-board', IL_JOB_BOARD_PLUGIN_URL . '/assets/css/job-board.css' );

		wp_enqueue_style( 'job-board' );

	}

	/**
	 * Load Scripts
	 */
	public function scripts() {

		// Make sure jQuery is loaded
		wp_enqueue_script( 'jquery' );

	}


}

// Initialize everything
$GLOBALS['ilmenite_job_board'] = new Ilmenite_Job_Board();