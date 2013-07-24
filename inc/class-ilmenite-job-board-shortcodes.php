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
								Logo
							</li>
							<li class="jobs-position-info">
								<span class="jobs-company-name">Company Name Here</span>
								<span class="jobs-position-title"><?php the_title(); ?></span>
							</li>
							<li class="jobs-location">
								Location
							</li>
							<li class="jobs-meta">
								<span class="jobs-job-type job-type-part-time">Part Time</span>
								<span class="jobs-expiry-date">Apply by: Nov 5, 2013</span>
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