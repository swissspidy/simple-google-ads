<?php
/**
 * Plugin Name: Simple Google Ads
 * Plugin URI:  https://github.com/swissspidy/simple-google-ads/
 * Description: Display Google Ad Manager ads.
 * Version:     1.0.0
 * Author:      Pascal Birchler
 * Author URI:  https://pascalbirchler.com
 * License:     GPL-2.0+
 * License URI: http://www.gnu.org/licenses/gpl-2.0.txt
 * Text Domain: simple-google-ads
 * Domain Path: /languages
 *
 * @package SimpleGoogleAds
 */

namespace SimpleGoogleAds;

if ( file_exists( __DIR__ . '/vendor/autoload.php' ) ) {
	require __DIR__ . '/vendor/autoload.php';
}

add_action( 'plugins_loaded', __NAMESPACE__ . '\bootstrap' );
