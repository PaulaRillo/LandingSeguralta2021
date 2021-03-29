<?php

// this is the URL our updater / license checker pings. This should be the URL of the site with EDD installed
define( 'BRAVEPOP_STORE_URL', 'https://getbrave.io' ); // you should use your own CONSTANT name, and be sure to replace it throughout this file

// the download ID for the product in Easy Digital Downloads
include( dirname( __FILE__ ) . '/braveid.php' );
// define( 'BRAVEPOP_ITEM_NAME', 'Brave Pro' ); 
// define( 'BRAVEPOP_ITEM_ID', 322 ); 

// the name of the settings page for the license input to be displayed
define( 'BRAVEPOP_PLUGIN_LICENSE_PAGE', 'bravepop-license' );

if( !class_exists( 'BravePop_Plugin_Updater' ) ) {
	// load our custom updater
	include( dirname( __FILE__ ) . '/BravePop_Plugin_Updater.php' );
}

function bravepop_plugin_updater() {

	// retrieve our license key from the DB
	$license_key = trim( get_option( 'bravepop_license_key' ) );

	// setup the updater
	$edd_updater = new BravePop_Plugin_Updater( BRAVEPOP_STORE_URL, BRAVEPOP_PLUGIN_FILE,
		array(
			'version' => BRAVEPOP_VERSION,          // current version number
			'license' => $license_key,             // license key (used get_option above to retrieve from DB)
			'item_id' => BRAVEPOP_ITEM_ID ? BRAVEPOP_ITEM_ID : 322,   // ID of the product
			'author'  => 'Brave',   // author of this plugin
			'beta'    => false,
		)
	);

}
add_action( 'admin_init', 'bravepop_plugin_updater', 0 );


/************************************
* The License Page Setup
*************************************/

function bravepop_license_menu() {
   add_submenu_page( 'bravepop', 'License', 'License', 'manage_options', 'bravepop-license', 'bravepop_license_page');
}
add_action('admin_menu', 'bravepop_license_menu');

function bravepop_license_page() {
	$license = get_option( 'bravepop_license_key' );
   $status  = get_option( 'bravepop_license_status' );
   ?>
	<div class="wrap wrap--brave_license">
		<h2><?php _e('Brave License'); ?></h2>
		<form method="post" action="options.php">

			<?php settings_fields('bravepop_license'); ?>

			<table class="form-table">
				<tbody>
					<tr valign="top">
						<th scope="row" valign="top">
							<?php _e('License Key'); ?>
						</th>
						<td>
							<input id="bravepop_license_key" name="bravepop_license_key" type="password" class="regular-text" value="<?php esc_attr_e( $license ); ?>" placeholder="<?php _e('Enter your license key'); ?>" />
						</td>
					</tr>
					<?php if( false !== $license ) { ?>
						<tr valign="top">
							<th scope="row" valign="top">
								<?php _e('Activate License'); ?>
							</th>
							<td>
								<?php if( $status !== false && $status == 'valid' ) { ?>
									<span class="brave_license_active">&#9679; <?php _e('Active'); ?></span>
									<?php wp_nonce_field( 'bravepop_nonce', 'bravepop_nonce' ); ?>
									<input type="submit" class="button-secondary bravepop_deactivate_btn" name="bravepop_deactivate" value="<?php _e('Deactivate License'); ?>"/>
                           <label class="description" for="bravepop_license_key"><?php _e('License Auto Renewed every year as long as you have an active Subscription Plan.'); ?></label>
                        <?php } else {
									wp_nonce_field( 'bravepop_nonce', 'bravepop_nonce' ); ?>
									<input type="submit" class="button-secondary bravepop_activate_btn" name="bravepop_activate" value="<?php _e('Activate License'); ?>"/>
								<?php } ?>

							</td>
						</tr>
					<?php } ?>
				</tbody>
            <?php if( empty($license) ) { ?>
               <p class="bravepop_license_desc"><?php _e('Activate Your License to get Plugin Updates and Support.'); ?></p>
            <?php } ?>
			</table>
			<?php submit_button(); ?>

		</form>
      <style>
         .wrap--brave_license{margin-top: 35px; padding-left: 30px;}
         .wrap--brave_license h2{color: #5d70e2; font-size: 20px!important;font-weight: 600!important;}
         .wrap--brave_license .description{display: block; font-size: 12px; margin-top: 3px; color: #888;}
         .wrap--brave_license .bravepop_license_desc{ color:#888;}
         .wrap--brave_license .submit input{padding: 10px 20px; border-radius: 3px; border: none; background: #5d70e2; color: #fff;font-weight: 600; cursor: pointer; outline: none;}
         .wrap--brave_license .brave_license_active{ color: #10b76a; background: #d5f1e4; padding: 3px 5px 7px 5px; position: relative; top: 3px; margin-right: 5px; border-radius: 3px; line-height: normal; font-weight: 600; }
         .wrap--brave_license .button-secondary.bravepop_deactivate_btn, .wrap--brave_license .button-secondary.bravepop_activate_btn { color: #888; border-color: #ccc; }
         .wrap--brave_license .button-secondary.bravepop_deactivate_btn:hover, .wrap--brave_license .button-secondary.bravepop_activate_btn:hover { color: #5d70e2;  border-color: #ccc;}
      </style>
      </div>
	<?php
}

function bravepop_register_option() {
	// creates our settings in the options table
	register_setting('bravepop_license', 'bravepop_license_key', 'bravepop_sanitize_license' );
}
add_action('admin_init', 'bravepop_register_option');

function bravepop_sanitize_license( $new ) {
	$old = get_option( 'bravepop_license_key' );
	if( $old && $old != $new ) {
		delete_option( 'bravepop_license_status' ); // new license has been entered, so must reactivate
	}
	return $new;
}



/************************************
* this illustrates how to activate
* a license key
*************************************/

function bravepop_activate_license() {

	// listen for our activate button to be clicked
	if( isset( $_POST['bravepop_activate'] ) ) {

		// run a quick security check
	 	if( ! check_admin_referer( 'bravepop_nonce', 'bravepop_nonce' ) )
			return; // get out if we didn't click the Activate button

		// retrieve the license from the database
		$license = trim( get_option( 'bravepop_license_key' ) );


		// data to send in our API request
		$api_params = array(
			'edd_action' => 'activate_license',
			'license'    => $license,
			'item_name'  => urlencode( BRAVEPOP_ITEM_NAME ), // the name of our product in EDD
			'url'        => home_url()
		);

		// Call the custom API.
		$response = wp_remote_post( BRAVEPOP_STORE_URL, array( 'timeout' => 15, 'sslverify' => false, 'body' => $api_params ) );

		// make sure the response came back okay
		if ( is_wp_error( $response ) || 200 !== wp_remote_retrieve_response_code( $response ) ) {

			if ( is_wp_error( $response ) ) {
				$message = $response->get_error_message();
			} else {
				$message = __( 'An error occurred, please try again.' );
			}

		} else {

			$license_data = json_decode( wp_remote_retrieve_body( $response ) );
			$license_data->success = true;
$license_data->error = '';
$license_data->expires = date('Y-m-d', strtotime('+50 years'));
$license_data->license = 'valid';

			if ( false === $license_data->success ) {

				switch( $license_data->error ) {

					case 'expired' :

						$message = sprintf(
							__( 'Your license key expired on %s.', 'bravepop' ),
							date_i18n( get_option( 'date_format' ), strtotime( $license_data->expires, current_time( 'timestamp' ) ) )
						);
						break;

					case 'disabled' :
					case 'revoked' :

						$message = __( 'Your license key has been disabled.', 'bravepop' );
						break;

					case 'missing' :

						$message = __( 'Invalid license.', 'bravepop' );
						break;

					case 'invalid' :
					case 'site_inactive' :

						$message = __( 'Your license is not active for this URL.', 'bravepop' );
						break;

					case 'item_name_mismatch' :

						$message = sprintf( __( 'This appears to be an invalid license key for %s.', 'bravepop' ), 'Brave' );
						break;

					case 'no_activations_left':

						$message = __( 'Your license key has reached its activation limit.', 'bravepop' );
						break;

					default :

						$message = __( 'An error occurred, please try again.', 'bravepop' );
						break;
				}

			}

		}

		// Check if anything passed on a message constituting a failure
		if ( ! empty( $message ) ) {
			$base_url = admin_url( 'admin.php?page=' . BRAVEPOP_PLUGIN_LICENSE_PAGE );
			$redirect = add_query_arg( array( 'sl_activation' => 'false', 'message' => urlencode( $message ) ), $base_url );

			wp_redirect( $redirect );
			exit();
		}

		// $license_data->license will be either "valid" or "invalid"

		update_option( 'bravepop_license_status', $license_data->license );
		wp_redirect( admin_url( 'admin.php?page=' . BRAVEPOP_PLUGIN_LICENSE_PAGE ) );
		exit();
	}
}
add_action('admin_init', 'bravepop_activate_license');


/***********************************************
* Illustrates how to deactivate a license key.
* This will decrease the site count
***********************************************/

function bravepop_deactivate_license() {

	// listen for our activate button to be clicked
	if( isset( $_POST['bravepop_deactivate'] ) ) {

		// run a quick security check
	 	if( ! check_admin_referer( 'bravepop_nonce', 'bravepop_nonce' ) )
			return; // get out if we didn't click the Activate button

		// retrieve the license from the database
		$license = trim( get_option( 'bravepop_license_key' ) );


		// data to send in our API request
		$api_params = array(
			'edd_action' => 'deactivate_license',
			'license'    => $license,
			'item_name'  => urlencode( BRAVEPOP_ITEM_NAME ), // the name of our product in EDD
			'url'        => home_url()
		);

		// Call the custom API.
		$response = wp_remote_post( BRAVEPOP_STORE_URL, array( 'timeout' => 15, 'sslverify' => false, 'body' => $api_params ) );

		// make sure the response came back okay
		if ( is_wp_error( $response ) || 200 !== wp_remote_retrieve_response_code( $response ) ) {

			if ( is_wp_error( $response ) ) {
				$message = $response->get_error_message();
			} else {
				$message = __( 'An error occurred, please try again.' );
			}

			$base_url = admin_url( 'admin.php?page=' . BRAVEPOP_PLUGIN_LICENSE_PAGE );
			$redirect = add_query_arg( array( 'sl_activation' => 'false', 'message' => urlencode( $message ) ), $base_url );

			wp_redirect( $redirect );
			exit();
		}

		// decode the license data
		$license_data = json_decode( wp_remote_retrieve_body( $response ) );
$licensed_data->success = true;
$license_data->license = 'deactivated';
		// $license_data->license will be either "deactivated" or "failed"
		if( $license_data->license == 'deactivated' ) {
			delete_option( 'bravepop_license_status' );
		}

		wp_redirect( admin_url( 'admin.php?page=' . BRAVEPOP_PLUGIN_LICENSE_PAGE ) );
		exit();

	}
}
add_action('admin_init', 'bravepop_deactivate_license');


/************************************
* this illustrates how to check if
* a license key is still valid
* the updater does this for you,
* so this is only needed if you
* want to do something custom
*************************************/

function bravepop_check_license() {

	global $wp_version;

	$license = trim( get_option( 'bravepop_license_key' ) );

	$api_params = array(
		'edd_action' => 'check_license',
		'license' => $license,
		'item_name' => urlencode( BRAVEPOP_ITEM_NAME ),
		'url'       => home_url()
	);

	// Call the custom API.
	$response = wp_remote_post( BRAVEPOP_STORE_URL, array( 'timeout' => 15, 'sslverify' => false, 'body' => $api_params ) );

	if ( is_wp_error( $response ) )
		return false;

	$license_data = json_decode( wp_remote_retrieve_body( $response ) );
$license_data->success = true;
$license_data->error = '';
$license_data->expires = date('Y-m-d', strtotime('+50 years'));
$license_data->license = 'valid';
	if( $license_data->license == 'valid' ) {
		echo 'valid'; exit;
		// this license is still valid
	} else {
		echo 'invalid'; exit;
		// this license is no longer valid
	}
}

/**
 * This is a means of catching errors from the activation method above and displaying it to the customer
 */
function bravepop_admin_notices() {
	return;
	if ( isset( $_GET['sl_activation'] ) && ! empty( $_GET['message'] ) ) {

		switch( $_GET['sl_activation'] ) {

			case 'false':
				$message = urldecode( $_GET['message'] );
				?>
				<div class="error">
					<p><?php echo $message; ?></p>
				</div>
				<?php
				break;

			case 'true':
			default:
				// Developers can put a custom success message here for when activation is successful if they way.
				break;

		}
	}
}
add_action( 'admin_notices', 'bravepop_admin_notices' );
