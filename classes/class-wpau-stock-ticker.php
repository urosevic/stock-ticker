<?php

// Exit if accessed directly
if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

if ( class_exists( 'Wpau_Stock_Ticker' ) ) {
	return;
}

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

	public $plugin_name   = 'Stock Ticker';
	public $plugin_slug   = 'stock-ticker';
	public $plugin_option = 'stockticker_defaults';
	public $plugin_url;
	public $plugin_file;
	public $defaults;

	public $endpoints = array( 'SYMBOL_SEARCH', 'GLOBAL_QUOTE', 'TIME_SERIES_DAILY', 'TIME_SERIES_INTRADAY', 'OVERVIEW' );

	public static $exchanges = array(
		'supported'   => array(
			'BOM'    => 'Bombay Stock Exchange',
			'TSE'    => 'Canadian/Toronto Securities Exchange',
			'FRA'    => 'Deutsche Boerse Frankfurt Stock Exchange',
			'ETR'    => 'Deutsche Boerse Frankfurt Stock Exchange',
			'AMS'    => 'Euronext Amsterdam',
			'EBR'    => 'Euronext Brussels',
			'ELI'    => 'Euronext Lisbon',
			'EPA'    => 'Euronext Paris',
			'LON'    => 'London Stock Exchange',
			'NASDAQ' => 'NASDAQ Exchange',
			'CPH'    => 'NASDAQ OMX Copenhagen',
			'HEL'    => 'NASDAQ OMX Helsinki',
			'ICE'    => 'NASDAQ OMX Iceland',
			'NYSE'   => 'New York Stock Exchange',
			'SHA'    => 'Shanghai Stock Exchange',
			'SHE'    => 'Shenzhen Stock Exchange',
			'TPE'    => 'Taiwan Stock Exchange',
			'TYO'    => 'Tokyo Stock Exchange',
		),
		'unsupported' => array(
			'ASX' => 'Australian Securities Exchange',
			'MCX' => 'Moscow Exchange',
			'NSE' => 'National Stock Exchange of India',
			'SGX' => 'Singapore Exchange',
			'STO' => 'NASDAQ OMX Stockholm',
			'BIT' => 'Borsa Italiana Milan Stock Exchange',
		),
	);

	/**
	 * Construct the plugin object
	 */
	public function __construct() {

		$this->plugin_url  = WPAU_STOCK_TICKER_PLUGIN_URL;
		$this->plugin_file = WPAU_STOCK_TICKER_PLUGIN_FILE;
		load_plugin_textdomain( $this->plugin_slug, false, dirname( plugin_basename( WPAU_STOCK_TICKER_PLUGIN_FILE ) ) . '/languages' );

		// Installation and uninstallation hooks.
		register_activation_hook( __FILE__, array( $this, 'activate' ) );
		register_deactivation_hook( __FILE__, array( $this, 'deactivate' ) );

		// Throw message on multisite
		if ( is_multisite() ) {
			add_action( 'admin_notices', array( $this, 'multisite_notice' ) );
			return;
		}

		// Maybe update trigger.
		add_action( 'plugins_loaded', array( $this, 'maybe_update' ) );

		// Initialize default settings
		$this->defaults = self::defaults();

		// Register AJAX ticker loader
		add_action( 'wp_ajax_stockticker_load', array( $this, 'ajax_stockticker_load' ) );
		add_action( 'wp_ajax_nopriv_stockticker_load', array( $this, 'ajax_stockticker_load' ) );
		// Register AJAX stock updater
		add_action( 'wp_ajax_stockticker_update_quotes', array( $this, 'ajax_stockticker_update_quotes' ) );
		add_action( 'wp_ajax_nopriv_stockticker_update_quotes', array( $this, 'ajax_stockticker_update_quotes' ) );
		// Register AJAX symbol search and test (only for logged in users)
		add_action( 'wp_ajax_stockticker_symbol_search_test', array( $this, 'ajax_stockticker_symbol_search_test' ) );
		// add_action( 'wp_ajax_nopriv_stockticker_symbol_search_test', array( $this, 'ajax_stockticker_symbol_search_test' ) );
		// Restart fetching loop by AJAX request (only for logged in users)
		add_action( 'wp_ajax_stockticker_purge_cache', array( $this, 'ajax_restart_av_fetching' ) );
		// add_action( 'wp_ajax_nopriv_stockticker_purge_cache', array( $this, 'ajax_restart_av_fetching' ) );

		if ( is_admin() ) {
			// Initialize Plugin Settings Magic
			add_action( 'init', array( $this, 'admin_init' ) );
			// Maybe display admin notices?
			add_action( 'admin_notices', array( $this, 'admin_notice' ) );
		} else {
			// Enqueue frontend scripts.
			add_action( 'wp_enqueue_scripts', array( $this, 'enqueue_scripts' ) );
		}

		// Initialize Widget.
		require_once 'class-wpau-stock-ticker-widget.php';

		// Register stock_ticker shortcode.
		add_shortcode( 'stock_ticker', array( $this, 'shortcode' ) );
	} // END public function __construct()

	/**
	 * Throw notice that plugin does not work on Multisite
	 */
	public function multisite_notice() {
		$class   = 'notice notice-error';
		$message = sprintf(
			/* translators: %1$s is Plugin name, %2$s is Plugin version */
			__( 'We are sorry, %1$s v%2$s does not support Multisite WordPress.', 'stock-ticker' ),
			$this->plugin_name,
			WPAU_STOCK_TICKER_VER
		);
		printf( '<div class="%1$s"><p>%2$s</p></div>', esc_attr( $class ), esc_html( $message ) );
	}

	public function admin_notice() {

		$missing_option = array();

		// If no AlphaVantage API Key, display admin notice
		if ( empty( $this->defaults['avapikey'] ) ) {
			$missing_option[] = __( 'AlphaVantage.co API Key', 'stock-ticker' );
		}

		// If no all symbls, display admin notice
		if ( empty( $this->defaults['all_symbols'] ) ) {
			$missing_option[] = __( 'All Stock Symbols', 'stock-ticker' );
		}

		if ( ! empty( $missing_option ) ) {
			$class           = 'notice notice-error';
			$missing_options = '<ul><li>' . implode( '</li><li>', $missing_option ) . '</li></ul>';
			$settings_title  = __( 'Settings' );
			$settings_link   = "<a href=\"options-general.php?page={$this->plugin_slug}\">{$settings_title}</a>";
			$message         = sprintf(
				/* translators: %1$s is Plugin name, %2$s is Plugin version, %3$s is link to the settings page, %4$s is list of missing options */
				__( 'Plugin %1$s v%2$s require that you have defined options listed below to work properly. Please visit plugin %3$s page and read description for those options. %4$s', 'stock-ticker' ),
				"<strong>{$this->plugin_name}</strong>",
				WPAU_STOCK_TICKER_VER,
				$settings_link,
				$missing_options
			);
			printf( '<div class="%1$s"><p>%2$s</p></div>', esc_attr( $class ), $message );
		}
	} // END function admin_notice()

	/**
	 * Activate the plugin
	 */
	public function activate() {
		// Auto disable on WPMU
		if ( is_multisite() ) {
			deactivate_plugins( plugin_basename( __FILE__ ) );
			wp_die(
				sprintf(
					/* translators: %1$s is Plugin Name, %2$s is Plugin version */
					__( 'We are sorry, %1$s v%2$s does not support Multisite WordPress.', 'stock-ticker' ),
					$this->plugin_name,
					WPAU_STOCK_TICKER_VER
				)
			);
		}
		// Single WP activation process
		global $wpau_stockticker;
		$wpau_stockticker->init_options();
		$wpau_stockticker->maybe_update();
	} // END function activate()

	/**
	 * Deactivate the plugin
	 */
	public function deactivate() {
		// Do nothing.
	} // END function deactivate()

	/**
	 * Return initial options
	 * @return array Global defaults for current plugin version
	 */
	public function init_options() {

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
			'timeout'         => 4,
			'reload'          => false,
			'reload_timeout'  => 5 * MINUTE_IN_SECONDS,
			'speed'           => 50,
			'globalassets'    => false,
			'avapikey'        => '',
			'loading_message' => 'Loading stock data...',
			'number_format'   => 'dc', // dot comma
			'decimals'        => 2,
			'av_api_tier'     => 5, // 5 = free
		);

		add_option( $this->plugin_option, $init, '', 'no' );

		return $init;
	} // END function init_options() {

	/**
	 * Check do we need to migrate options
	 */
	public function maybe_update() {
		// Bail if this plugin data doesn't need updating
		if ( get_option( 'stockticker_db_ver', 0 ) >= WPAU_STOCK_TICKER_DB_VER ) {
			return;
		}
		require_once WPAU_STOCK_TICKER_DIR . '/update.php';
		au_stockticker_update();
	} // END function maybe_update()

	/**
	 * Initialize Settings link for Plugins page and create Settings page
	 *
	 */
	public function admin_init() {

		// Add plugin Settings link.
		add_filter( 'plugin_action_links_' . $this->plugin_file, array( $this, 'add_action_links' ) );

		// Update links in plugin row on Plugins page.
		add_filter( 'plugin_row_meta', array( $this, 'add_plugin_meta_links' ), 10, 2 );

		// Load colour picker scripts on plugin settings page and on widgets/customizer.
		add_action( 'admin_enqueue_scripts', array( $this, 'admin_scripts' ) );

		require_once 'class-wpau-stock-ticker-settings.php';

		global $wpau_stockticker_settings;
		if ( empty( $wpau_stockticker_settings ) ) {
			$wpau_stockticker_settings = new Wpau_Stock_Ticker_Settings();
		}
	} // END function admin_init()

	/**
	 * Append Settings link for Plugins page
	 *
	 * @param array $links Array of default plugin links
	 *
	 * @return array       Array of plugin links with appended link for Settings page
	 */
	public function add_action_links( $links ) {
		$links[] = '<a href="' . esc_url( admin_url( 'options-general.php?page=' . $this->plugin_slug ) ) . '">' . esc_html__( 'Settings' ) . '</a>';
		return $links;
	} // END function add_action_links()

	/**
	 * Add link to plugin community support
	 *
	 * @param array $links Array of default plugin meta links
	 * @param string $file Current hook file path
	 *
	 * @return array       Array of default plugin meta links with appended link for Support community forum
	 */
	public function add_plugin_meta_links( $links, $file ) {
		if ( 'stock-ticker/stock-ticker.php' === $file ) {
			$links[] = '<a href="https://wordpress.org/support/plugin/stock-ticker/" target="_blank">' . esc_html__( 'Support' ) . '</a>';
		}

		// Return updated array of links
		return $links;
	} // END function add_plugin_meta_links()

	/**
	 * Enqueue the colour picker and admin style
	 */
	public function admin_scripts( $hook ) {
		if ( 'settings_page_' . $this->plugin_slug === $hook ) {
			wp_enqueue_style( 'wp-color-picker' );
			wp_enqueue_script( 'wp-color-picker' );
			wp_enqueue_style(
				$this->plugin_slug . '-admin', // 'stock-ticker',
				$this->plugin_url . 'assets/css/admin.min.css',
				array(),
				WPAU_STOCK_TICKER_VER
			);
			wp_register_script(
				'stock-ticker-admin',
				$this->plugin_url . ( WP_DEBUG ? 'assets/js/jquery.admin.js' : 'assets/js/jquery.admin.min.js' ),
				array( 'jquery' ),
				WPAU_STOCK_TICKER_VER,
				true
			);
			wp_localize_script(
				'stock-ticker-admin',
				'stockTickerJs',
				array(
					'ajax_url' => admin_url( 'admin-ajax.php' ),
					'avurl'    => 'https://www.alphavantage.co/query?function=GLOBAL_QUOTE&apikey=' . $this->defaults['avapikey'] . '&datatype=json&symbol=',
					'nonce'    => wp_create_nonce( 'stock-ticker-js' ),
				)
			);
			wp_enqueue_script( 'stock-ticker-admin' );
		}
	} // END function admin_scripts()

	/**
	 * Enqueue frontend assets
	 */
	public function enqueue_scripts() {
		$defaults   = $this->defaults;
		$upload_dir = wp_upload_dir();

		wp_enqueue_script(
			'jquery-webticker',
			$this->plugin_url . ( WP_DEBUG ? 'assets/js/jquery.webticker.js' : 'assets/js/jquery.webticker.min.js' ),
			array( 'jquery' ),
			'2.2.0.2',
			true
		);
		wp_enqueue_style(
			'stock-ticker',
			$this->plugin_url . 'assets/css/stock-ticker.min.css',
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
			$this->plugin_url . ( WP_DEBUG ? 'assets/js/jquery.stockticker.js' : 'assets/js/jquery.stockticker.min.js' ),
			array( 'jquery', 'jquery-webticker' ),
			WPAU_STOCK_TICKER_VER,
			true
		);
		wp_localize_script(
			'stock-ticker',
			'stockTickerJs',
			array(
				'ajax_url' => admin_url( 'admin-ajax.php' ),
				'nonce'    => wp_create_nonce( 'stock-ticker-js' ),
			)
		);
		// Enqueue script parser
		if ( isset( $defaults['globalassets'] ) ) {
			wp_enqueue_script( 'stock-ticker' );
		}

		// Register reload script if option is enabled
		if ( ! empty( $defaults['reload'] ) ) {
			wp_register_script(
				'stock-ticker-reload',
				set_url_scheme( $upload_dir['baseurl'] ) . '/stock-ticker-reload.js',
				array( 'jquery', 'jquery-webticker', 'stock-ticker' ),
				WPAU_STOCK_TICKER_VER,
				true
			);
			wp_enqueue_script( 'stock-ticker-reload' );
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
		update_option( 'stockticker_av_last', '' );
		$expired_timestamp = time() - ( 10 * YEAR_IN_SECONDS );
		update_option( 'stockticker_av_last_timestamp', $expired_timestamp );
		update_option( 'stockticker_av_progress', false );
		self::log( 'Stock Ticker: data fetching from first symbol has been restarted' );
	} // END public static function restart_av_fetching() {

	/**
	 * Restart AV fetching
	 * Allowed only on backend for logged in users with `manage_options` permission
	 *
	 * @return json|void
	 */
	public function ajax_restart_av_fetching() {
		// Check permission and validate ajax nonce
		if ( ! current_user_can( 'manage_options' ) ) {
			wp_die( -1, 403 );
		}
		check_ajax_referer( 'stock-ticker-js', 'nonce' );

		self::restart_av_fetching();
		$result['status']  = 'success';
		$result['message'] = 'OK';

		echo json_encode( $result );
		wp_die();
	} // END function ajax_restart_av_fetching() {

	/**
	 * Function to get stock data from DB and return JSON object
	 * Allowed for backend and frontend
	 *
	 * @return json
	 */
	public function ajax_stockticker_load() {
		check_ajax_referer( 'stock-ticker-js', 'nonce' );

		// @TODO Provide error message if any of params missing + add nonce check
		if ( ! empty( $_POST['symbols'] ) ) {
			// Sanitize data
			$symbols       = self::sanitize_symbols( $_POST['symbols'] );
			$show          = sanitize_html_class( $_POST['show'] );
			$number_format = sanitize_html_class( $_POST['number_format'] );
			$decimals      = (int) $_POST['decimals'];
			$static        = (int) $_POST['static'];
			$empty         = (int) $_POST['empty'];
			$duplicate     = (int) $_POST['duplicate'];
			$class         = sanitize_html_class( $_POST['class'] );
			$speed         = (int) $_POST['speed'];

			// Treat as error if no stock ticker composed but 'Unfortunately' message displayed
			$message = self::stock_ticker( $symbols, $show, $number_format, $decimals, $static, $empty, $duplicate, $class );
			if ( strpos( $message, 'error' ) !== false ) {
				$message           = strip_tags( $message );
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

		echo json_encode( $result );
		wp_die();
	} // END function ajax_stockticker_load() {

	/**
	 * AJAX to update AlphaVantage.co quotes
	 * Allowed for backend and frontend
	 *
	 * @return json
	 */
	public function ajax_stockticker_update_quotes() {
		check_ajax_referer( 'stock-ticker-js', 'nonce' );

		$response          = $this->get_alphavantage_quotes();
		$result['status']  = 'success';
		$result['message'] = $response['message'];
		$result['symbol']  = $response['symbol'];
		$result['method']  = $response['method'];

		if ( strpos( $result['message'], 'no need to start' ) !== false ) {
			$result['done']    = true;
			$result['message'] = 'DONE';
		} elseif ( strpos( $result['message'], 'API Key tier daily quota reached' ) !== false ) {
			$result['done'] = true;
		} else {
			$result['done'] = false;
			// If we have some plugin functionality fatal error
			// (missing API key, no symbols, can't write to DB, etc)
			// then throw error and signal stop fetching:
			// * There is no defined All Stock Symbols
			// * Failed to save stock data for {$symbol_to_fetch} to database!
			// * AlphaVantage.co API key has not set
			if ( strpos( $result['message'], 'Stock Ticker Fatal Error:' ) !== false ) {
				$result['done'] = true;
			}
		}

		echo json_encode( $result );
		wp_die();
	} // END function ajax_stockticker_update_quotes()

	/**
	 * AJAX to search for symbol on AlphaVantage.co
	 * Allowed only on backend for logged in users with `manage_options` permission
	 *
	 * @return json
	 */
	public function ajax_stockticker_symbol_search_test() {

		// Check permission and validate ajax nonce
		if ( ! current_user_can( 'manage_options' ) ) {
			wp_die( -1, 403 );
		}
		check_ajax_referer( 'stock-ticker-js', 'nonce' );

		$symbol            = strip_tags( $_POST['symbol'] );
		$endpoint          = strip_tags( $_POST['endpoint'] );
		$result['message'] = $this->av_query_endpoint( $endpoint, $symbol );

		echo json_encode( $result );
		wp_die();
	} // END function ajax_stockticker_symbol_search_test()

	/**
	 * Do AlphaVantage.co API request to targeted endpoint
	 * @param  string $endpoint AlphaVantage.co supported endpoint GLOBAL_QUOTE or SYMBOL_SEARCH
	 * @param  string $item     Item to query for (symbol or keywords)
	 * @return string           Error message or JSON encoded response from API
	 */
	public function av_query_endpoint( $endpoint = '', $item = '' ) {
		// Exit if we don't have API Key, supported endpoint and requested item.
		if ( empty( $this->defaults['avapikey'] ) ) {
			return 'Stock Ticker Fatal Error: AlphaVantage.co API key has not set.';
		} elseif ( empty( $item ) ) {
			return 'Stock Ticker Fatal Error: No item provided for query.';
		} elseif ( ! in_array( $endpoint, $this->endpoints, true ) ) {
			return 'Stock Ticker Fatal Error: AlphaVantage.co API endpoint ' . strtoupper( $endpoint ) . ' has not supported.';
		}

		// Get current timestamp (for reference)
		$timestamp_now = time();

		// Get API Tier and calculate timeout
		$av_api_tier         = ! empty( $this->defaults['av_api_tier'] ) ? $this->defaults['av_api_tier'] : 5;
		$av_api_tier_timeout = 60 / $av_api_tier;

		// If user has Free tier, count daily limit (25 requests per day)
		if ( 5 === $av_api_tier ) {
			$av_api_tier_free_quota = get_option(
				'av_api_tier_free_quota',
				array(
					'day'  => gmdate( 'Ymd' ),
					'used' => 1,
				)
			);
			if ( $av_api_tier_free_quota['used'] >= 25 ) {
				if ( gmdate( 'Ymd' ) === $av_api_tier_free_quota['day'] ) {
					self::log( 'API Free Tier daily quota reached.' );
					return 'Alpha Vantage Free API Key tier daily quota reached (25 requests/day). Consider upgrade to Premium tier, or try again tomorrow...';
				} else {
					self::log( 'Reset used count to 1 when new day begins' );
					$av_api_tier_free_quota['used'] = 1;
				}
			} else {
				// Increase daily used number
				$av_api_tier_free_quota['used'] = intval( $av_api_tier_free_quota['used'] ) + 1;
			}
			update_option( 'av_api_tier_free_quota', $av_api_tier_free_quota );
		}

		// Get API Tier end timestamp for previous fetch
		$api_tier_end_timestamp = get_option( 'stockticker_av_tier_end_timestamp', $timestamp_now );
		if ( $timestamp_now < $api_tier_end_timestamp ) {
			self::log( 'API Tier timeout for previous request of ' . $av_api_tier_timeout . ' seconds has not expired... waiting...' );
			return json_encode(
				array(
					'message' => "API Tier timeout for previous request has not expired. Try again in {$av_api_tier_timeout} second(s) ...",
					'method'  => 'wait',
				)
			);
		}

		// Calculate API tier pause and save it to options and get data
		$av_api_tier_end_timestamp = $timestamp_now + $av_api_tier_timeout;
		update_option( 'stockticker_av_tier_end_timestamp', $av_api_tier_end_timestamp );

		self::log( "Use endpoint {$endpoint} for {$item}..." );

		if ( 'TIME_SERIES_INTRADAY' === $endpoint ) {
			$extra_params = '&interval=60min';
		} else {
			$extra_params = '';
		}

		// Define AplhaVantage API URL
		$feed_url = sprintf(
			'https://www.alphavantage.co/query?function=%1$s&apikey=%2$s&datatype=json&%3$s=%4$s%5$s',
			$endpoint,
			$this->defaults['avapikey'],
			'SYMBOL_SEARCH' === $endpoint ? 'keywords' : 'symbol',
			$item,
			$extra_params
		);
		$wparg    = array(
			'timeout' => intval( $this->defaults['timeout'] ),
		);

		self::log( 'Fetching data from AV: ' . $feed_url );
		$response = wp_remote_get( $feed_url, $wparg );

		// If we have WP error log it and return none
		if ( is_wp_error( $response ) ) {
			return 'Stock Ticker got error fetching feed from AlphaVantage.co: ' . $response->get_error_message();
		} else {
			// Get response from AV and parse it - look for error
			$json         = wp_remote_retrieve_body( $response );
			$response_arr = json_decode( $json, true );

			// If we got some error from AV, log to self::log and return none
			if ( ! empty( $response_arr['Error Message'] ) ) {
				return 'Stock Ticker connected to AlphaVantage.co but got error: ' . $response_arr['Error Message'];
			} elseif ( ! empty( $response_arr['Information'] ) ) {
				return 'Stock Ticker connected to AlphaVantage.co and got response: ' . $response_arr['Information'];
			} elseif ( 'GLOBAL_QUOTE' === $endpoint && ! isset( $response_arr['Global Quote'] ) ) {
				return 'Bad API response: Stock Ticker connected to AlphaVantage.co and received response w/o Global Quote object!';
			} else {
				return $json;
			}
		}
	} // END public function av_query_endpoint($endpoint = '', $item = '')

	/**
	 * Generate and output stock ticker block
	 * @param  string   $symbols       Comma separated array of symbols.
	 * @param  string   $show          What to show (name or symbol).
	 * @param  bool     $mode_static        Request for static (non-animated) block.
	 * @param  bool     $start_empty         Start ticker empty or prefilled with symbols.
	 * @param  bool     $duplicate     If there is less items than visible on the ticker make it continuous
	 * @param  string   $block_class         Custom class for styling Stock Ticker block.
	 * @param  integer  $decimals      Number of decimal places.
	 * @param  string   $number_format Which number format to use (dc, sc, cd, sd).
	 * @return string          Composed HTML for block.
	 */
	public function stock_ticker(
		$symbols,
		$show,
		$number_format = null,
		$decimals = null,
		$mode_static = false,
		$start_empty = true,
		$duplicate = false,
		$block_class = ''
	) {

		if ( empty( $symbols ) ) {
			return;
		}

		// Get legend for company names.
		$defaults = $this->defaults;

		// Prepare number format
		if ( ! empty( $number_format ) && in_array( $number_format, array( 'dc', 'sd', 'sc', 'cd' ), true ) ) {
			$defaults['number_format'] = $number_format;
		} elseif ( ! isset( $defaults['number_format'] ) ) {
			$defaults['number_format'] = 'dc';
		}
		switch ( $defaults['number_format'] ) {
			case 'cd': // 0,000.00
				$thousands_sep = ',';
				$dec_point     = '.';
				break;
			case 'sd': // 0 000.00
				$thousands_sep = ' ';
				$dec_point     = '.';
				break;
			case 'sc': // 0 000,00
				$thousands_sep = ' ';
				$dec_point     = ',';
				break;
			default: // dc - 0.000,00
				$thousands_sep = '.';
				$dec_point     = ',';
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
		$msize  = count( $matrix );
		for ( $m = 0; $m < $msize; ++$m ) {
			$line = explode( ';', $matrix[ $m ] );
			if ( ! empty( $line[0] ) && ! empty( $line[1] ) ) {
				$legend[ strtoupper( trim( $line[0] ) ) ] = trim( $line[1] );
			}
		}
		unset( $m, $msize, $matrix, $line );

		// Prepare ticker.
		if ( ! empty( $mode_static ) && 1 === $mode_static ) {
			$block_class .= ' static';
		}

		// Prepare out vars
		$out_start     = sprintf( '<ul class="stock_ticker %s">', $block_class );
		$out_end       = '</ul>';
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
			$q_price   = $stock_data[ $symbol ]['last_close']; // ['l']; // last_close not last_open
			$q_changep = $stock_data[ $symbol ]['changep']; // ['cp'];
			$q_volume  = $stock_data[ $symbol ]['last_volume'];
			// Timezone.
			$q_tz = $stock_data[ $symbol ]['tz'];
			// Date.
			$q_ltrade_raw = $stock_data[ $symbol ]['last_refreshed'];
			// Make default plugin date format (for tooltip) w/o zero time.
			$q_ltrade = $q_ltrade_raw; // ['lt'];
			$q_ltrade = str_replace( ' 00:00:00', '', $q_ltrade ); // Strip zero time from last trade date string
			$q_ltrade = "{$q_ltrade} {$q_tz}";
			// Extract Exchange from Symbol.
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
				$prefix  = '+';
			} else {
				$chclass  = 'zero';
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
			if ( 'name' === $show ) {
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
				$quote_title = $q_name . ' (' . self::$exchanges['supported'][ $q_exch ] . ', Volume ' . $q_volume . ', Last trade ' . $q_ltrade . ')';
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

			// Now simply replace not customized %ltrade% with default format.
			$template = str_replace( '%ltrade%', $q_ltrade, $template );

			// Check if template has custom date format for %ltrade%
			// Usage: %ltrade% custom date format %ltrade|F j, Y%
			if ( false !== strpos( $template, '%ltrade|' ) ) {
				// Match all %ltrade% occurances with custom date format
				preg_match_all( '/(\%ltrade\|[^%]+\%)+/', $template, $ltrade_formats );

				// Just for testing...
				$test = $ltrade_formats[0];

				// If matches array exists, proceed with custom formatting
				if ( ! empty( $ltrade_formats[0] ) ) {
					// Convert date from quote to timestamp - use $q_ltrade and $q_tz for timezone conversion.
					// $ltrade_datetime = strtotime( $q_ltrade_raw );
					$ltrade_date = date_create_from_format( 'Y-m-d H:i:s', $q_ltrade_raw, new DateTimeZone( $q_tz ) );

					// Now process each custom date format %ltrade% occurance.
					foreach ( $ltrade_formats[0] as $ltrade_format ) {

						// Extract custom date format.
						$ltrade_date_format = str_replace( '%ltrade|', '', $ltrade_format );
						$ltrade_date_format = str_replace( '%', '', $ltrade_date_format );

						// Format timestamp to custom date format.
						$ltrade_date_formatted = date_format(
							$ltrade_date,
							$ltrade_date_format
						);

						// Now replace custom date format %ltrade% in $template with formatted date.
						$template = str_replace(
							$ltrade_format,
							$ltrade_date_formatted,
							$template
						);
					}
				}
			}

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
	}
	// END public function stock_ticker()

	/**
	 * Shortcode processor for Stock Ticker
	 * @param  array $atts    Array of shortcode parameters.
	 * @return string         Generated HTML output for block.
	 */
	public function shortcode( $atts ) {
		$defaults = $this->defaults;

		$atts = shortcode_atts(
			array(
				'symbols'         => $defaults['symbols'],
				'show'            => $defaults['show'],
				'number_format'   => isset( $defaults['number_format'] ) ? $defaults['number_format'] : 'dc', // dot comma
				'decimals'        => isset( $defaults['decimals'] ) ? $defaults['decimals'] : 2,
				'static'          => 0,
				'nolink'          => 0,
				'prefill'         => 0,
				'duplicate'       => 0,
				'speed'           => isset( $defaults['speed'] ) ? $defaults['speed'] : 50,
				'class'           => '',
				'loading_message' => isset( $defaults['loading_message'] ) ? $defaults['loading_message'] : __( 'Loading stock data...', 'stock-ticker' ),
			),
			$atts
		);

		// Strip tags as we allow only real symbols
		$symbols = $this->sanitize_symbols( strip_tags( $atts['symbols'] ) );

		// If we don't have defined symbols, just finish
		if ( empty( $symbols ) ) {
			return;
		}

		// Otherwise, enqueue script and print stock holder

		// Enqueue script parser on demand
		if ( empty( $defaults['globalassets'] ) ) {
			wp_enqueue_script( 'stock-ticker' );
			if ( ! empty( $defaults['reload'] ) ) {
				wp_enqueue_script( 'stock-ticker-reload' );
			}
		}

		// Sanitize provided parameters
		// show (name|symbol)
		$show = ! in_array( $atts['show'], array( 'name', 'symbol' ), true ) ? $defaults['show'] : $atts['show'];
		// number_format (cd|dc|sd|sc)
		$number_format = ! in_array( $atts['number_format'], array( 'cd', 'dc', 'sd', 'sc' ), true ) ? $defaults['number_format'] : $atts['number_format'];
		// startEmpty based on prefill option (true|false)
		$start_empty = empty( $atts['prefill'] ) || 'false' === strtolower( $atts['prefill'] ) ? true : false;
		// duplicate (true|false)
		$duplicate = empty( $atts['duplicate'] ) || 'false' === strtolower( $atts['duplicate'] ) ? false : true;
		// static (true|false)
		$static = empty( $atts['static'] ) || 'false' === strtolower( $atts['static'] ) ? false : true;
		// loading_message is plain text w/o HTML and garbage
		$loading_message = strip_tags( $atts['loading_message'] );
		// class allow multiple classes
		$class = sanitize_html_classes( $atts['class'] );

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
			$symbols,                     // 1
			esc_attr( $show ),            // 2
			(bool) $static,               // 3
			esc_attr( $number_format ),   // 4
			$class,                       // 5
			(int) $atts['speed'],         // 6
			(bool) $start_empty,          // 7
			(bool) $duplicate,            // 8
			esc_html( $loading_message ), // 9
			(int) $atts['decimals']       // 10
		);
	} // END public function shortcode()

	// Thanks to https://coderwall.com/p/zepnaw/sanitizing-queries-with-in-clauses-with-wpdb-on-wordpress
	/**
	 * Retrieve stock data for symbol from local database
	 * @param  string $symbols  Stock symbol to get data for (single or multiple symbols separate with comma)
	 * @return array            Array of stock data for symbols
	 */
	public static function get_stock_from_db( $symbols = '' ) {
		// If no symbols we have to fetch from DB, then exit
		if ( empty( $symbols ) ) {
			return;
		}

		// Sanitize symbols
		$symbols = self::sanitize_symbols( $symbols );
		// Explode symbols to array
		$symbols_arr = explode( ',', $symbols );
		// Count how many entries will we select?
		$how_many = count( $symbols_arr );
		// Prepare the right amount of placeholders for each symbol
		$placeholders = array_fill( 0, $how_many, '%s' );
		// Glue together all the placeholders...
		$format = implode( ',', $placeholders );

		// From below we'll do magic within database
		global $wpdb;

		// Put all in the query, prepare and get results
		$stock_data_a = $wpdb->get_results(
			$wpdb->prepare(
				// phpcs:ignore Squiz.Strings.DoubleQuoteUsage.NotRequired
				"
				SELECT `symbol`,`tz`,`last_refreshed`,`last_open`,`last_high`,`last_low`,`last_close`,`last_volume`,`change`,`changep`,`range`
				FROM " . $wpdb->prefix . "stock_ticker_data
				WHERE symbol IN ( $format )", // phpcs:ignore WordPress.DB.PreparedSQL.InterpolatedNotPrepared, WordPress.DB.PreparedSQLPlaceholders.UnfinishedPrepare
				$symbols_arr
			),
			ARRAY_A
		);

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
	 * @return array Array of symbol data fetch status with message, symbol and method
	 */
	public function get_alphavantage_quotes() {

		// Get current timestamp (for reference)
		$timestamp_now = time();

		// Get API Tier and calculate timeout
		$av_api_tier         = ! empty( $this->defaults['av_api_tier'] ) ? $this->defaults['av_api_tier'] : 5;
		$av_api_tier_timeout = 60 / $av_api_tier;

		// If user has Free tier, count daily limit (25 requests per day)
		if ( 5 === $av_api_tier ) {
			$av_api_tier_free_quota = get_option(
				'av_api_tier_free_quota',
				array(
					'day'  => gmdate( 'Ymd' ),
					'used' => 1,
				)
			);
			if ( $av_api_tier_free_quota['used'] >= 25 ) {
				if ( gmdate( 'Ymd' ) === $av_api_tier_free_quota['day'] ) {
					self::log( 'API Free Tier daily quota reached.' );
					return array(
						'message' => 'Alpha Vantage Free API Key tier daily quota reached (25 requests/day). Consider upgrade to Premium tier, or try again tomorrow...', // phpcs:ignore
						'symbol'  => '',
						'method'  => 'skip',
					);
				} else {
					self::log( 'Reset used count to 1 when new day begins' );
					$av_api_tier_free_quota['used'] = 1;
				}
			} else {
				// Increase daily used number
				$av_api_tier_free_quota['used'] = intval( $av_api_tier_free_quota['used'] ) + 1;
			}
			update_option( 'av_api_tier_free_quota', $av_api_tier_free_quota );
		}

		// Get API Tier end timestamp for previous fetch
		$api_tier_end_timestamp = get_option( 'stockticker_av_tier_end_timestamp', $timestamp_now );
		if ( $timestamp_now < $api_tier_end_timestamp ) {
			self::log( 'API Tier timeout for previous symbol of ' . $av_api_tier_timeout . ' has not expired... waiting...' );
			return array(
				'message' => "API Tier timeout for previous symbol has not expired. Waiting {$av_api_tier_timeout} second(s) ...",
				'symbol'  => '',
				'method'  => 'wait',
			);
		}

		// Check is fetch in progress (even with expired API Tier timeout)
		$progress = get_option( 'stockticker_av_progress', false );
		if ( true === $progress ) {
			return array(
				'message' => 'Stock Ticker already fetching data. Skip.',
				'symbol'  => '',
				'method'  => 'skip',
			);
		}

		// Get index and symbol to fetch
		$to_fetch = self::get_symbol_to_fetch();

		// Define method for symbol
		$method = 'global_quote';

		// If $to_fetch['index'] is 0 and cache timeout has not expired,
		// do not attempt to fetch again but wait to expire timeout for next loop (UTC)
		if ( 0 === $to_fetch['index'] ) {
			$last_fetched_timestamp = get_option( 'stockticker_av_last_timestamp', $timestamp_now );
			$target_timestamp       = $last_fetched_timestamp + (int) $this->defaults['cache_timeout'];
			if ( $target_timestamp > $timestamp_now ) {
				// If timestamp not expired, do not fetch but exit
				self::unlock_fetch();
				return array(
					'message' => 'Cache timeout has not expired, no need to start new fetch loop at the moment.',
					'symbol'  => $to_fetch['symbol'],
					'method'  => $method,
				);
			} else {
				// If timestamp expired, set new value and proceed
				update_option( 'stockticker_av_last_timestamp', $timestamp_now );
				self::log( 'Set timestamp now when first symbol is fetched as a reference for next fetch loop' );
			}
		}

		// Calculate API tier pause and save it to options and get data
		$av_api_tier_end_timestamp = $timestamp_now + $av_api_tier_timeout;
		update_option( 'stockticker_av_tier_end_timestamp', $av_api_tier_end_timestamp );

		// Now call AlphaVantage fetcher for current symbol
		$stock_data = $this->fetch_alphavantage_feed( $to_fetch['symbol'] );

		// If we have not got array with stock data, exit w/o updating DB
		if ( ! is_array( $stock_data ) ) {
			self::log( $stock_data );

			// If it's Invalid API call, report and skip it
			if ( strpos( $stock_data, 'Invalid API call' ) >= 0 ) {
				self::log( "Damn, we got Invalid API call for symbol {$to_fetch['symbol']}" );
				update_option( 'stockticker_av_last', $to_fetch['symbol'] );
			} elseif ( strpos( $stock_data, 'Bad API response' ) >= 0 ) {
				// Bad response
				// Stock Ticker connected to AlphaVantage.co but received unusable response. Try to prefix symbol with stock exchange.
				self::log( "Damn, we got Bad API response for symbol {$to_fetch['symbol']}" );
				update_option( 'stockticker_av_last', $to_fetch['symbol'] );
			}

			// If we got some error for first symbol, (and resnponse has not invalid API) revert last timestamp
			if ( 0 === $to_fetch['index'] && false === strpos( $stock_data, 'Invalid API call' ) && false === strpos( $stock_data, 'Bad API response' ) ) {
				self::log( 'Failed fetching and crunching for first symbol, set back previous timestamp' );
				update_option( 'stockticker_av_last_timestamp', $last_fetched_timestamp );
			}
			// Release processing for next run
			self::unlock_fetch();
			// Return response status
			return array(
				'message' => $stock_data,
				'symbol'  => $to_fetch['symbol'],
				'method'  => $method,
			);
		}

		// Now write object of stock data to DB
		$ret = self::data_to_db( $to_fetch, $stock_data );

		// Is failed updated data in DB
		if ( false === $ret ) {
			$msg = "Stock Ticker Fatal Error: Failed to save stock data for {$to_fetch['symbol']} to database!";
			self::log( $msg );
			// Release processing for next run
			self::unlock_fetch();
			// Return failed status
			return array(
				'message' => $msg,
				'symbol'  => $to_fetch['symbol'],
				'method'  => $method,
			);
		}

		// After success update in database, report in log
		$msg = "Stock data for symbol {$to_fetch['symbol']} has been updated in database.";
		self::log( $msg );
		// Set last fetched symbol
		update_option( 'stockticker_av_last', $to_fetch['symbol'] );
		// Release processing for next run
		self::unlock_fetch();
		// Return succes status
		return array(
			'message' => $msg,
			'symbol'  => $to_fetch['symbol'],
			'method'  => $method,
		);
	} // END function get_alphavantage_quotes()

	/**
	 * Define index (position) and symbol of one to fetch
	 * @return array Arary with index of symbol and symbol to fetch
	 */
	public function get_symbol_to_fetch() {

		// Get symbols we should to fetch from AlphaVantage
		$symbols = $this->defaults['all_symbols'];

		// Make array of global symbols
		$symbols_arr = explode( ',', $symbols );

		// Default symbol to fetch first (first from array)
		$current_symbol_index = 0;
		$symbol_to_fetch      = $symbols_arr[ $current_symbol_index ];

		// Get last fetched symbol
		$last_fetched = strtoupper( get_option( 'stockticker_av_last' ) );

		// Find which symbol we should fetch
		if ( ! empty( $last_fetched ) ) {
			$last_symbol_index    = array_search( $last_fetched, $symbols_arr, true );
			$current_symbol_index = $last_symbol_index + 1;
			// If we have less than next symbol, then rewind to beginning
			if ( count( $symbols_arr ) <= $current_symbol_index ) {
				$current_symbol_index = 0;
			} else {
				$symbol_to_fetch = strtoupper( $symbols_arr[ $current_symbol_index ] );
			}
		}
		// Return array
		return array(
			'index'  => $current_symbol_index,
			'symbol' => $symbol_to_fetch,
		);
	} // END public function get_symbol_to_fetch()

	/**
	 * Save data retrieved from AV API to DB
	 * @param  array $to_fetch    Array of index of symbol and symbol to fetch
	 * @param  array $stock_data  Array of stock data
	 * @return array              Result of MySQL query
	 */
	private function data_to_db( $to_fetch, $stock_data ) {

		// With success stock data in array, save data to database
		global $wpdb;
		// Define plugin table name
		$table_name = $wpdb->prefix . 'stock_ticker_data';

		// Check does symbol already exists in DB (to update or to insert new one)
		// I'm not using here $wpdb->replace() as I wish to avoid reinserting row to table which change primary key (delete row, insert new row)
		$symbol_exists = $wpdb->get_var(
			$wpdb->prepare(
				// phpcs:ignore Squiz.Strings.DoubleQuoteUsage.NotRequired
				"SELECT symbol FROM " . $wpdb->prefix . "stock_ticker_data WHERE symbol = %s",
				$to_fetch['symbol']
			)
		);
		if ( ! empty( $symbol_exists ) ) {
			// UPDATE
			$ret = $wpdb->update(
				// table
				$table_name,
				// data
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
				// WHERE
				array(
					'symbol' => $stock_data['t'],
				),
				// format
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
				),
				// WHERE format
				array(
					'%s',
				)
			);
		} else {
			// INSERT
			$ret = $wpdb->insert(
				// table
				$table_name,
				// data
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
				// format
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
		}
		return $ret;
	} // END private function data_to_db( $to_fetch, $stock_data )

	public function fetch_alphavantage_feed( $symbol ) {

		self::log( "Fetching data for symbol {$symbol}..." );

		// Get defaults (for API key)
		$defaults = $this->defaults;

		// Exit if we don't have API Key
		if ( empty( $defaults['avapikey'] ) ) {
			return 'Stock Ticker Fatal Error: AlphaVantage.co API key has not set';
		}

		// Define AplhaVantage API URL
		self::log( "Using GLOBAL_QUOTE for {$symbol}..." );
		$feed_url = 'https://www.alphavantage.co/query?function=GLOBAL_QUOTE&apikey=' . $defaults['avapikey'] . '&datatype=json&symbol=' . $symbol;

		$wparg = array(
			'timeout' => intval( $defaults['timeout'] ),
		);

		self::log( 'Fetching data from AV: ' . $feed_url );
		$response = wp_remote_get( $feed_url, $wparg );

		// Initialize empty $json variable
		$data_arr = '';

		// If we have WP error log it and return none
		if ( is_wp_error( $response ) ) {
			return 'Stock Ticker got error fetching feed from AlphaVantage.co: ' . $response->get_error_message();
		} else {
			// Get response from AV and parse it - look for error
			$json         = wp_remote_retrieve_body( $response );
			$response_arr = json_decode( $json, true );
			// If we got some error from AV, log to self::log and return none
			if ( ! empty( $response_arr['Error Message'] ) ) {
				return 'Stock Ticker connected to AlphaVantage.co but got error: ' . $response_arr['Error Message'];
			} elseif ( ! empty( $response_arr['Information'] ) ) {
				return 'Stock Ticker connected to AlphaVantage.co and got response: ' . $response_arr['Information'];
			} elseif ( ! isset( $response_arr['Global Quote'] ) ) {
				return 'Bad API response: Stock Ticker connected to AlphaVantage.co and received response w/o Global Quote object!';
			} else {
				// Crunch data from AlphaVantage for symbol and prepare compact array
				self::log( "We got some data from AlphaVantage for $symbol, so now let we crunch them and save to database if possible..." );

				// GLOBAL_QUOTE
				$quote = $response_arr['Global Quote'];
				if ( empty( $quote['07. latest trading day'] ) ) {
					return 'Bad API response: Stock Ticker connected to AlphaVantage.co and received empty Global Quote object.';
				}
				$data_arr = array(
					't'   => $symbol,
					'pc'  => $quote['08. previous close'],
					'c'   => $quote['09. change'],
					'cp'  => str_replace( '%', '', $quote['10. change percent'] ),
					'l'   => $quote['05. price'], // $last_close,
					'lt'  => $quote['07. latest trading day'], // $last_trade_refresh,
					'ltz' => 'US/Eastern', // default US/Eastern
					'r'   => "{$quote['04. low']} - {$quote['03. high']}", // $range,
					'o'   => $quote['02. open'], // $last_open,
					'h'   => $quote['03. high'], // $last_high,
					'low' => $quote['04. low'], // $last_low,
					'v'   => $quote['06. volume'], // $last_volume,
				);
				self::log( 'data_arr w/o raw JSON: ' . print_r( $data_arr, 1 ) );
				$data_arr['raw'] = $json;

			}
			unset( $response_arr );
		}

		return $data_arr;
	} // END function fetch_alphavantage_feed( $symbol )

	/**
	 * Filter out invalid stock symbols (eg. one with equals or carret sign)
	 * Allow only numbers, alphabet, dot, and semicolon
	 *
	 * @param  string $symbols Unfiltered value of stock symbols
	 *
	 * @return string          Sanitized value of stock symbols
	 */
	public static function sanitize_symbols( $symbols ) {

		// Split symbols by comma
		$symbols_arr   = explode( ',', $symbols );
		$symbols_clean = array();
		// Discard each symbol that contains equals or carret sign
		foreach ( $symbols_arr as $symbol ) {
			if ( false === strpos( $symbol, '=', 0 ) && false === strpos( $symbol, '^', 0 ) ) {
				$symbols_clean[] = preg_replace( '/[^0-9A-Z\.\:\-]+/', '', strtoupper( $symbol ) );
			}
		}
		return implode( ',', $symbols_clean );
	} // END private function sanitize_symbols( $symbols )

	private function lock_fetch() {
		update_option( 'stockticker_av_progress', true );
		return;
	}
	private function unlock_fetch() {
		update_option( 'stockticker_av_progress', false );
		return;
	}
	public static function log( $str ) {
		// Only if WP_DEBUG is enabled
		if ( defined( 'WP_DEBUG' ) && true === WP_DEBUG ) {
			$log_file = trailingslashit( WP_CONTENT_DIR ) . 'stockticker.log';
			$date     = gmdate( 'c' );
			error_log( "{$date}: {$str}\n", 3, $log_file );
		}
	}
} // END class Wpau_Stock_Ticker

if ( class_exists( 'Wpau_Stock_Ticker' ) ) {
	// Instantiate the plugin class.
	global $wpau_stockticker;
	if ( empty( $wpau_stockticker ) ) {
		$wpau_stockticker = new Wpau_Stock_Ticker();
	}
} // END class_exists( 'Wpau_Stock_Ticker' )
