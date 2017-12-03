<?php
/**
Plugin Name: Stock Ticker
Plugin URI: https://urosevic.net/wordpress/plugins/stock-ticker/
Description: Easy add customizable moving or static ticker tapes with stock information for custom stock symbols.
Version: 0.2.99-alpha9
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
along with this program.  If not, see <https://www.gnu.org/licenses/>.
 */
/*
 * @TODO:
 * * add admin notification for AlphaVantage.co API Key and default symbols
 */

// Exit if accessed directly
if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

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

		const DB_VER = 3;
		const VER = '0.2.99-alpha9';

		public $plugin_name   = 'Stock Ticker';
		public $plugin_slug   = 'stock-ticker';
		public $plugin_option = 'stockticker_defaults';
		public $plugin_url;

		public static $exchanges = array(
			'ASX'    => 'Australian Securities Exchange',
			'BOM'    => 'Bombay Stock Exchange',
			'BIT'    => 'Borsa Italiana Milan Stock Exchange',
			'TSE'    => 'Canadian/Toronto Securities Exchange',
			'FRA'    => 'Deutsche Boerse Frankfurt Stock Exchange',
			'ETR'    => 'Deutsche Boerse Frankfurt Stock Exchange',
			'AMS'    => 'Euronext Amsterdam',
			'EBR'    => 'Euronext Brussels',
			'ELI'    => 'Euronext Lisbon',
			'EPA'    => 'Euronext Paris',
			'LON'    => 'London Stock Exchange',
			'MCX'    => 'Moscow Exchange',
			'NASDAQ' => 'NASDAQ Exchange',
			'CPH'    => 'NASDAQ OMX Copenhagen',
			'HEL'    => 'NASDAQ OMX Helsinki',
			'ICE'    => 'NASDAQ OMX Iceland',
			'STO'    => 'NASDAQ OMX Stockholm',
			'NSE'    => 'National Stock Exchange of India',
			'NYSE'   => 'New York Stock Exchange',
			'SGX'    => 'Singapore Exchange',
			'SHA'    => 'Shanghai Stock Exchange',
			'SHE'    => 'Shenzhen Stock Exchange',
			'TPE'    => 'Taiwan Stock Exchange',
			'TYO'    => 'Tokyo Stock Exchange',
		);

		/**
		 * Construct the plugin object
		 */
		public function __construct() {

			$this->plugin_url = plugin_dir_url( __FILE__ );
			$this->plugin_file = plugin_basename( __FILE__ );
			load_plugin_textdomain( $this->plugin_slug, false, dirname( plugin_basename( __FILE__ ) ) . '/languages' );

			// Installation and uninstallation hooks.
			register_activation_hook( __FILE__, array( $this, 'activate' ) );
			register_deactivation_hook( __FILE__, array( $this, 'deactivate' ) );

			// Maybe update trigger.
			add_action( 'plugins_loaded', array( $this, 'maybe_update' ) );

			// Cleanup transients
			if ( ! empty( $_GET['stockticker_purge_cache'] ) ) {
				self::restart_av_fetching();
			}

			// Initialize default settings
			$this->defaults = self::defaults();

			// Register AJAX ticker loader
			add_action( 'wp_ajax_stockticker_load', array( $this, 'ajax_stockticker_load' ) );
			add_action( 'wp_ajax_nopriv_stockticker_load', array( $this, 'ajax_stockticker_load' ) );
			// Register AJAX stock updater
			add_action( 'wp_ajax_stockticker_update_quotes', array( $this, 'ajax_stockticker_update_quotes' ) );
			add_action( 'wp_ajax_nopriv_stockticker_update_quotes', array( $this, 'ajax_stockticker_update_quotes' ) );

			if ( is_admin() ) {
				// Initialize Plugin Settings Magic
				add_action( 'init', array( $this, 'admin_init' ) );
			} else {
				// Enqueue frontend scripts.
				add_action( 'wp_enqueue_scripts', array( $this, 'enqueue_scripts' ) );
			}

			// Initialize Widget.
			require_once( 'inc/widget.php' );

			// Register stock_ticker shortcode.
			add_shortcode( 'stock_ticker', array( $this, 'shortcode' ) );

		} // END public function __construct()

		/**
		 * Activate the plugin
		 */
		function activate() {
			global $wpau_stockticker;
			$wpau_stockticker->init_options();
			$wpau_stockticker->maybe_update();
		} // END function activate()

		/**
		 * Deactivate the plugin
		 */
		function deactivate() {
			// Do nothing.
		} // END function deactivate()

		/**
		 * Return initial options
		 * @return array Global defaults for current plugin version
		 */
		function init_options() {

			$init = array(
				'all_symbols'     => 'AAPL,MSFT,INTC',
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
				'avapikey'        => '',
				'loading_message' => 'Loading stock data...',
				'number_format'   => 'dc',
				'decimals'        => 2,
			);

			add_option( 'stockticker_version', self::VER, '', 'no' );
			add_option( 'stockticker_db_ver', self::DB_VER, '', 'no' );
			add_option( $this->plugin_option, $init, '', 'no' );

			return $init;

		} // END function init_options() {

		/**
		 * Check do we need to migrate options
		 */
		function maybe_update() {
			// Bail if this plugin data doesn't need updating
			if ( get_option( 'stockticker_db_ver' ) >= self::DB_VER ) {
				return;
			}
			require_once( dirname( __FILE__ ) . '/update.php' );
			au_stockticker_update();
		} // END function maybe_update()

		/**
		 * Initialize Settings link for Plugins page and create Settings page
		 *
		 */
		function admin_init() {

			// Add plugin Settings link.
			add_filter( 'plugin_action_links_' . $this->plugin_file, array( $this, 'plugin_settings_link' ) );

			// Update links in plugin row on Plugins page.
			add_filter( 'plugin_row_meta', array( $this, 'add_plugin_meta_links' ), 10, 2 );

			// Load colour picker scripts on plugin settings page and on widgets/customizer.
			add_action( 'admin_enqueue_scripts', array( $this, 'admin_scripts' ) );

			require_once( 'inc/settings.php' );

			global $wpau_stockticker_settings;
			if ( empty( $wpau_stockticker_settings ) ) {
				$wpau_stockticker_settings = new Wpau_Stock_Ticker_Settings();
			}

		} // END function admin_init_settings()

		/**
		 * Add link to official plugin pages
		 * @param array $links  Array of existing plugin row links.
		 * @param string $file  Path of current plugin file.
		 * @return array        Array of updated plugin row links
		 */
		function add_plugin_meta_links( $links, $file ) {
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
		} // END function add_plugin_meta_links()

		/**
		 * Generate Settings link on Plugins page listing
		 * @param  array $links Array of existing plugin row links.
		 * @return array        Updated array of plugin row links with link to Settings page
		 */
		function plugin_settings_link( $links ) {
			$settings_title = __( 'Settings' );
			$settings_link = "<a href=\"options-general.php?page={$this->plugin_slug}\">{$settings_title}</a>";
			array_unshift( $links, $settings_link );
			return $links;
		} // END function plugin_settings_link()

		/**
		 * Enqueue the colour picker and admin style
		 */
		function admin_scripts( $hook ) {
			if ( 'settings_page_' . $this->plugin_slug == $hook ) {
				wp_enqueue_style( 'wp-color-picker' );
				wp_enqueue_script( 'wp-color-picker' );
				wp_enqueue_style(
					$this->plugin_slug . '-admin', // 'stock-ticker',
					plugins_url( 'assets/css/admin.css', __FILE__ ),
					array(),
					self::VER
				);
			}
		} // END function admin_scripts()

		/**
		 * Enqueue frontend assets
		 */
		function enqueue_scripts() {
			$defaults = $this->defaults;
			$upload_dir = wp_upload_dir();

			wp_enqueue_script(
				'jquery-webticker',
				$this->plugin_url . ( WP_DEBUG ? 'assets/js/jquery.webticker.js' : 'assets/js/jquery.webticker.min.js' ),
				array( 'jquery' ),
				'2.2.0.1',
				true
			);
			wp_enqueue_style(
				'stock-ticker',
				$this->plugin_url . 'assets/css/stock-ticker.css',
				array(),
				self::VER
			);
			wp_enqueue_style(
				'stock-ticker-custom',
				set_url_scheme( $upload_dir['baseurl'] ) . '/stock-ticker-custom.css',
				array(),
				self::VER
			);

			wp_register_script(
				'stock-ticker',
				$this->plugin_url . ( WP_DEBUG ? 'assets/js/jquery.stockticker.js' : 'assets/js/jquery.stockticker.min.js' ),
				array( 'jquery', 'jquery-webticker' ),
				self::VER,
				true
			);
			wp_localize_script(
				'stock-ticker',
				'stockTickerJs',
				array( 'ajax_url' => admin_url( 'admin-ajax.php' ) )
			);
			// Enqueue script parser
			if ( isset( $defaults['globalassets'] ) ) {
				wp_enqueue_script( 'stock-ticker' );
			}

			// Register refresh script if option is enabled
			if ( ! empty( $defaults['refresh'] ) ) {
				wp_register_script(
					'stock-ticker-refresh',
					set_url_scheme( $upload_dir['baseurl'] ) . '/stock-ticker-refresh.js',
					array( 'jquery', 'jquery-webticker', 'stock-ticker' ),
					self::VER,
					true
				);
				wp_enqueue_script( 'stock-ticker-refresh' );
			}

		} // END function enqueue_scripts()

		/**
		 * Get default options from DB
		 * @return array Latest global defaults
		 */
		public function defaults() {
			$defaults = get_option( $this->plugin_option );
			if ( empty( $defaults ) ) {
				$defaults = $this->init_options();
			}
			return $defaults;
		} // END public function defaults()

		/**
		 * Delete control options to force re-fetching from first symbol
		 */
		public static function restart_av_fetching() {
			update_option( 'stockticker_av_latest', '' );
			$expired_timestamp = time() - ( 10 * YEAR_IN_SECONDS );
			update_option( 'stockticker_av_latest_timestamp', $expired_timestamp );
			update_option( 'stockticker_av_progress', false );
		} // END public static function restart_av_fetching() {

		function ajax_stockticker_load() {
			// @TODO Provide error message if any of params missing + add nonce check
			if ( ! empty( $_POST['symbols'] ) ) {
				// Sanitize data
				$symbols       = strip_tags( $_POST['symbols'] );
				$show          = strip_tags( $_POST['show'] );
				$number_format = (int) $_POST['number_format'];
				$decimals      = (int) $_POST['decimals'];
				$static        = (int) $_POST['static'];
				$empty         = (int) $_POST['empty'];
				$duplicate     = (int) $_POST['duplicate'];
				$class         = strip_tags( $_POST['class'] );
				$speed         = (int) $_POST['speed'];

				// Treat as error if no stock ticker composed but 'Unfortunately' message displayed
				$message = self::stock_ticker( $symbols, $show, $number_format, $decimals, $static, $empty, $duplicate, $class );
				if ( strpos( $message, 'error' ) !== false ) {
					$message = strip_tags( $message );
					$result['status']  = 'error';
					$result['message'] = $message;
				} else {
					$result['status']  = 'success';
					$result['speed']   = $speed;
					$result['message'] = $message;
				}
			} else {
				$result['status']  = 'error';
				$result['message'] = 'Error ocurred: No symbols provided';
			}
			$result = json_encode( $result );
			echo $result;
			wp_die();
		} // END function ajax_stockticker_load() {

		/**
		 * AJAX to update AlphaVantage.co quotes
		 */
		function ajax_stockticker_update_quotes() {
			$response = $this->get_alphavantage_quotes();
			echo $response;
			wp_die();
		} // END function ajax_stockticker_update_quotes()

		/**
		 * Generate and output stock ticker block
		 * @param  string   $symbols       Comma separated array of symbols.
		 * @param  string   $show          What to show (name or symbol).
		 * @param  bool     $static        Request for static (non-animated) block.
		 * @param  bool     $empty         Start ticker empty or prefilled with symbols.
		 * @param  bool     $duplicate     If there is less items than visible on the ticker make it continuous
		 * @param  string   $class         Custom class for styling Stock Ticker block.
		 * @param  integer  $decimals      Number of decimal places.
		 * @param  string   $number_format Which number format to use (dc, sc, cd, sd).
		 * @return string          Composed HTML for block.
		 */
		public function stock_ticker( $symbols, $show, $number_format = null, $decimals = null, $static, $empty = true, $duplicate = false, $class = '' ) {

			if ( empty( $symbols ) ) {
				return;
			}

			// Get legend for company names.
			$defaults = $this->defaults;

			// Prepare number format
			if ( ! empty( $number_format ) && in_array( $number_format, array( 'dc', 'sd', 'sc', 'cd' ) ) ) {
				$defaults['number_format'] = $number_format;
			} else if ( ! isset( $defaults['number_format'] ) ) {
				$defaults['number_format'] = 'cd';
			}
			switch ( $defaults['number_format'] ) {
				case 'dc': // 0.000,00
					$thousands_sep = '.';
					$dec_point     = ',';
					break;
				case 'sd': // 0 000.00
					$thousands_sep = ' ';
					$dec_point     = '.';
					break;
				case 'sc': // 0 000,00
					$thousands_sep = ' ';
					$dec_point     = ',';
					break;
				default: // 0,000.00
					$thousands_sep = ',';
					$dec_point     = '.';
			}

			// Prepare number of decimals
			if ( null !== $decimals ) {
				// From shortcode or widget
				$decimals = (int) $decimals;
			} else {
				// From settings
				if ( ! isset( $defaults['decimals'] ) ) {
					$defaults['decimals'] = 2;
				}
				$decimals = (int) $defaults['decimals'];
			}

			// Parse legend
			$matrix = explode( "\n", $defaults['legend'] );
			$msize = count( $matrix );
			for ( $m = 0; $m < $msize; ++$m ) {
				$line = explode( ';', $matrix[ $m ] );
				if ( ! empty( $line[0] ) && ! empty( $line[1] ) ) {
					$legend[ strtoupper( trim( $line[0] ) ) ] = trim( $line[1] );
				}
			}
			unset( $m, $msize, $matrix, $line );

			// Prepare ticker.
			if ( ! empty( $static ) && 1 == $static ) { $class .= ' static'; }

			// Prepare out vars
			$out_start = sprintf( '<ul class="stock_ticker %s">', $class );
			$out_end = '</ul>';
			$out_error_msg = "<li class=\"error\">{$defaults['error_message']}</li>";

			// Get stock data from database
			$stock_data = self::get_stock_from_db( $symbols );
			if ( empty( $stock_data ) ) {
				return "{$out_start}{$out_error_msg}{$out_end}";
			}

			// Start ticker string.
			$q = '';

			// Parse results and extract data to display.
			$symbols_arr = explode( ',', $symbols );
			foreach ( $symbols_arr as $symbol ) {

				if ( empty( $stock_data[ $symbol ] ) ) {
					continue;
				}

				// Assign object elements to vars.
				$q_symbol  = $symbol;
				$q_name    = $stock_data[ $symbol ]['symbol']; // ['t']; // No nicename on AlphaVantage.co so use ticker instead.
				$q_change  = $stock_data[ $symbol ]['change']; // ['c'];
				$q_price   = $stock_data[ $symbol ]['last_open']; // ['l'];
				$q_changep = $stock_data[ $symbol ]['changep']; // ['cp'];
				$q_volume  = $stock_data[ $symbol ]['last_volume'];
				$q_tz      = $stock_data[ $symbol ]['tz'];
				$q_ltrade  = $stock_data[ $symbol ]['last_refreshed']; // ['lt'];
				$q_ltrade  = str_replace( ' 00:00:00', '', $q_ltrade ); // Strip zero time from last trade date string
				$q_ltrade  = "{$q_ltrade} {$q_tz}";
				// Extract Exchange from Symbol
				$q_exch = '';
				if ( strpos( $symbol, ':' ) !== false ) {
					list( $q_exch, $q_symbol ) = explode( ':', $symbol );
				}

				// Define class based on change.
				$prefix = '';
				if ( $q_change < 0 ) {
					$chclass = 'minus';
				} elseif ( $q_change > 0 ) {
					$chclass = 'plus';
					$prefix = '+';
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

				// Format numbers.
				$q_price   = number_format( $q_price, $decimals, $dec_point, $thousands_sep );
				$q_change  = $prefix . number_format( $q_change, $decimals, $dec_point, $thousands_sep );
				$q_changep = $prefix . number_format( $q_changep, $decimals, $dec_point, $thousands_sep );

				$url_query = $q_symbol;
				if ( ! empty( $q_exch ) ) {
					$quote_title = $q_name . ' (' . self::$exchanges[ $q_exch ] . ', Volume ' . $q_volume . ', Last trade ' . $q_ltrade . ')';
				} else {
					$quote_title = $q_name . ' (Last trade ' . $q_ltrade . ')';
				}

				// Value template.
				$template = $defaults['template'];
				$template = str_replace( '%company%', $company_show, $template );
				$template = str_replace( '%symbol%', $q_symbol, $template );
				$template = str_replace( '%exch_symbol%', $url_query, $template );
				$template = str_replace( '%price%', $q_price, $template );
				$template = str_replace( '%change%', $q_change, $template );
				$template = str_replace( '%changep%', "{$q_changep}%", $template );
				$template = str_replace( '%volume%', $q_volume, $template );

				$q .= '<span class="sqitem" title="' . $quote_title . '">' . $template . '</span>';

				// Close stock quote item.
				$q .= '</li>';

			} // END foreach ( $symbols_arr as $symbol ) {

			// No results were returned?
			if ( empty( $q ) ) {
				return "{$out_start}{$out_error_msg}{$out_end}";
			}

			// Print ticker content if we have it.
			return "{$out_start}{$q}{$out_end}";

		} // END public function stock_ticker()

		/**
		 * Shortcode processor for Stock Ticker
		 * @param  array $atts    Array of shortcode parameters.
		 * @return string         Generated HTML output for block.
		 */
		public function shortcode( $atts ) {
			$defaults = $this->defaults;

			$atts = shortcode_atts( array(
				'symbols'         => $defaults['symbols'],
				'show'            => $defaults['show'],
				'number_format'   => isset( $defaults['number_format'] ) ? $defaults['number_format'] : 'dc',
				'decimals'        => isset( $defaults['decimals'] ) ? $defaults['decimals'] : 2,
				'static'          => 0,
				'nolink'          => 0,
				'prefill'         => 0,
				'duplicate'       => 0,
				'speed'           => isset( $defaults['speed'] ) ? $defaults['speed'] : 50,
				'class'           => '',
				'loading_message' => isset( $defaults['loading_message'] ) ? $defaults['loading_message'] : __( 'Loading stock data...', 'wpaust' ),
			), $atts );

			// If we have defined symbols, enqueue script and print stock holder
			if ( ! empty( $atts['symbols'] ) ) {
				// Strip tags as we allow only real symbols
				$atts['symbols'] = strip_tags( $atts['symbols'] );

				// Enqueue script parser on demand
				if ( empty( $defaults['globalassets'] ) ) {
					wp_enqueue_script( 'stock-ticker' );
					if ( ! empty( $defaults['refresh'] ) ) {
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
					 data-stockticker_number_format="%4$s"
					 data-stockticker_decimals="%10$s"
					 data-stockticker_static="%3$s"
					 data-stockticker_class="%5$s"
					 data-stockticker_speed="%6$s"
					 data-stockticker_empty="%7$s"
					 data-stockticker_duplicate="%8$s"
					><ul class="stock_ticker"><li class="init"><span class="sqitem">%9$s</span></li></ul></div>',
					$atts['symbols'],         // 1
					$atts['show'],            // 2
					$atts['static'],          // 3
					$atts['number_format'],   // 4
					$atts['class'],           // 5
					$atts['speed'],           // 6
					$empty,                   // 7
					$duplicate,               // 8
					$atts['loading_message'], // 9
					$atts['decimals']         // 10
				);
			}
			return false;

		} // END public function shortcode()

		// Thanks to https://coderwall.com/p/zepnaw/sanitizing-queries-with-in-clauses-with-wpdb-on-wordpress
		private function get_stock_from_db( $symbols = '' ) {
			// If no symbols we have to fetch from DB, then exit
			if ( empty( $symbols ) ) {
				return;
			}

			global $wpdb;
			// Explode symbols to array
			$symbols_arr = explode( ',', $symbols );
			// Count how many entries will we select?
			$how_many = count( $symbols_arr );
			// prepare the right amount of placeholders for each symbol
			$placeholders = array_fill( 0, $how_many, '%s' );
			// glue together all the placeholders...
			$format = implode( ',', $placeholders );
			// put all in the query and prepare
			$stock_sql = $wpdb->prepare(
				"
				SELECT `symbol`,`tz`,`last_refreshed`,`last_open`,`last_high`,`last_low`,`last_close`,`last_volume`,`change`,`changep`,`range`
				FROM {$wpdb->prefix}stock_ticker_data
				WHERE symbol IN ($format)
				",
				$symbols_arr
			);

			// retrieve the results from database
			$stock_data_a = $wpdb->get_results( $stock_sql, ARRAY_A );

			// If we don't have anything retrieved, just exit
			if ( empty( $stock_data_a ) ) {
				return;
			}

			// Convert DB result to associated array
			$stock_data = array();
			foreach ( $stock_data_a as $stock_data_item ) {
				$stock_data[ $stock_data_item['symbol'] ] = $stock_data_item;
			}

			// Return re-composed assiciated array
			return $stock_data;
		} // END private function get_stock_from_db( $symbols ) {

		/**
		 * Download stock quotes from AlphaVantage.co and store them all to single transient
		 */
		function get_alphavantage_quotes() {

			// Check is currently fetch in progress
			$progress = get_option( 'stockticker_av_progress', false );

			if ( false != $progress ) {
				return;
			}

			// Set fetch progress as active
			self::lock_fetch();

			// Get defaults (for API key)
			$defaults = $this->defaults;
			// Get symbols we should to fetch from AlphaVantage
			$symbols = $defaults['all_symbols'];

			// If we don't have defined global symbols, exit
			if ( empty( $symbols ) ) {
				return 'We do not have any symbol to fetch data for.';
			}

			// Make array of global symbols
			$symbols_arr = explode( ',', $symbols );

			// Default symbol to fetch first (first form array)
			$current_symbol_index = 0;
			$symbol_to_fetch = $symbols_arr[ $current_symbol_index ];

			// Get last fetched symbol
			$last_fetched = get_option( 'stockticker_av_last' );

			// Find which symbol we should fetch
			if ( ! empty( $last_fetched ) ) {
				$last_symbol_index = array_search( $last_fetched, $symbols_arr );
				$current_symbol_index = $last_symbol_index + 1;
				// If we have less than next symbol, then rewind to beginning
				if ( count( $symbols_arr ) <= $current_symbol_index ) {
					$current_symbol_index = 0;
				}
				$symbol_to_fetch = $symbols_arr[ $current_symbol_index ];
			}

			// If no symbol to fetch, exit
			if ( empty( $symbol_to_fetch ) ) {
				// After finished update, set last fetched symbol
				update_option( 'stockticker_av_last', $symbol_to_fetch );
				// and release processing for next run
				self::unlock_fetch();
				return 'No symbol to fetch!';
			}

			// If current_symbol_index is 0 and cache timeout not expired, do not attempt to fetch again
			// but wait to timeout expire for next loop (UTC)
			if ( 0 == $current_symbol_index ) {
				$current_timestamp = time();
				$last_fetched_timestamp = get_option( 'stockticker_av_last_timestamp', $current_timestamp );
				$target_timestamp = $current_timestamp - (int) $defaults['cache_timeout'];
				if ( $last_fetched_timestamp > $target_timestamp ) {
					// If timestamp not expired, do not fetch but exit
					self::unlock_fetch();
					return 'Cache timeout has not expired, no need to fetch new loop at the moment.';
				} else {
					// If timestamp expired, set new value and proceed
					update_option( 'stockticker_av_last_timestamp', $current_timestamp );
				}
			}

			// Now call AlphaVantage fetcher for current symbol
			$stock_data = $this->fetch_alphavantage_feed( $symbol_to_fetch );

			// If we have not got array with stock data, exit w/o updating DB
			if ( ! is_array( $stock_data ) ) {
				self::log( "No proper data got from Alpha Vantage, but we got this: {$stock_data}" );
				// Release processing for next run
				self::unlock_fetch();
				return $stock_data;
			}

			// With success stock data in array, save data to database
			global $wpdb;
			$ret = $wpdb->replace(
				$wpdb->prefix . 'stock_ticker_data',
				array(
					'symbol'         => $stock_data['t'],
					'raw'            => $stock_data['raw'],
					'last_refreshed' => $stock_data['lt'],
					'tz'             => $stock_data['ltz'],
					'last_open'      => $stock_data['o'],
					'last_high'      => $stock_data['h'],
					'last_low'       => $stock_data['low'],
					'last_close'     => $stock_data['l'],
					'last_volume'    => $stock_data['v'],
					'change'         => $stock_data['c'],
					'changep'        => $stock_data['cp'],
					'range'          => $stock_data['r'],
				),
				array(
					'%s', // symbol
					'%s', // raw
					'%s', // last_refreshed
					'%s', // tz
					'%f', // last_open
					'%f', // last_high
					'%f', // last_low
					'%f', // last_close
					'%d', // last_volume
					'%f', // last_change
					'%f', // last_changep
					'%s', // range
				)
			);

			// Is successfully updated data in DB?
			if ( (int) $ret > 0 ) {
				self::log( "Stock data for {$symbol_to_fetch} has been updated in database." );
				// After success update in database, set last fetched symbol
				update_option( 'stockticker_av_last', $symbol_to_fetch );
			} else {
				self::log( "Error: Failed to save stock data for {$symbol_to_fetch} to database!" );
			}

			// Release processing for next run
			self::unlock_fetch();

			return 'OK';
		} // END function get_alphavantage_quotes( $symbols )

		function fetch_alphavantage_feed( $symbol ) {

			self::log( "Fetching data for symbol {$symbol}..." );

			// Get defaults (for API key)
			$defaults = $this->defaults;

			// Exit if we don't have API Key
			if ( empty( $defaults['avapikey'] ) ) {
				self::log( 'Stock Ticker can not fetch stock data from AlphaVantage.co because you don`t have set API Key!' );
				return false;
			}

			// Define AplhaVantage API URL
			// $feed_url = 'https://www.alphavantage.co/query?function=TIME_SERIES_INTRADAY&interval=5min&apikey=' . $defaults['avapikey'] . '&symbol=';
			$feed_url = 'https://www.alphavantage.co/query?function=TIME_SERIES_DAILY&outputsize=compact&apikey=' . $defaults['avapikey'] . '&symbol=';
			$feed_url .= $symbol;

			$wparg = array(
				'timeout' => intval( $defaults['timeout'] ),
			);

			// self::log( 'Fetching data from AV: ' . $feed_url );
			$response = wp_remote_get( $feed_url, $wparg );

			// Initialize empty $json variable
			$data_arr = '';

			// If we have WP error log it and return none
			if ( is_wp_error( $response ) ) {
				self::log( 'Stock Ticker got error fetching feed from AlphaVantage.co: ' . $response->get_error_message() );
				return 'Stock Ticker got error fetching feed from AlphaVantage.co: ' . $response->get_error_message();
			} else {
				// Get response from AV and parse it - look for error
				$json = wp_remote_retrieve_body( $response );
				$response_arr = json_decode( $json, true );
				// If we got some error from AV, log to self::log and return none
				if ( ! empty( $response_arr['Error Message'] ) ) {
					self::log( 'Stock Ticker connected to AlphaVantage but got error: ' . $response_arr['Error Message'] );
					return 'Stock Ticker connected to AlphaVantage but got error: ' . $response_arr['Error Message'];
				} else {
					// Crunch data from AlphaVantage for symbol and prepare compact array
					self::log( "We got data from AlphaVantage for $symbol, so now let we crunch them and save to database..." );

					// Get basics
					// $ticker_symbol      = $response_arr['Meta Data']['2. Symbol']; // We don't use this at the moment, but requested symbol
					$last_trade_refresh = $response_arr['Meta Data']['3. Last Refreshed'];
					$last_trade_tz      = $response_arr['Meta Data']['5. Time Zone']; // TIME_SERIES_DAILY
					// $last_trade_tz      = $response_arr['Meta Data']['6. Time Zone']; // TIME_SERIES_INTRADAY

					// Get prices
					$i = 0;
					/*
					$last_trade = array();
					$last_trade_date = '';
					$prev_trade = array();
					$prev_trade_date = '';
					foreach ( $response_arr['Time Series (5min)'] as $datetime => $val ) { // TIME_SERIES_INTRADAY
						// If we don't have last trade already set, do it now
						if ( empty( $last_trade ) ) {
							$last_trade = $val;
							$last_trade_datetime = $datetime;
							$last_trade_date = date( 'Y-m-d', strtotime( $last_trade_datetime ) );
						} else if ( empty( $prev_trade ) ) {
							// Get previous trade day
							$prev_trade_date = date( 'Y-m-d', strtotime( $datetime ) );
							// If this date differ from last, consider as previous day
							if ( $prev_trade_date != $last_trade_date ) {
								$prev_trade = $val;
								$prev_trade_datetime = $datetime;
							}
						}
					}

					$last_open   = $last_trade['1. open'];
					$last_high   = $last_trade['2. high'];
					$last_low    = $last_trade['3. low'];
					$last_close  = $last_trade['4. close'];
					$last_volume = (int) $last_trade['5. volume'];

					$prev_open   = $prev_trade['1. open'];
					$prev_high   = $prev_trade['2. high'];
					$prev_low    = $prev_trade['3. low'];
					$prev_close  = $prev_trade['4. close'];
					$prev_volume = (int) $prev_trade['5. volume'];
					*/

					/**/
					foreach ( $response_arr['Time Series (Daily)'] as $key => $val ) { // TIME_SERIES_DAILY
					// foreach ( $response_arr['Time Series (5min)'] as $key => $val ) { // TIME_SERIES_INTRADAY
						switch ( $i ) {
							case 0:
								$last_trade_date = $key;
								$last_trade = $val;
								break;
							case 1:
								$prev_trade_date = $key;
								$prev_trade = $val;
								break;
							case 2: // Workaround for inconsistent data
								$prev_trade_2_date = $key;
								$prev_trade_2 = $val;
								break;
							case 3: // Workaround for weekend data (currencies)
								$prev_trade_3_date = $key;
								$prev_trade_3 = $val;
								break;
							default:
								continue;
						}
						++$i;
					}

					$last_open   = $last_trade['1. open'];
					$last_high   = $last_trade['2. high'];
					$last_low    = $last_trade['3. low'];
					$last_close  = $last_trade['4. close'];
					$last_volume = (int) $last_trade['5. volume'];

					$prev_open   = $prev_trade['1. open'];
					$prev_high   = $prev_trade['2. high'];
					$prev_low    = $prev_trade['3. low'];
					$prev_close  = $prev_trade['4. close'];
					$prev_volume = (int) $prev_trade['5. volume'];

					// Try fallback for previous data if AV return zero for second day
					if ( '0.0000' == $prev_open ) {
						$prev_open   = $prev_trade_2['1. open'];
						// 3rd day (weekend)
						if ( '0.0000' == $prev_open ) {
							$prev_open   = $prev_trade_3['1. open'];
						}
					}
					if ( '0.0000' == $prev_high ) {
						$prev_high   = $prev_trade_2['2. high'];
						// 3rd day (weekend)
						if ( '0.0000' == $prev_high ) {
							$prev_high   = $prev_trade_3['2. high'];
						}
					}
					if ( '0.0000' == $prev_low ) {
						$prev_low    = $prev_trade_2['3. low'];
						// 3rd day (weekend)
						if ( '0.0000' == $prev_low ) {
							$prev_low    = $prev_trade_3['3. low'];
						}
					}
					if ( '0.0000' == $prev_close ) {
						$prev_close  = $prev_trade_2['4. close'];
						// 3rd day (weekend)
						if ( '0.0000' == $prev_close ) {
							$prev_close  = $prev_trade_3['4. close'];
						}
					}

					// Volume (1st day)
					if ( 0 == $last_volume ) {
						// 2nd day
						$last_volume = (int) $prev_trade['5. volume'];
						// 3rd day
						if ( 0 == $last_volume ) {
							$last_volume = (int) $prev_trade_2['5. volume'];
							// 4th day
							if ( 0 == $last_volume ) {
								$last_volume = (int) $prev_trade_3['5. volume'];
							}
						}
					}
					/**/

					// The difference between 2017-09-01's close price and 2017-08-31's close price gives you the "Change" value.
					$change = $last_close - $prev_close;
					// So the gain on Friday was 25.92 (5025.92 - 5000) or 0.52% (25.92/5000 x 100%). No mystery!
					$change_p = ( $change / $prev_close ) * 100;
					// if we got INF, fake changep to 0
					if ( 'INF' == $change_p ) {
						$change_p = 0;
					}

					// The high and low prices combined give you the "Range" information
					$range = "$last_low - $last_high";

					// unset( $json );
					$data_arr = array(
						't'   => $symbol, // $ticker_symbol,
						'c'   => $change,
						'cp'  => $change_p,
						'l'   => $last_close,
						'lt'  => $last_trade_refresh,
						'ltz' => $last_trade_tz,
						'r'   => $range,
						'o'   => $last_open,
						'h'   => $last_high,
						'low' => $last_low,
						'v'   => $last_volume,
					);
					$data_arr['raw'] = $json;

				}
				unset( $response_arr );
			}

			return $data_arr;

		} // END function fetch_alphavantage_feed( $symbol )

		private function lock_fetch() {
			update_option( 'stockticker_av_progress', true );
			return;
		}
		private function unlock_fetch() {
			update_option( 'stockticker_av_progress', false );
			return;
		}
		private function log( $str ) {
			$log_file = trailingslashit( WP_CONTENT_DIR ) . 'stockticker.log';
			$date = date( 'c' );
			error_log( "{$date}: {$str}\n", 3, $log_file );
		}
	} // END class Wpau_Stock_Ticker

} // END if(!class_exists('Wpau_Stock_Ticker'))

if ( class_exists( 'Wpau_Stock_Ticker' ) ) {
	// Instantiate the plugin class.
	global $wpau_stockticker;
	if ( empty( $wpau_stockticker ) ) {
		$wpau_stockticker = new Wpau_Stock_Ticker();
	}
} // END class_exists( 'Wpau_Stock_Ticker' )
