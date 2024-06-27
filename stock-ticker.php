<?php
/**
 * @package Stock Ticker
 *
 * Plugin Name: Stock Ticker
 * Plugin URI:  https://urosevic.net/wordpress/plugins/stock-ticker/
 * Description: Easy add customizable moving or static ticker tapes with stock information for custom stock symbols.
 * Version:     3.24.6
 * Author:      Aleksandar Urošević
 * Author URI:  https://urosevic.net
 * License:     GNU GPLv3
 * License URI: https://www.gnu.org/licenses/gpl-3.0.txt
 * Text Domain: stock-ticker
 */

/**
 * Copyright 2014-2024 Aleksandar Urosevic (urke.kg@gmail.com)
 *
 * This program is free software; you can redistribute it and/or modify
 * it under the terms of the GNU General Public License as published by
 * the Free Software Foundation; either version 3 of the License, or
 * (at your option) any later version.
 *
 * This program is distributed in the hope that it will be useful,
 * but WITHOUT ANY WARRANTY; without even the implied warranty of
 * MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
 * GNU General Public License for more details.
 *
 * You should have received a copy of the GNU General Public License
 * along with this program.  If not, see <https://www.gnu.org/licenses/>.
 */

// Exit if accessed directly
if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

define( 'WPAU_STOCK_TICKER_VER', '3.24.6' );
define( 'WPAU_STOCK_TICKER_DB_VER', 11 );
define( 'WPAU_STOCK_TICKER_DIR', __DIR__ );
define( 'WPAU_STOCK_TICKER_PLUGIN_URL', plugin_dir_url( __FILE__ ) );
define( 'WPAU_STOCK_TICKER_PLUGIN_FILE', plugin_basename( __FILE__ ) );

require_once 'classes/class-wpau-stock-ticker.php';
if ( class_exists( 'Wpau_Stock_Ticker' ) ) {
	// Instantiate the plugin class.
	global $wpau_stockticker;
	if ( empty( $wpau_stockticker ) ) {
		$wpau_stockticker = new Wpau_Stock_Ticker();
	}
} // END class_exists( 'Wpau_Stock_Ticker' )

if ( ! function_exists( 'sanitize_html_classes' ) ) {
	/**
	 * Sanitizes an HTML classnames to ensure it only contains valid characters.
	 *
	 * Strips the string down to A-Z,a-z,0-9,_,-, and space
	 * If this results in an empty string then it will return the alternative value supplied.
	 *
	 * @param string $classes    The classnames to be sanitized (multiple classnames separated by space)
	 * @param string $fallback   Optional. The value to return if the sanitization ends up as an empty string.
	 *                           Defaults to an empty string.
	 *
	 * @return string            The sanitized value
	 */
	function sanitize_html_classes( $classes, $fallback = '' ) {
		// Strip out any %-encoded octets.
		$sanitized = preg_replace( '|%[a-fA-F0-9][a-fA-F0-9]|', '', $classes );

		// Limit to A-Z, a-z, 0-9, '_', '-' and ' ' (for multiple classes).
		$sanitized = trim( preg_replace( '/[^A-Za-z0-9\_\ \-]/', '', $sanitized ) );

		if ( '' === $sanitized && $fallback ) {
			return sanitize_html_classes( $fallback );
		}

		/**
		 * Filters a sanitized HTML class string.
		 *
		 * @param string $sanitized The sanitized HTML class.
		 * @param string $classes   HTML class before sanitization.
		 * @param string $fallback  The fallback string.
		 */
		return apply_filters( 'sanitize_html_classes', $sanitized, $classes, $fallback );
	}
}
