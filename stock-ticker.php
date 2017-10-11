<?php
/**
Plugin Name: Stock Ticker
Plugin URI: https://urosevic.net/wordpress/plugins/stock-ticker/
Description: Easy add customizable moving or static ticker tapes with stock information for custom stock symbols.
Version: 0.2.3
Author: Aleksandar Urosevic
Author URI: https://urosevic.net
License: GNU GPL3
 * @package Stock Ticker
 */

/**
Copyright 2014-2017 Aleksandar Urosevic (urke.kg@gmail.com)

This program is free software; you can redistribute it and/or modify
it under the terms of the GNU General Public License as published by
the Free Software Foundation; either version 3 of the License, or
(at your option) any later version.

This program is distributed in the hope that it will be useful,
but WITHOUT ANY WARRANTY; without even the implied warranty of
MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
GNU General Public License for more details.

You should have received a copy of the GNU General Public License
along with this program.  If not, see <http://www.gnu.org/licenses/>.
 */

/**
Google Finance Disclaimer <https://www.google.com/intl/en-US/googlefinance/disclaimer/>

Data is provided by financial exchanges and may be delayed as specified
by financial exchanges or our data providers. Google does not verify any
data and disclaims any obligation to do so.

Google, its data or content providers, the financial exchanges and
each of their affiliates and business partners (A) expressly disclaim
the accuracy, adequacy, or completeness of any data and (B) shall not be
liable for any errors, omissions or other defects in, delays or
interruptions in such data, or for any actions taken in reliance thereon.
Neither Google nor any of our information providers will be liable for
any damages relating to your use of the information provided herein.
As used here, “business partners” does not refer to an agency, partnership,
or joint venture relationship between Google and any such parties.

You agree not to copy, modify, reformat, download, store, reproduce,
reprocess, transmit or redistribute any data or information found herein
or use any such data or information in a commercial enterprise without
obtaining prior written consent. All data and information is provided “as is”
for personal informational purposes only, and is not intended for trading
purposes or advice. Please consult your broker or financial representative
to verify pricing before executing any trade.

Either Google or its third party data or content providers have exclusive
proprietary rights in the data and information provided.

Please find all listed exchanges and indices covered by Google along with
their respective time delays from the table on the left.

Advertisements presented on Google Finance are solely the responsibility
of the party from whom the ad originates. Neither Google nor any of its
data licensors endorses or is responsible for the content of any advertisement
or any goods or services offered therein.
 */

define( 'WPAU_STOCK_TICKER_VER', '0.2.3' );

if ( ! class_exists( 'Wpau_Stock_Ticker' ) ) {

	/**
	 * Wpau_Stock_Ticker Class provide main plugin functionality
	 *
	 * @category Class
	 * @package Stock Ticker
	 * @author Aleksandar Urosevic
	 * @license http://www.gnu.org/copyleft/gpl.html GNU General Public License
	 * @link https://urosevic.net
	 */
	class Wpau_Stock_Ticker {

		/**
		 * Global default options
		 * @var null
		 */
		public static $defaults = null;

		/**
		 * Construct the plugin object
		 */
		public function __construct() {
			// Initialize default settings
			self::$defaults = self::defaults();

			// Installation and uninstallation hooks.
			register_activation_hook( __FILE__, array( $this, 'activate' ) );
			register_deactivation_hook( __FILE__, array( $this, 'deactivate' ) );

			// Register AJAX
			add_action( 'wp_ajax_stock_ticker_load', array( $this, 'ajax_stock_ticker_load' ) );
			add_action( 'wp_ajax_nopriv_stock_ticker_load', array( $this, 'ajax_stock_ticker_load' ) );

			// Add Settings page link to plugin actions cell.
			$plugin_file = plugin_basename( __FILE__ );
			add_filter( "plugin_action_links_$plugin_file", array( $this, 'plugin_settings_link' ) );

			// Update links in plugin row on Plugins page.
			add_filter( 'plugin_row_meta', array( $this, 'add_plugin_meta_links' ), 10, 2 );

			// Load colour picker scripts on plugin settings page and on widgets/customizer.
			add_action( 'admin_enqueue_scripts', array( $this, 'enqueue_colour_picker' ) );

			// Enqueue frontend scripts.
			add_action( 'wp_enqueue_scripts', array( $this, 'enqueue_scripts' ) );

			// Register stock_ticker shortcode.
			add_shortcode( 'stock_ticker', array( $this, 'shortcode' ) );

			// Initialize Settings.
			require_once( sprintf( '%s/inc/settings.php', dirname( __FILE__ ) ) );
			// Initialize Widget.
			require_once( sprintf( '%s/inc/widget.php', dirname( __FILE__ ) ) );

			$wpau_stock_ticker_settings = new WPAU_Stock_Ticker_Settings();

			// Cleanup transients
			if ( ! empty( $_GET['stockticker_purge_cache'] ) ) {
				self::clean_transients();
			}
		} // END public function __construct()

		/**
		 * Defaults
		 */
		public static function defaults() {
			$defaults = array(
				'symbols'         => 'AAPL,MSFT,INTC',
				'show'            => 'name',
				'zero'            => '#454545',
				'minus'           => '#D8442F',
				'plus'            => '#009D59',
				'cache_timeout'   => '180', // 3 minutes
				'template'        => '%company% %price% %change% %changep%',
				'error_message'   => 'Unfortunately, we could not get stock quotes this time.',
				'legend'          => "AAPL;Apple Inc.\nFB;Facebook, Inc.\nCSCO;Cisco Systems, Inc.\nGOOG;Google Inc.\nINTC;Intel Corporation\nLNKD;LinkedIn Corporation\nMSFT;Microsoft Corporation\nTWTR;Twitter, Inc.\nBABA;Alibaba Group Holding Limited\nIBM;International Business Machines Corporationn\n.DJI;Dow Jones Industrial Average\nEURGBP;Euro (€) ⇨ British Pound Sterling (£)",
				'style'           => 'font-family:"Open Sans",Helvetica,Arial,sans-serif;font-weight:normal;font-size:14px;',
				'timeout'         => 2,
				'refresh'         => false,
				'refresh_timeout' => 5 * MINUTE_IN_SECONDS,
				'speed'           => 50,
				'globalassets'    => false,
			);
			$options = wp_parse_args( get_option( 'stock_ticker_defaults' ), $defaults );
			return $options;
		} // END public static function defaults()

		/**
		 * Activate the plugin
		 */
		public static function activate() {
			// Transit old settings to new format.
			$defaults = self::$defaults;
			if ( get_option( 'st_symbols' ) ) {
				$defaults['symbols'] = get_option( 'st_symbols' );
				delete_option( 'st_symbols' );
			}
			if ( get_option( 'st_show' ) ) {
				$defaults['show'] = get_option( 'st_show' );
				delete_option( 'st_show' );
			}
			if ( get_option( 'st_quote_zero' ) ) {
				$defaults['zero'] = get_option( 'st_quote_zero' );
				delete_option( 'st_quote_zero' );
			}
			if ( get_option( 'st_quote_minus' ) ) {
				$defaults['minus'] = get_option( 'st_quote_minus' );
				delete_option( 'st_quote_minus' );
			}
			if ( get_option( 'st_quote_plus' ) ) {
				$defaults['plus'] = get_option( 'st_quote_plus' );
				delete_option( 'st_quote_plus' );
			}
			update_option( 'stock_ticker_defaults', $defaults );
		} // END public static function activate()

		/**
		 * Deactivate the plugin
		 */
		public static function deactivate() {
			// Do nothing.
		} // END public static function deactivate()

		/**
		 * Delete Stock Ticker Transients
		 */
		public static function clean_transients() {
			global $wpdb;
			$ret = $wpdb->query( "DELETE FROM $wpdb->options WHERE option_name LIKE '%\_transient\_st\_json\_%' OR option_name LIKE '%\_transient\_timeout\_st\_json\_%'" );
		} // END public static function clean_transients() {

		/**
		 * Enqueue the colour picker and admin style
		 */
		public static function enqueue_colour_picker( $hook ) {
			if ( 'settings_page_wpau_stock_ticker' == $hook ) {
				wp_enqueue_style( 'wp-color-picker' );
				wp_enqueue_script( 'wp-color-picker' );
				wp_enqueue_style(
					'stock-ticker',
					plugin_dir_url( __FILE__ ) . 'assets/css/admin.css',
					array(),
					WPAU_STOCK_TICKER_VER
				);

			}
		} // END function wpau_enqueue_colour_picker()

		/**
		 * Enqueue frontend assets
		 */
		public static function enqueue_scripts() {
			$defaults = self::$defaults;
			$upload_dir = wp_upload_dir();

			wp_enqueue_script(
				'jquery-webticker',
				plugin_dir_url( __FILE__ ) . ( WP_DEBUG ? 'assets/js/jquery.webticker.js' : 'assets/js/jquery.webticker.min.js' ),
				array( 'jquery' ),
				'2.2.0.1',
				true
			);
			wp_enqueue_style(
				'stock-ticker',
				plugin_dir_url( __FILE__ ) . 'assets/css/stock-ticker.css',
				array(),
				WPAU_STOCK_TICKER_VER
			);
			wp_enqueue_style(
				'stock-ticker-custom',
				set_url_scheme( $upload_dir['baseurl'] ) . '/stock-ticker-custom.css',
				array(),
				WPAU_STOCK_TICKER_VER
			);

			wp_register_script(
				'stock-ticker',
				plugin_dir_url( __FILE__ ) . ( WP_DEBUG ? 'assets/js/jquery.stockticker.js' : 'assets/js/jquery.stockticker.min.js' ),
				array( 'jquery', 'jquery-webticker' ),
				WPAU_STOCK_TICKER_VER,
				true
			);
			wp_localize_script(
				'stock-ticker',
				'stockTickerJs',
				array( 'ajax_url' => admin_url( 'admin-ajax.php' ) )
			);
			// Enqueue script parser
			if ( $defaults['globalassets'] ) {
				wp_enqueue_script( 'stock-ticker' );
			}

			// Register refresh script if option is enabled
			if ( ! empty( $defaults['refresh'] ) ) {
				wp_register_script(
					'stock-ticker-refresh',
					set_url_scheme( $upload_dir['baseurl'] ) . '/stock-ticker-refresh.js',
					array( 'jquery', 'jquery-webticker', 'stock-ticker' ),
					WPAU_STOCK_TICKER_VER,
					true
				);
				wp_enqueue_script( 'stock-ticker-refresh' );
			}

		} // END public static function enqueue_scripts()

		public function ajax_stock_ticker_load() {
			##TODO: Provide error message if any of params missing + add nonce check
			if ( ! empty( $_POST['symbols'] ) ) {
				// Sanitize data
				$symbols   = strip_tags( $_POST['symbols'] );
				$show      = strip_tags( $_POST['show'] );
				$static    = (int) $_POST['static'];
				$nolink    = (int) $_POST['nolink'];
				$speed     = (int) $_POST['speed'];
				$empty     = (int) $_POST['empty'];
				$duplicate = (int) $_POST['duplicate'];
				$class     = strip_tags( $_POST['class'] );

				$result['status']  = 'success';
				$result['speed']   = $speed;
				$result['message'] = self::stock_ticker( $symbols, $show, $static, $nolink, $empty, $duplicate, $class );
			} else {
				$result['status']  = 'error';
				$result['message'] = 'Error ocurred: No symbols provided';
			}
			$result = json_encode( $result );
			echo $result;
			wp_die();
		}

		/**
		 * Generate and output stock ticker block
		 * @param  string $symbols Comma separated array of symbols.
		 * @param  string $show    What to show (name or symbol).
		 * @param  bool   $static  Request for static (non-animated) block.
		 * @param  bool   $nolink  Request for unlinked quotes.
		 * @return string          Composed HTML for block.
		 */
		public static function stock_ticker( $symbols, $show, $static, $nolink, $empty = true, $duplicate = false, $class = '' ) {

			if ( ! empty( $symbols ) ) {

				// Get legend for company names.
				$defaults = self::$defaults;

				// Get fresh or from transient cache stock quote.
				$st_transient_id = 'st_json_' . md5( $symbols );

				$matrix = explode( "\n", $defaults['legend'] );
				$msize = count( $matrix );
				for ( $m = 0; $m < $msize; ++$m ) {
					$line = explode( ';', $matrix[ $m ] );
					if ( ! empty( $line[0] ) && ! empty( $line[1] ) ) {
						$legend[ strtoupper( trim( $line[0] ) ) ] = trim( $line[1] );
					}
				}
				unset( $m, $msize, $matrix, $line );

				// Check if cache exists.
				if ( false === ( $json = get_transient( $st_transient_id ) ) || empty( $json ) ) {

					// Log new fetch for cache if WP debugging is enabled
					if ( WP_DEBUG ) {
						error_log( "Download data for symbols {$symbols} and cache for {$defaults['cache_timeout']} seconds" );
					}

					// If does not exist, get new cache.
					// Clean and prepare symbols for query.
					$exc_symbols = preg_replace( '/\s+/', '', $symbols );
					// Adapt ^DIJ to .DJI symbol format.
					$exc_symbols = preg_replace( '/\^/', '.', $exc_symbols );
					// Replace amp with code.
					$exc_symbols = str_replace( '&', '%26', $exc_symbols );
					// Adapt currency symbols EURGBP=X to CURRENCY:EURGBP symbol format.
					$exc_symbols = preg_replace( '/([a-zA-Z]*)\=X/i', 'CURRENCY:$1', $exc_symbols );
					// Compose URL.
					$exc_url = "https://finance.google.com/finance/info?client=ig&q={$exc_symbols}";

					// Set timeout.
					$wparg = array(
						'timeout' => $defaults['timeout'], // Two seconds only.
					);
					// Get stock from Google.
					$response = wp_remote_get( $exc_url, $wparg );
					if ( is_wp_error( $response ) ) {
						return $defaults['error_message'] . '<!-- ERROR: ' . $response->get_error_message() . ' -->';
					} else {
						// Get content from response.
						$data = wp_remote_retrieve_body( $response );
						// Convert a string with ISO-8859-1 characters encoded with UTF-8 to single-byte ISO-8859-1.
						$data = utf8_decode( $data );
						// Remove newlines from content.
						$data = str_replace( "\n", '', $data );
						// Remove // from content.
						$data = trim( str_replace( '/', '', $data ) );

						// Decode data to JSON.
						$json = json_decode( $data );
						unset( $data );

						set_transient( $st_transient_id, $json, (int) $defaults['cache_timeout'] );
					}

					// Free some memory: destroy all vars that we temporary used here.
					unset( $exc_symbols, $exc_url, $reponse );
				}

				// Prepare ticker.
				// @deprecated ID not required since v0.2.0
				$id = 'stock_ticker_' . substr( md5( mt_rand() ), 0, 4 );
				if ( ! empty( $static ) && 1 == $static ) { $class .= ' static'; }
				$out = sprintf( '<ul id="%s" class="stock_ticker %s">', $id, $class );

				// Process quotes.
				if ( ! empty( $json ) && ! is_null( $json[0]->id ) ) {
					// Start ticker string.
					$q = '';

					// Parse results and extract data to display.
					foreach ( $json as $quote ) {
						// Assign object elements to vars.
						$q_change  = $quote->c;
						$q_price   = $quote->l;
						$q_name    = $quote->t; // No nicename in Google Finance so use Symbol instead.
						$q_changep = $quote->cp;
						$q_symbol  = $quote->t;
						$q_ltrade  = $quote->lt;
						$q_exch    = $quote->e;

						// Define class based on change.
						if ( $q_change < 0 ) {
							$chclass = 'minus';
						} elseif ( $q_change > 0 ) {
							$chclass = 'plus';
						} else {
							$chclass = 'zero';
							$q_change = '0.00';
						}

						// Get custom company name if exists.
						if ( ! empty( $legend[ $q_exch . ':' . $q_symbol ] ) ) {
							// First in format EXCHANGE:SYMBOL.
							$q_name = $legend[ $q_exch . ':' . $q_symbol ];
						} elseif ( ! empty( $legend[ $q_symbol ] ) ) {
							// Then in format SYMBOL.
							$q_name = $legend[ $q_symbol ];
						}

						// What to show: Symbol or Company Name?
						if ( 'name' == $show ) {
							$company_show = $q_name;
						} else {
							$company_show = $q_symbol;
						}
						// Open stock quote item.
						$q .= "<li class=\"{$chclass}\">";

						// Do not print change, volume and change% for currencies.
						if ( 'CURRENCY' == $q_exch ) {
							$company_show = ( $q_symbol == $q_name ) ? $q_name . '=X' : $q_name;
							$url_query = $q_symbol;
							$quote_title = $q_name;
						} else {
							$url_query = $q_exch . ':' . $q_symbol;
							$quote_title = $q_name . ' (' . $q_exch . ' Last trade ' . $q_ltrade . ')';
						}

						// Value template.
						$template = $defaults['template'];
						$template = str_replace( '%company%', $company_show, $template );
						$template = str_replace( '%symbol%', $q_symbol, $template );
						$template = str_replace( '%exch_symbol%', $url_query, $template );
						$template = str_replace( '%price%', $q_price, $template );
						$template = str_replace( '%change%', $q_change, $template );
						$template = str_replace( '%changep%', "{$q_changep}%", $template );

						// Quote w/ or w/o link.
						if ( empty( $nolink ) ) {
							$q .= '<a rel="nofollow" href="https://www.google.com/finance?q=' . $url_query
							   . '" class="sqitem" target="_blank" title="' . $quote_title
							   . '">' . $template . '</a>';
						} else {
							$q .= '<span class="sqitem" title="' . $quote_title . '">' . $template . '</span>';
						}

						// Close stock quote item.
						$q .= '</li>';

					}
				}

				// No results were returned.
				if ( empty( $q ) ) {
					$q = "<li class=\"error\">{$defaults['error_message']}</li>";
				}

				$out .= $q;

				$out .= '</ul>';

				unset( $q, $id, $css, $defaults, $legend );

				// Print ticker content.
				return $out;

			}
		} // END public static function stock_ticker()

		/**
		 * Shortcode processor for Stock Ticker
		 * @param  array $atts    Array of shortcode parameters.
		 * @return string         Generated HTML output for block.
		 */
		public static function shortcode( $atts ) {

			$st_defaults = self::$defaults;
			$atts = shortcode_atts( array(
				'symbols'   => $st_defaults['symbols'],
				'show'      => $st_defaults['show'],
				'static'    => 0,
				'nolink'    => 0,
				'prefill'   => 0,
				'duplicate' => 0,
				'speed'     => isset( $st_defaults['speed'] ) ? $st_defaults['speed'] : 50,
				'class'     => '',
			), $atts );

			// If we have defined symbols, enqueue script and print stock holder
			if ( ! empty( $atts['symbols'] ) ) {
				// Strip tags as we allow only real symbols
				$atts['symbols'] = strip_tags( $atts['symbols'] );

				// Enqueue script parser on demand
				if ( empty( $st_defaults['globalassets'] ) ) {
					wp_enqueue_script( 'stock-ticker' );
					if ( ! empty( $st_defaults['refresh'] ) ) {
						wp_enqueue_script( 'stock-ticker-refresh' );
					}
				}

				// startEmpty based on prefill option
				$empty = empty( $atts['prefill'] ) ? 'true' : 'false';
				// duplicate
				$duplicate = empty( $atts['duplicate'] ) ? 'false' : 'true';

				// Return stock holder
				return sprintf(
					'<div
					 class="stock-ticker-wrapper %5$s"
					 data-stockticker_symbols="%1$s"
					 data-stockticker_show="%2$s"
					 data-stockticker_static="%3$s"
					 data-stockticker_nolink="%4$s"
					 data-stockticker_class="%5$s"
					 data-stockticker_speed="%6$s"
					 data-stockticker_empty="%7$s"
					 data-stockticker_duplicate="%8$s"
					><ul class="stock_ticker"><li class="init"><span class="sqitem">Loading stock data...</span></li></ul></div>',
					$atts['symbols'],  // 1
					$atts['show'],     // 2
					$atts['static'],   // 3
					$atts['nolink'],   // 4
					$atts['class'],    // 5
					$atts['speed'],    // 6
					$empty,            // 7
					$duplicate         // 8
				);
			}
			return false;

		} // END public static function shortcode()

		/**
		 * Add link to official plugin pages
		 * @param array $links  Array of existing plugin row links.
		 * @param string $file  Path of current plugin file.
		 * @return array        Array of updated plugin row links
		 */
		public static function add_plugin_meta_links( $links, $file ) {
			if ( 'stock-ticker/stock-ticker.php' === $file ) {
				return array_merge(
					$links,
					array(
						sprintf(
							'<a href="https://wordpress.org/support/plugin/stock-ticker" target="_blank">%s</a>',
							__( 'Support' )
						),
						sprintf(
							'<a href="https://urosevic.net/wordpress/donate/?donate_for=stock-ticker" target="_blank">%s</a>',
							__( 'Donate' )
						),
					)
				);
			}
			return $links;
		} // END public static function add_plugin_meta_links()

		/**
		 * Generate Settings link on Plugins page listing
		 * @param  array $links Array of existing plugin row links.
		 * @return array        Updated array of plugin row links with link to Settings page
		 */
		public static function plugin_settings_link( $links ) {
			$settings_link = '<a href="options-general.php?page=wpau_stock_ticker">Settings</a>';
			array_unshift( $links, $settings_link );
			return $links;
		} // END public static function plugin_settings_link()

	} // END class Wpau_Stock_Ticker

} // END if(!class_exists('Wpau_Stock_Ticker'))

if ( class_exists( 'Wpau_Stock_Ticker' ) ) {

	// Instantiate the plugin class.
	$wpau_stock_ticker = new Wpau_Stock_Ticker();

} // END class_exists('Wpau_Stock_Ticker')
