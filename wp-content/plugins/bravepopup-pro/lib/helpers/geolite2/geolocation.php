<?php
/**
 * Geolocation class
 *
 * Handles geolocation and updating the geolocation database.
 *
 * This product includes GeoLite data created by MaxMind, available from http://www.maxmind.com.
 *
 * @package WooCommerce/Classes
 * @version 3.4.0
 */

defined( 'ABSPATH' ) || exit;

/**
 * BravePop_Geolocation Class.
 */
class BravePop_Geolocation {


    /**
     * GeoLite2 DB.
     *
     * @since 3.4.0
     */
    //const GEOLITE2_DB = 'http://geolite.maxmind.com/download/geoip/database/GeoLite2-Country.tar.gz';
    const GEOLITE2_DB = 'https://media.getbrave.io/GeoLite2-Country.mmdb';
    //https://download.db-ip.com/free/dbip-country-lite-2020-01.mmdb.gz

    /**
     * API endpoints for looking up user IP address.
     *
     * @var array
     */
    private static $ip_lookup_apis = array(
        'ipify'             => 'http://api.ipify.org/',
        'ipecho'            => 'http://ipecho.net/plain',
        'ident'             => 'http://ident.me',
        'whatismyipaddress' => 'http://bot.whatismyipaddress.com',
    );

    /**
     * API endpoints for geolocating an IP address
     *
     * @var array
     */
    private static $geoip_apis = array(
        'ipinfo.io'  => 'https://ipinfo.io/%s/json',
        'ip-api.com' => 'http://ip-api.com/json/%s',
    );

    /**
     * Check if server supports MaxMind GeoLite2 Reader.
     *
     * @since 3.4.0
     * @return bool
     */
    private static function supports_geolite2() {
        return version_compare( PHP_VERSION, '5.4.0', '>=' );
    }

    /**
     * Check if geolocation is enabled.
     *
     * @since 3.4.0
     * @param string $current_settings Current geolocation settings.
     * @return bool
     */
    private static function is_geolocation_enabled( $current_settings ) {
        return in_array( $current_settings, array( 'geolocation', 'geolocation_ajax' ), true );
    }

    /**
     * Prevent geolocation via MaxMind when using legacy versions of php.
     *
     * @since 3.4.0
     * @param string $default_customer_address current value.
     * @return string
     */
    public static function disable_geolocation_on_legacy_php( $default_customer_address ) {
        if ( self::is_geolocation_enabled( $default_customer_address ) ) {
            $default_customer_address = 'base';
        }

        return $default_customer_address;
    }

    /**
     * Hook in geolocation functionality.
     */
    public static function init() {
        if ( self::supports_geolite2() ) {
            add_action( 'bravepop_geoip_updater', array( __CLASS__, 'update_database' ) );
        }
    }

    /**
     * Maybe trigger a DB update for the first time.
     *
     * @param  string $new_value New value.
     * @param  string $old_value Old value.
     * @return string
     */
    public static function maybe_update_database( $new_value, $old_value ) {
        if ( $new_value !== $old_value && self::is_geolocation_enabled( $new_value ) ) {
            self::update_database();
        }

        return $new_value;
    }

    /**
     * Get current user IP Address.
     *
     * @return string
     */
    public static function get_ip_address() {
        if ( isset( $_SERVER['HTTP_X_REAL_IP'] ) ) { // WPCS: input var ok, CSRF ok.
            return sanitize_text_field( wp_unslash( $_SERVER['HTTP_X_REAL_IP'] ) );  // WPCS: input var ok, CSRF ok.
        } elseif ( isset( $_SERVER['HTTP_X_FORWARDED_FOR'] ) ) { // WPCS: input var ok, CSRF ok.
            // Proxy servers can send through this header like this: X-Forwarded-For: client1, proxy1, proxy2
            // Make sure we always only send through the first IP in the list which should always be the client IP.
            return (string) rest_is_ip_address( trim( current( preg_split( '/,/', sanitize_text_field( wp_unslash( $_SERVER['HTTP_X_FORWARDED_FOR'] ) ) ) ) ) ); // WPCS: input var ok, CSRF ok.
        } elseif ( isset( $_SERVER['REMOTE_ADDR'] ) ) { // @codingStandardsIgnoreLine
            return sanitize_text_field( wp_unslash( $_SERVER['REMOTE_ADDR'] ) ); // @codingStandardsIgnoreLine
        }
        return '';
    }

    //Adding missing wc_clean function
    public static function bravepop_clean( $var ) {
      if ( is_array( $var ) ) {
          return array_map( 'bravepop_clean', $var );
      } else {
          return is_scalar( $var ) ? sanitize_text_field( $var ) : $var;
      }
   }

    /**
     * Get user IP Address using an external service.
     * This can be used as a fallback for users on localhost where
     * get_ip_address() will be a local IP and non-geolocatable.
     *
     * @return string
     */
    public static function get_external_ip_address() {
        $external_ip_address = '0.0.0.0';

        if ( '' !== self::get_ip_address() ) {
            $transient_name      = 'external_ip_address_' . self::get_ip_address();
            $external_ip_address = get_transient( $transient_name );
        }

        if ( false === $external_ip_address ) {
            $external_ip_address     = '0.0.0.0';
            $ip_lookup_services      = apply_filters( 'bravepop_geolocation_ip_lookup_apis', self::$ip_lookup_apis );
            $ip_lookup_services_keys = array_keys( $ip_lookup_services );
            shuffle( $ip_lookup_services_keys );

            foreach ( $ip_lookup_services_keys as $service_name ) {
                $service_endpoint = $ip_lookup_services[ $service_name ];
                $response         = wp_safe_remote_get( $service_endpoint, array( 'timeout' => 2 ) );

                if ( ! is_wp_error( $response ) && rest_is_ip_address( $response['body'] ) ) {
                    $external_ip_address = apply_filters( 'bravepop_geolocation_ip_lookup_api_response', self::bravepop_clean( $response['body'] ), $service_name );
                    break;
                }
            }

            set_transient( $transient_name, $external_ip_address, WEEK_IN_SECONDS );
        }

        return $external_ip_address;
    }

    /**
     * Geolocate an IP address.
     *
     * @param  string $ip_address   IP Address.
     * @param  bool   $fallback     If true, fallbacks to alternative IP detection (can be slower).
     * @param  bool   $api_fallback If true, uses geolocation APIs if the database file doesn't exist (can be slower).
     * @return array
     */
    public static function geolocate_ip( $ip_address = '', $fallback = false, $api_fallback = true ) {
        // Filter to allow custom geolocation of the IP address.
        $country_name = apply_filters( 'bravepop_geolocate_ip', false, $ip_address, $fallback, $api_fallback );
        $country_code = apply_filters( 'bravepop_geolocate_ip_code', false, $ip_address, $fallback, $api_fallback );

        if ( false === $country_name ) {
            // If GEOIP is enabled in CloudFlare, we can use that (Settings -> CloudFlare Settings -> Settings Overview).
            if ( ! empty( $_SERVER['HTTP_CF_IPCOUNTRY'] ) ) { // WPCS: input var ok, CSRF ok.
                $country_name = strtoupper( sanitize_text_field( wp_unslash( $_SERVER['HTTP_CF_IPCOUNTRY'] ) ) ); // WPCS: input var ok, CSRF ok.
                $country_code = strtoupper( sanitize_text_field( wp_unslash( $_SERVER['HTTP_CF_IPCOUNTRY'] ) ) ); 
            } else {
                $ip_address = $ip_address ? $ip_address : self::get_ip_address();
                $database   = self::get_local_database_path();

                //error_log('#### supports_geolite2: '. json_encode(self::supports_geolite2()). ' ');
                //error_log('#### $database exists: '. json_encode(file_exists( $database ) ). ' ');

                if ( self::supports_geolite2() && file_exists( $database ) ) {
                     $countryData = self::geolocate_via_db( $ip_address, $database );
                     $country_name = isset($countryData['name']) ? $countryData['name'] : '';
                     $country_code = isset($countryData['code']) ? $countryData['code'] : '';
                     
                } elseif ( $api_fallback ) {
                    $country_name = self::geolocate_via_api( $ip_address );
                } else {
                    $country_name = '';
                }
                
                if ( ! $country_name && $fallback ) {
                    // May be a local environment - find external IP.
                    return self::geolocate_ip( self::get_external_ip_address(), false, $api_fallback );
                }
                
            }
        }

        return array(
            'country' => $country_name,
            'iso_code' => $country_code,
            'state'   => '',
        );
    }

    /**
     * Path to our local db.
     *
     * @param  string $deprecated Deprecated since 3.4.0.
     * @return string
     */
    public static function get_local_database_path( $deprecated = '2' ) {
        return apply_filters( 'bravepop_geolocation_local_database_path', WP_CONTENT_DIR . '/uploads/GeoLite2-Country.mmdb', $deprecated );
    }

    /**
     * Update geoip database.
     *
     * Extract files with PharData. Tool built into PHP since 5.3.
     */
    public static function update_database() {
        if ( ! self::supports_geolite2() ) {
            error_log( 'Requires PHP 5.4 to be able to download MaxMind GeoLite2 database', array( 'source' => 'geolocation' ) );
            return;
        }

        require_once ABSPATH . 'wp-admin/includes/file.php';

        $database             = 'GeoLite2-Country.mmdb';
        $target_database_path = self::get_local_database_path();
        $tmp_database_path    = download_url( self::GEOLITE2_DB );

        if ( ! is_wp_error( $tmp_database_path ) ) {
            copy( $tmp_database_path, $target_database_path );
            unlink( $tmp_database_path ); // must unlink afterwards
            BravePopup_Settings::save_settings( array( 'geodb_update' => time()) );
            // WP_Filesystem();
            // global $wp_filesystem;
            // try {
            //     // Make sure target dir exists.
            //     $wp_filesystem->mkdir( dirname( $target_database_path ) );
            //    error_log($tmp_database_path);
            //     // Extract files with PharData. Tool built into PHP since 5.3.
            //     $file      = new PharData( $tmp_database_path ); // phpcs:ignore PHPCompatibility.Classes.NewClasses.phardataFound
            //     //$file_path = trailingslashit( $file->current()->getFileName() ) . $database;
            //     $file_path = $database;
            //     $file->extractTo( dirname( $tmp_database_path ), $file_path, true );

            //     // Move file and delete temp.
            //     $wp_filesystem->move( trailingslashit( dirname( $tmp_database_path ) ) . $file_path, $target_database_path, true );
            //     $wp_filesystem->delete( trailingslashit( dirname( $tmp_database_path ) ) . $file->current()->getFileName() );
            //     //error_log('Successfully downlaoded maxmind db.');

            // } catch ( Exception $e ) {
            //    error_log('Brave Popup Error: Updating MaxMind GEO Database: '.$e->getMessage());
            //    // Reschedule download of DB.
            //    wp_clear_scheduled_hook( 'bravepop_geoip_updater' );
            //    wp_schedule_event( strtotime( 'first tuesday of next month' ), 'monthly', 'bravepop_geoip_updater' );
            // }
            // // Delete temp file regardless of success.
            // $wp_filesystem->delete( $tmp_database_path );
        } else {
            error_log('Brave Error: Unable to download GeoIP Database: '.$tmp_database_path->get_error_message());
        }
    }

    /**
     * Use MAXMIND GeoLite database to geolocation the user.
     *
     * @param  string $ip_address IP address.
     * @param  string $database   Database path.
     * @return string
     */
    private static function geolocate_via_db( $ip_address, $database ) {
        if ( ! class_exists( 'BravePop_Geolite_Integration', false ) ) {
            //require_once  './integration.php';
            require_once  __DIR__ . '/integration.php';
        }

        $geolite = new BravePop_Geolite_Integration( $database );

        return $geolite->get_country_data( $ip_address );
    }

    /**
     * Use APIs to Geolocate the user.
     *
     * Geolocation APIs can be added through the use of the bravepop_geolocation_geoip_apis filter.
     * Provide a name=>value pair for service-slug=>endpoint.
     *
     * If APIs are defined, one will be chosen at random to fulfil the request. After completing, the result
     * will be cached in a transient.
     *
     * @param  string $ip_address IP address.
     * @return string
     */
    private static function geolocate_via_api( $ip_address ) {
        //$country_code = get_transient( 'geoip_' . $ip_address );
        $country_code  = '';
        if ( false === $country_code ) {
            $geoip_services = apply_filters( 'bravepop_geolocation_geoip_apis', self::$geoip_apis );

            if ( empty( $geoip_services ) ) {
                return '';
            }

            $geoip_services_keys = array_keys( $geoip_services );

            shuffle( $geoip_services_keys );

            foreach ( $geoip_services_keys as $service_name ) {
                $service_endpoint = $geoip_services[ $service_name ];
                $response         = wp_safe_remote_get( sprintf( $service_endpoint, $ip_address ), array( 'timeout' => 2 ) );

                if ( ! is_wp_error( $response ) && $response['body'] ) {
                    switch ( $service_name ) {
                        case 'ipinfo.io':
                            $data         = json_decode( $response['body'] );
                            $country_code = isset( $data->country ) ? $data->country : '';
                            break;
                        case 'ip-api.com':
                            $data         = json_decode( $response['body'] );
                            $country_code = isset( $data->countryCode ) ? $data->countryCode : ''; // @codingStandardsIgnoreLine
                            break;
                        default:
                            $country_code = apply_filters( 'bravepop_geolocation_geoip_response_' . $service_name, '', $response['body'] );
                            break;
                    }

                    $country_code = sanitize_text_field( strtoupper( $country_code ) );

                    if ( $country_code ) {
                        break;
                    }
                }
            }

            set_transient( 'geoip_' . $ip_address, $country_code, WEEK_IN_SECONDS );
        }

        return $country_code;
    }
}

BravePop_Geolocation::init();

add_action("plugins_loaded", "bravepop_update_geodb");

function bravepop_update_geodb()
{
   if(is_admin()){
      $currentSettings = get_option('_bravepopup_settings');
      $lastUpdated = $currentSettings && isset($currentSettings['geodb_update']) ? $currentSettings['geodb_update'] *1000 : 0;
      $unixNinetyDays = 2678400000 * 3; //90 Days
      $currentTime = time() *1000;
      $diffTime = $currentTime - $lastUpdated;
   
      // error_log('Last Updated: '.$lastUpdated.' currentTime: '.$currentTime);
      // error_log($diffTime);
      if($diffTime >= $unixNinetyDays || !file_exists( WP_CONTENT_DIR . '/uploads/GeoLite2-Country.mmdb' )){
         //error_log('GEO DB Expired or Does not exist. Downloading GEO DB...');
         $geo_instance = new BravePop_Geolocation();
         $geo_instance->update_database();
      }
   
   }
}