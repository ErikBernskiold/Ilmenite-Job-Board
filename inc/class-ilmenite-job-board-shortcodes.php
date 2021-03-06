<?php
/**
 * Shortcodes
 *
 * Adds shortcodes for various important displays:
 * - Job Board Listing
 * - Job Submission
 */

class Ilmenite_Job_Board_Shortcodes {

	public function __construct() {

		// Add jobs listing shortcode
		add_shortcode( 'ilmenite_jobs', array( $this, 'jobs_listing' ) );

		// Adds the job submission form shortcode
		add_shortcode( 'ilmenite_submit_job', array( $this, 'submit_form' ) );

		// Adds the job application form shortcode
		add_shortcode( 'ilmenite_job_application_form', array( $this, 'application_form' ) );

		// Adds the job submission form shortcode
		add_shortcode( 'ilmenite_job_dashboard', array( $this, 'employer_dashboard' ) );

	}

	public function jobs_listing( $atts ) {

		ob_start();

		// Extract the attributes in the shortcode to own variables
		extract( shortcode_atts( array(
			'amount'       => 20,
			'orderby'      => 'date',
			'order'        => 'desc',
			'show_filters' => true,
		), $atts ) );

		// Query Arguments
		$jobs_args = array(
			'post_type'           => 'il_job_board',
			'posts_per_page'      => $amount,
			'orderby'             => $orderby,
			'order'               => $order,
			'ignore_sticky_posts' => 1,
			'post_status'         => 'publish',
			'meta_query' 			 => array(
				array(
					'key'     => 'iljb_filled',
					'value'   => 1,
					'compare' => '!=',
				),
			),
		);

		$jobs = new WP_Query( $jobs_args );

		if ( $jobs->have_posts() ) : ?>

			<ul>

				<?php while ( $jobs->have_posts() ) : $jobs->the_post(); ?>

					<li>
						<ul>
							<li class="jobs-company-logo">
								<?php if ( get_user_meta( get_the_author_meta( 'ID' ), 'company_logo', true ) ) : ?>
								<a href="<?php the_permalink(); ?>"><img src="<?php echo get_user_meta( get_the_author_meta( 'ID' ), 'company_logo', true ); ?>" alt="<?php echo get_user_meta( get_the_author_meta( 'ID' ), 'company_name', true ); ?>"></a>
								<?php endif; ?>
							</li>
							<li class="jobs-position-info">
								<span class="jobs-position-title"><a href="<?php the_permalink(); ?>" title="<?php the_title(); ?>"><?php the_title(); ?></a></span>
								<span class="jobs-company-name"><?php echo get_user_meta( get_the_author_meta( 'ID' ), 'company_name', true ); ?></span>
							</li>
							<li class="jobs-location">
								<?php echo get_post_meta( get_the_ID(), 'iljb_location', true ); ?>
							</li>
							<li class="jobs-meta">
								<span class="jobs-job-type job-type-part-time">Part Time</span>
								<span class="jobs-expiry-date"><?php printf( __( 'Ends %s', 'iljobboard' ), date( 'F j, Y', strtotime( get_post_meta( get_the_ID(), 'iljb_expiry_date', true ) ) ) ); ?></span>
							</li>
						</ul>
					</li>

				<?php endwhile; ?>

			</ul>

		<?php endif;

		wp_reset_postdata();

		return '<div class="job-listing">' . ob_get_clean() . '</div>';

	}

	/**
	 * Submit Job Shortcode
	 *
	 * Shortcode for displaying the job submission form
	 */
	public function submit_form( $atts ) {

		return $GLOBALS['ilmenite_job_board']->forms->submit_form();

	}

	/**
	 * Job Application Shortcode
	 *
	 * Shortcode for displaying the job application form
	 */
	public function application_form( $atts ) {

		return $GLOBALS['ilmenite_job_board']->forms->application_form();

	}

	/**
	 * Employer Dashboard Shortcode
	 *
	 * Lists all the job openings from the logged in
	 * user, which is assumed to be the employer.
	 */
	public function employer_dashboard( $atts ) {

		ob_start();

		if ( ! is_user_logged_in() )
			return get_job_board_login();

		// Query Arguments
		$jobs_args = array(
			'post_type'           => 'il_job_board',
			'posts_per_page'      => -1,
			'orderby'             => 'date',
			'order'               => 'desc',
			'ignore_sticky_posts' => 1,
			'post_status'         => 'any',
			'author'					 => get_current_user_id(),
		);

		$jobs = new WP_Query( $jobs_args );

		if ( $jobs->have_posts() ) : ?>

			<ul>

				<?php while ( $jobs->have_posts() ) : $jobs->the_post(); ?>

					<li>
						<ul>
							<li class="jobs-actions">
								<a href="#" class="job-edit-link">Edit</a>
							</li>
							<li class="jobs-position-info">
								<span class="jobs-position-title"><a href="<?php the_permalink(); ?>" title="<?php the_title(); ?>"><?php the_title(); ?></a></span>
								<span class="jobs-company-name"><?php echo get_user_meta( get_the_author_meta( 'ID' ), 'company_name', true ); ?></span>
							</li>
							<li class="jobs-location">
								<?php echo get_post_meta( get_the_ID(), 'iljb_location', true ); ?>
							</li>
							<li class="jobs-meta">
								<span class="jobs-job-type job-type-part-time">Part Time</span>
								<span class="jobs-expiry-date"><?php printf( __( 'Ends %s', 'iljobboard' ), date( 'F j, Y', strtotime( get_post_meta( get_the_ID(), 'iljb_expiry_date', true ) ) ) ); ?></span>
							</li>
						</ul>
					</li>

				<?php endwhile; ?>

			</ul>

		<?php endif;

		wp_reset_postdata();

		return '<div class="job-listing">' . ob_get_clean() . '</div>';

	}

}

new Ilmenite_Job_Board_Shortcodes();