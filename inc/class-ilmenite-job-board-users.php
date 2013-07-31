<?php
/**
 * User Functions
 *
 * Create custom role for companies and add
 * meta fields for company info, set up registration flow, login flow
 * and require companies to fill out these fields.
 */

/**
 * Ilmenite_Job_Board_Users Class
 */
class Ilmenite_Job_Board_Users {

	/**
	 * Constructor
	 */
	public function __construct() {

		add_action( 'show_user_profile', array( $this, 'admin_user_meta_fields') );
		add_action( 'edit_user_profile', array( $this, 'admin_user_meta_fields' ) );
		add_action( 'personal_options_update', array( $this, 'save_user_meta_fields' ) );
		add_action( 'edit_user_profile_update', array( $this, 'save_user_meta_fields' ) );

	}

	/**
	 * Adds Custom Profile Fields to User Editing Page
	 */
	public function admin_user_meta_fields( $user ) {

		// Only add this if the user is in the employer role
		//if ( ! current_user_can( 'employer' ) )
			//return false;
		?>

			<h3><?php _e('Company/Employer Info', 'iljobboard'); ?></h3>

			<table class="form-table">
				<tr>
					<th>
						<label for="company_name"><?php _e('Name', 'iljobboard'); ?> <span class="description"><?php _e('(required)', 'iljobboard'); ?></span></label>
					</th>
					<td>
						<input type="text" name="company_name" id="company_name" value="<?php echo esc_attr( get_the_author_meta( 'company_name', $user->ID ) ); ?>" class="regular-text" /><br />
					</td>
				</tr>
				<tr>
					<th>
						<label for="company_address"><?php _e('Address', 'iljobboard'); ?> <span class="description"><?php _e('(required)', 'iljobboard'); ?></span></label>
					</th>
					<td>
						<input type="text" name="company_address" id="company_address" value="<?php echo esc_attr( get_the_author_meta( 'company_address', $user->ID ) ); ?>" class="regular-text" /><br />
					</td>
				</tr>
				<tr>
					<th>
						<label for="company_phone"><?php _e('Phone Number', 'iljobboard'); ?> <span class="description"><?php _e('(required)', 'iljobboard'); ?></span></label>
					</th>
					<td>
						<input type="tel" name="company_phone" id="company_phone" value="<?php echo esc_attr( get_the_author_meta( 'company_phone', $user->ID ) ); ?>" class="regular-text" /><br />
					</td>
				</tr>
				<tr>
					<th>
						<label for="company_fax"><?php _e('Fax Number', 'iljobboard'); ?></label>
					</th>
					<td>
						<input type="tel" name="company_fax" id="company_fax" value="<?php echo esc_attr( get_the_author_meta( 'company_fax', $user->ID ) ); ?>" class="regular-text" /><br />
					</td>
				</tr>
				<tr>
					<th>
						<label for="company_website"><?php _e('Website', 'iljobboard'); ?></label>
					</th>
					<td>
						<input type="url" name="company_website" id="company_website" value="<?php echo esc_attr( get_the_author_meta( 'company_website', $user->ID ) ); ?>" class="regular-text" /><br />
					</td>
				</tr>
				<tr>
					<th>
						<label for="company_logo"><?php _e('Logo', 'iljobboard'); ?></label>
					</th>
					<td>
						<input type="text" name="company_logo" id="company_logo" value="<?php echo esc_attr( get_the_author_meta( 'company_logo', $user->ID ) ); ?>" class="regular-text" /><br />
					</td>
				</tr>
			</table>
		<?php
	}

	/**
	 * Save the Custom User Profile Fields
	 */
	public function save_user_meta_fields( $user_id ) {

		// If we can't update profiles, quit.
		if ( ! current_user_can( 'edit_user', $user_id ) )
			return false;

		// Update the fields...
		update_user_meta( $user_id, 'company_name', $_POST['company_name'] );
		update_user_meta( $user_id, 'company_address', $_POST['company_address'] );
		update_user_meta( $user_id, 'company_phone', $_POST['company_phone'] );
		update_user_meta( $user_id, 'company_fax', $_POST['company_fax'] );
		update_user_meta( $user_id, 'company_website', $_POST['company_website'] );
		update_user_meta( $user_id, 'company_logo', $_POST['company_logo'] );

	}

}

new Ilmenite_Job_Board_Users();