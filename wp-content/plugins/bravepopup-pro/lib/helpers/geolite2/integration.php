<?php
/**
 * Wrapper for MaxMind GeoLite2 Reader
 *
 * This class provide an interface to handle geolocation and error handling.
 *
 * Requires PHP 5.4+.
 *
 * @package WooCommerce\Classes
 * @since   3.4.0
 */
defined( 'ABSPATH' ) || exit;
/**
 * Geolite integration class.
 */
class BravePop_Geolite_Integration {
	/**
	 * MaxMind GeoLite2 database path.
	 *
	 * @var string
	 */
	private $database = '';
	/**
	 * Logger instance.
	 *
	 */
	private $log = null;
	/**
	 * Constructor.
	 *
	 * @param string $database MaxMind GeoLite2 database path.
	 */
	public function __construct( $database ) {
		$this->database = $database;
		if ( ! class_exists( 'MaxMind\\Db\\Reader', false ) ) {
			$this->require_geolite_library();
		}
	}
	/**
	 * Get country 2-letters ISO by IP address.
	 * Returns empty string when not able to find any ISO code.
	 *
	 * @param string $ip_address User IP address.
	 * @return string
	 */
	public function get_country_data( $ip_address ) {
		$countryname = ''; $countryCode = '';
		try {
			$reader = new MaxMind\Db\Reader( $this->database ); // phpcs:ignore PHPCompatibility.LanguageConstructs.NewLanguageConstructs.t_ns_separatorFound
         $data   = $reader->get( $ip_address );

			if ( isset( $data['country']['names']['en'] ) ) {
				$countryname = $data['country']['names']['en'];
         }
         if ( isset( $data['country']['iso_code'] ) ) {
				$countryCode = $data['country']['iso_code'];
			}
			$reader->close();
		} catch ( Exception $e ) {
			$this->log( $e->getMessage(), 'warning' );
		}
		return array('name'=>sanitize_text_field(  $countryname  ), 'code'=> sanitize_text_field(  $countryCode  ));
	}
	/**
	 * Logging method.
	 *
	 * @param string $message Log message.
	 * @param string $level   Log level.
	 *                        Available options: 'emergency', 'alert',
	 *                        'critical', 'error', 'warning', 'notice',
	 *                        'info' and 'debug'.
	 *                        Defaults to 'info'.
	 */
	private function log( $message, $level = 'info' ) {
		// if ( is_null( $this->log ) ) {
		// 	$this->log = wc_get_logger();
		// }
      //$this->log->log( $level, $message, array( 'source' => 'geoip' ) );
      error_log($level.' '.$message);
	}
	/**
	 * Require geolite library.
	 */
	private function require_geolite_library() {
      require_once  __DIR__ . '/Reader/Decoder.php';
		require_once  __DIR__ . '/Reader/InvalidDatabaseException.php';
		require_once  __DIR__ . '/Reader/Metadata.php';
		require_once  __DIR__ . '/Reader/Util.php';
      require_once  __DIR__ . '/Reader.php';
      
		// require_once './Reader/Decoder.php';
		// require_once './Reader/InvalidDatabaseException.php';
		// require_once './Reader/Metadata.php';
		// require_once './Reader/Util.php';
		// require_once './Reader.php';
	}
}