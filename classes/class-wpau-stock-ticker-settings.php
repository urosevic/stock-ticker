<?php
/**
 * Stock Ticker Settings
 *
 * @category Wpau_Stock_Ticker_Settings
 * @package Stock Ticker
 * @author Aleksandar Urosevic
 * @license https://www.gnu.org/copyleft/gpl.html GNU General Public License
 * @link https://urosevic.net
 */

if ( class_exists( 'Wpau_Stock_Ticker_Settings' ) ) {
	return;
}

/**
 * Wpau_Stock_Ticker_Settings Class provide general plugins settings page
 *
 * @category Class
 * @package Stock Ticker
 * @author Aleksandar Urosevic
 * @license https://www.gnu.org/copyleft/gpl.html GNU General Public License
 * @link https://urosevic.net
 */
class Wpau_Stock_Ticker_Settings {

	public $slug;
	public $option_name;
	public $defaults;
	public $endpoints;
	/**
	 * Construct the plugin object
	 */
	public function __construct() {
		global $wpau_stockticker;

		// Get default values.
		$this->slug        = $wpau_stockticker->plugin_slug;
		$this->option_name = $wpau_stockticker->plugin_option;
		$this->defaults    = $wpau_stockticker->defaults;
		$this->endpoints   = $wpau_stockticker->endpoints;

		add_action( 'admin_init', array( &$this, 'register_settings' ) );
		add_action( 'admin_menu', array( &$this, 'add_menu' ) );
	} // END public function __construct

	/**
	 * Hook into WP's register_settings action hook
	 */
	public function register_settings() {
		global $wpau_stockticker;

		// Add settings section.
		add_settings_section(
			'wpau_stock_ticker',
			__( 'Settings', 'stock-ticker' ),
			array( &$this, 'settings_section_description' ),
			$this->slug
		);

		// Add setting's fields.
		// Add separator for General section
		add_settings_field(
			$this->option_name . 'general_section',
			__( 'General', 'stock-ticker' ),
			array( &$this, 'settings_field_section_divider' ),
			$this->slug,
			'wpau_stock_ticker',
			array(
				'description' => __( 'Predefine general settings for Stock Ticker. Here you can set API key and symbols used on the whole website (in all ticker).', 'stock-ticker' ),
			)
		);

		add_settings_field(
			$this->option_name . 'avapikey',
			__( 'AlphaVantage.co API Key', 'stock-ticker' ),
			array( &$this, 'settings_field_input_password' ),
			$this->slug,
			'wpau_stock_ticker',
			array(
				'field'       => $this->option_name . '[avapikey]',
				'description' => sprintf(
					// translators: %1$s is replaced with AlphaVantage.co, %2$s is replaced with Alpha Vantage API Key anchor
					__( 'We get stock data from 3rd party API service %1$s. %2$s if you do not owe it already, and enter it here. Please note, a free API key is limited to five API requests per minute.', 'stock-ticker' ),
					'AlphaVantage.co',
					wp_kses(
						sprintf(
							'<a href="%1$s" target="_blank">%2$s</a>',
							esc_url( 'https://www.alphavantage.co/support/#api-key' ),
							esc_attr__( 'Claim your free API Key', 'stock-ticker' )
						),
						array(
							'a' => array(
								'href'   => array(),
								'target' => array( '_blank' ),
							),
						)
					)
				),
				'class'       => 'regular-text',
				'value'       => $this->defaults['avapikey'],
			)
		);

		add_settings_field(
			$this->option_name . 'av_api_tier',
			__( 'AlphaVantage.co API Key Tier', 'stock-ticker' ),
			array( &$this, 'settings_field_select' ),
			$this->slug,
			'wpau_stock_ticker',
			array(
				'field'       => $this->option_name . '[av_api_tier]',
				'description' => sprintf(
					wp_kses(
						/*
						 * translators: %1$s is replaced with AlphaVantage.co free URL
						 * %2$s with translated keyword `Free`
						 * %3$s with link to AlphaVantage.co premium URL
						 * %4$s with trnslated keyword `Premium`
						 */
						__( 'Which Alpha Vantage API Key membership you have (<a href="%1$s" target="_blank">%2$s</a> or <a href="%3$s" target="_blank">%4$s</a>)?', 'stock-ticker' ),
						array(
							'a' => array(
								'href'   => array(),
								'target' => array( '_blank' ),
							),
						)
					),
					esc_url( 'https://www.alphavantage.co/support/#api-key' ),
					esc_attr__( 'Free', 'stock-ticker' ),
					esc_url( 'https://www.alphavantage.co/premium/' ),
					esc_attr__( 'Premium', 'stock-ticker' )
				),
				'items'       => array(
					'5'    => esc_attr__( 'Free (5 requests/min, 25 requests/day)', 'stock-ticker' ),
					'30'   => esc_attr__( 'Premium (30 requests/min)', 'stock-ticker' ),
					'75'   => esc_attr__( 'Premium (75 requests/min)', 'stock-ticker' ),
					'150'  => esc_attr__( 'Premium (150 requests/min)', 'stock-ticker' ),
					'300'  => esc_attr__( 'Premium (300 requests/min)', 'stock-ticker' ),
					'600'  => esc_attr__( 'Premium (600 requests/min)', 'stock-ticker' ),
					'1200' => esc_attr__( 'Premium (1200 requests/min)', 'stock-ticker' ),
				),
				'value'       => $this->defaults['av_api_tier'],
			)
		);

		add_settings_field(
			$this->option_name . 'all_symbols',
			__( 'All Stock Symbols', 'stock-ticker' ),
			array( &$this, 'settings_field_input_text' ),
			$this->slug,
			'wpau_stock_ticker',
			array(
				'field'       => $this->option_name . '[all_symbols]',
				'description' => esc_attr__( 'You can use some or all of those symbols in any ticker on the website. Please note, you have to define which symbols you will use per widget/shortcode. Enter stock symbols separated with a comma.', 'stock-ticker' ),
				'class'       => 'widefat',
				'value'       => $this->defaults['all_symbols'],
			)
		);
		// Symbol Search and Test
		add_settings_field(
			$this->option_name . 'symbol_search_test',
			__( 'Symbol Search & Test', 'stock-ticker' ),
			array( &$this, 'settings_js_symbolsearchtest' ),
			$this->slug,
			'wpau_stock_ticker'
		);
		// Force fetch stock
		add_settings_field(
			$this->option_name . 'force_fetch',
			__( 'Force data fetch', 'stock-ticker' ),
			array( &$this, 'settings_js_forcedatafetch' ),
			$this->slug,
			'wpau_stock_ticker'
		);

		add_settings_field(
			$this->option_name . 'loading_message',
			__( 'Loading Message', 'stock-ticker' ),
			array( &$this, 'settings_field_input_text' ),
			$this->slug,
			'wpau_stock_ticker',
			array(
				'field'       => $this->option_name . '[loading_message]',
				'description' => esc_attr__( 'Customize message displayed to visitor until plugin load stock data through AJAX.', 'stock-ticker' ),
				'class'       => 'widefat',
				'value'       => $this->defaults['loading_message'],
			)
		);
		// Default error message.
		add_settings_field(
			$this->option_name . 'error_message',
			__( 'Error Message', 'stock-ticker' ),
			array( &$this, 'settings_field_input_text' ),
			$this->slug,
			'wpau_stock_ticker',
			array(
				'field'       => $this->option_name . '[error_message]',
				'description' => esc_attr__( 'When we do not have pre-fetched from AlphaVantage.co stock data for requested symbols, display this message in the ticker', 'stock-ticker' ),
				'class'       => 'widefat',
				'value'       => $this->defaults['error_message'],
			)
		);

		// Add setting's fields.
		// Add separator for Defaults section
		add_settings_field(
			$this->option_name . 'default_section',
			__( 'Defaults', 'stock-ticker' ),
			array( &$this, 'settings_field_section_divider' ),
			$this->slug,
			'wpau_stock_ticker',
			array(
				'description' => sprintf(
					// translators: %s is replaced with translated plugin name
					esc_attr__( 'Predefine default settings for %s. Here you can set stock symbols and how you wish to present companies in ticker.', 'stock-ticker' ),
					esc_attr__( 'Stock Ticker', 'stock-ticker' )
				),
			)
		);

		// Add setting's fields.
		add_settings_field(
			$this->option_name . 'symbols',
			__( 'Stock Symbols', 'stock-ticker' ),
			array( &$this, 'settings_field_input_text' ),
			$this->slug,
			'wpau_stock_ticker',
			array(
				'field'       => $this->option_name . '[symbols]',
				'description' => esc_attr__( 'Those simbols are used as default for shortcodes w/o provided symbols, but not for widgets as widget have own symbols setting. Enter stock symbols separated with comma.', 'stock-ticker' ),
				'class'       => 'widefat',
				'value'       => $this->defaults['symbols'],
			)
		);
		add_settings_field(
			$this->option_name . 'show',
			__( 'Show Company as', 'stock-ticker' ),
			array( &$this, 'settings_field_select' ),
			$this->slug,
			'wpau_stock_ticker',
			array(
				'field'       => $this->option_name . '[show]',
				'description' => sprintf(
					// translators: %s is replaced with untranslatable keyword `show`
					esc_attr__( 'What to show as Company identifier by default for shortcodes if not provided shortcode parameter %s. The widget has its setting for this.', 'stock-ticker' ),
					"'show'"
				),
				'items'       => array(
					'name'   => esc_attr__( 'Company Name', 'stock-ticker' ),
					'symbol' => esc_attr__( 'Stock Symbol', 'stock-ticker' ),
				),
				'value'       => $this->defaults['show'],
			)
		);

		add_settings_field(
			$this->option_name . 'number_format',
			__( 'Number format', 'stock-ticker' ),
			array( &$this, 'settings_field_select' ),
			$this->slug,
			'wpau_stock_ticker',
			array(
				'field'       => $this->option_name . '[number_format]',
				'description' => __( 'Select default number format', 'stock-ticker' ),
				'items'       => array(
					'cd' => '0,000.00',
					'dc' => '0.000,00', // default
					'sd' => '0 000.00',
					'sc' => '0 000,00',
				),
				'value'       => $this->defaults['number_format'],
				'class'       => 'regular-text',
			)
		);
		add_settings_field(
			$this->option_name . 'decimals',
			__( 'Decimal places', 'stock-ticker' ),
			array( &$this, 'settings_field_select' ),
			$this->slug,
			'wpau_stock_ticker',
			array(
				'field'       => $this->option_name . '[decimals]',
				'description' => __( 'Select amount of decimal places for numbers', 'stock-ticker' ),
				'items'       => array(
					'1' => __( 'One', 'stock-ticker' ),
					'2' => __( 'Two', 'stock-ticker' ),
					'3' => __( 'Three', 'stock-ticker' ),
					'4' => __( 'Four', 'stock-ticker' ),
				),
				'value'       => $this->defaults['decimals'],
				'class'       => 'regular-text',
			)
		);
		// Color pickers.
		// Unchanged.
		add_settings_field(
			$this->option_name . 'quote_zero',
			__( 'Unchanged Quote', 'stock-ticker' ),
			array( &$this, 'settings_field_colour_picker' ),
			$this->slug,
			'wpau_stock_ticker',
			array(
				'field'       => $this->option_name . '[zero]',
				'description' => __( 'Set colour for unchanged quote', 'stock-ticker' ),
				'value'       => $this->defaults['zero'],
			)
		);
		// Minus.
		add_settings_field(
			$this->option_name . 'quote_minus',
			__( 'Negative Change', 'stock-ticker' ),
			array( &$this, 'settings_field_colour_picker' ),
			$this->slug,
			'wpau_stock_ticker',
			array(
				'field'       => $this->option_name . '[minus]',
				'description' => __( 'Set colour for negative change', 'stock-ticker' ),
				'value'       => $this->defaults['minus'],
			)
		);
		// Plus.
		add_settings_field(
			$this->option_name . 'quote_plus',
			__( 'Positive Change', 'stock-ticker' ),
			array( &$this, 'settings_field_colour_picker' ),
			$this->slug,
			'wpau_stock_ticker',
			array(
				'field'       => $this->option_name . '[plus]',
				'description' => __( 'Set colour for positive change', 'stock-ticker' ),
				'value'       => $this->defaults['plus'],
			)
		);

		// Add setting's fields.
		// Add separator for Advanced section
		add_settings_field(
			$this->option_name . 'advanced_section',
			__( 'Advanced', 'stock-ticker' ),
			array( &$this, 'settings_field_section_divider' ),
			$this->slug,
			'wpau_stock_ticker',
			array(
				'description' => __( 'Set advanced options important for caching quote feeds.', 'stock-ticker' ),
			)
		);

		// Ticker speed.
		add_settings_field(
			$this->option_name . 'speed',
			__( 'Ticker Speed', 'stock-ticker' ),
			array( &$this, 'settings_field_input_number' ),
			$this->slug,
			'wpau_stock_ticker',
			array(
				'field'       => $this->option_name . '[speed]',
				'description' => __( 'Define speed of ticker scrolling in pixels per second (default is 50)', 'stock-ticker' ),
				'class'       => 'num small-text',
				'value'       => isset( $this->defaults['speed'] ) ? $this->defaults['speed'] : 50,
				'min'         => 10,
				'max'         => 200,
				'step'        => 1,
			)
		);
		// Default ticker item template.
		add_settings_field(
			$this->option_name . 'template',
			__( 'Item Template', 'stock-ticker' ),
			array( &$this, 'settings_field_textarea' ),
			$this->slug,
			'wpau_stock_ticker',
			array(
				'field'       => $this->option_name . '[template]',
				'description' => sprintf(
					// translators: %1$s and %2$s are replaved with untranslatable template placeholders, %2$s is replaved with available HTML tags
					__( 'Custom template for item. You can use macro keywords %1$s or %2$s mixed with HTML tags %3$s and/or %4$s.', 'stock-ticker' ),
					'%exch_symbol%, %symbol%, %company%, %price%, %volume%, %change%, %changep%, %ltrade%',
					'%ltrade|l, jS \of F Y%',
					'&lt;span&gt;, &lt;em&gt;',
					'&lt;strong&gt;'
				),
				'class'       => 'widefat',
				'rows'        => 2,
				'value'       => $this->defaults['template'],
			)
		);
		// Custom name.
		add_settings_field(
			$this->option_name . 'legend',
			__( 'Custom Names', 'stock-ticker' ),
			array( &$this, 'settings_field_textarea' ),
			$this->slug,
			'wpau_stock_ticker',
			array(
				'field'       => $this->option_name . '[legend]',
				'class'       => 'widefat',
				'value'       => $this->defaults['legend'],
				'rows'        => 7,
				'description' => __(
					'Define custom names for symbols. Single symbol per row in format EXCHANGE:SYMBOL;CUSTOM_NAME',
					'stock-ticker'
				),
			)
		);

		// Caching timeout field.
		add_settings_field(
			$this->option_name . 'cache_timeout',
			__( 'Cache Timeout', 'stock-ticker' ),
			array( &$this, 'settings_field_input_number' ),
			$this->slug,
			'wpau_stock_ticker',
			array(
				'field'       => $this->option_name . '[cache_timeout]',
				'description' => __( 'Define timeout before next round of fetching symbols data start, in seconds. Make sure to set this value to (at least) number of **All Stock Symbols** × **Fetch Timeout** + **50%** (if you have 10 symbols and 10 second fetch timeout, set to at least 150 seconds). Please note, Alpha Vantage update quotes on 15 minutes!', 'stock-ticker' ),
				'class'       => 'num small-text',
				'value'       => isset( $this->defaults['cache_timeout'] ) ? $this->defaults['cache_timeout'] : 180,
				'min'         => 0,
				'max'         => DAY_IN_SECONDS,
				'step'        => 5,
			)
		);
		// Fetch timeout field.
		add_settings_field(
			$this->option_name . 'timeout',
			__( 'Fetch Timeout', 'stock-ticker' ),
			array( &$this, 'settings_field_input_number' ),
			$this->slug,
			'wpau_stock_ticker',
			array(
				'field'       => $this->option_name . '[timeout]',
				'description' => __( 'Define timeout to fetch quote feed before give up and display error message, in seconds (default is 2)', 'stock-ticker' ),
				'class'       => 'num small-text',
				'value'       => isset( $this->defaults['timeout'] ) ? $this->defaults['timeout'] : 2,
				'min'         => 1,
				'max'         => 60,
				'step'        => 1,
			)
		);

		// Default styling.
		add_settings_field(
			$this->option_name . 'style',
			__( 'Custom Style', 'stock-ticker' ),
			array( &$this, 'settings_field_textarea' ),
			$this->slug,
			'wpau_stock_ticker',
			array(
				'field'       => $this->option_name . '[style]',
				'class'       => 'widefat',
				'rows'        => 2,
				'value'       => $this->defaults['style'],
				'description' => __( 'Define custom CSS style for ticker item (font family, size, weight)', 'stock-ticker' ),
			)
		);

		// Reload checkbox.
		add_settings_field(
			$this->option_name . 'reload',
			__( 'Auto Reload', 'stock-ticker' ),
			array( &$this, 'settings_field_checkbox' ),
			$this->slug,
			'wpau_stock_ticker',
			array(
				'field'       => $this->option_name . '[reload]',
				'description' => __( 'Enable this option to automatically reload all stock tickers on the page without reloading page manually.', 'stock-ticker' ),
				'class'       => 'checkbox',
				'value'       => isset( $this->defaults['reload'] ) ? $this->defaults['reload'] : false,
			) // args
		);

		// Reload Timeout field.
		add_settings_field(
			$this->option_name . 'reload_timeout',
			__( 'Auto Reload Timeout', 'stock-ticker' ),
			array( &$this, 'settings_field_input_number' ),
			$this->slug,
			'wpau_stock_ticker',
			array(
				'field'       => $this->option_name . '[reload_timeout]',
				'description' => esc_attr__( 'Define auto-reload timeout, in seconds. It is related to reloading ticker on a webpage without refreshing page, and does not affect how often plugin update data from AlphaVantage API!', 'stock-ticker' ),
				'class'       => 'num small-text',
				'value'       => isset( $this->defaults['reload_timeout'] ) ? $this->defaults['reload_timeout'] : 5 * MINUTE_IN_SECONDS,
				'min'         => 0,
				'max'         => HOUR_IN_SECONDS,
				'step'        => 5,
			)
		);

		// Global enqueue assets.
		add_settings_field(
			$this->option_name . 'globalassets',
			__( 'Load assets on all pages?', 'stock-ticker' ),
			array( &$this, 'settings_field_checkbox' ),
			$this->slug,
			'wpau_stock_ticker',
			array(
				'field'       => $this->option_name . '[globalassets]',
				'description' => esc_attr__( 'By default, Stock Ticker will load corresponding JavaScript files on demand. But, if you need to load assets on all pages, check this option. (For example, if you have some plugin that loads widgets or content via Ajax, you should enable this option)', 'stock-ticker' ),
				'class'       => 'checkbox',
				'value'       => isset( $this->defaults['globalassets'] ) ? $this->defaults['globalassets'] : false,
			) // args
		);

		// --- Register setting so $_POST handling is done ---
		register_setting(
			'wpau_stock_ticker',
			$this->option_name,
			array( &$this, 'sanitize_options' )
		);
	} // END public static function register_settings()

	public function settings_js_forcedatafetch() {
		?>
		<p class="description">
			<?php esc_attr_e( 'After you update settings, you can force initial stock data fetching by click on button below.', 'stock-ticker' ); ?>
			<br />
			<?php
			printf(
				// translators: %1$s is replaved with untranslatable label `Timeout`, %2$s is replaced with translated `Fetch Timeout`
				esc_attr__( 'If you get too much %1$s statuses during fetch, try to increase %2$s option, save settings and fetch data again.', 'stock-ticker' ),
				'<code>[Timeout]</code>',
				'<strong>' . __( 'Fetch Timeout', 'stock-ticker' ) . '</strong>'
			);
			?>
			<br />
		If you get any <code>[Invalid API call]</code> or <code>[Bad API response]</code> for same symbol multiple times, then AlphaVantage.co does not have that symbol for GLOBAL_QUOTE scope. In that case you should try to prepend stock exchange to symbol, or remove faulty symbol from <strong>All Stock Symbols</strong>.</p>
		<p class="fieldset-flex">
			<button name="st_force_data_fetch" class="button button-primary">Fetch Stock Data Now!</button> <button name="st_force_data_fetch_stop" class="button button-secondary">Stop Fetch</button>
		</p>
		<div class="st_force_data_fetch"></div>
		<?php
	}

	public function settings_js_symbolsearchtest() {
		?>
		<p class="description">
			<?php
			printf(
				// translators: %s is replaved with translated `Test` buton label
				esc_attr__( 'If you do not know which symbol to use, enter keywords or symbol to field below and %s.', 'stock-ticker' ),
				'<strong>' . __( 'Test', 'stock-ticker' ) . '</strong>'
			);
			?>
			<br />
			<?php
			printf(
				// translators: %1$s is replaved with API domain, %2$s and %2$s with untranslatable API endpoints
				esc_attr__( 'Please note, even if %1$s return symbol for %2$s, that does not guarantee they will also provide any stock data for %3$s.', 'stock-ticker' ),
				'AlphaVantage.co',
				'<code>SYMBOL_SEARCH</code>',
				'<code>GLOBAL_QUOTE</code>'
			);
			?>
			<br />
			<?php
			printf(
				// translators: %s is replaced with plugin name
				esc_attr__( 'Other API endpoints are there only for testing purposes, but they are not used in %s.', 'stock-ticker' ),
				__( 'Stock Ticker', 'stock-ticker' )
			);
			?>
		</p>
		<p class="fieldset-flex">
			<input type="text" name="st_symbol_search_test" class="regular-text" placeholder="Enter keyword or symbol..." />
			<select name="st_symbol_search_test_endpoint" class="regular-text">
				<option value="">
					<?php
					// translators: %s is replaced with API RUL
					printf( esc_html__( 'Please select %s endpoint', 'stock-ticker' ), 'AlphaVantage.co API' );
					?>
				</option>
				<?php
				foreach ( $this->endpoints as $endpoint ) {
					printf( '<option value="%1$s">%1$s</option>', $endpoint );
				}
				?>
			</select>
			<button name="st_symbol_search_test_button" class="button button-primary">Test</button>
		</p>
		<div class="st_symbol_search_test_log"></div>
		<?php
	}

	/**
	 * Print description for General section
	 */
	public function settings_section_description() {
		// Think of this as help text for the section.
		return;
	}

	/**
	 * Print divider for section
	 * @return [type] [description]
	 */
	public function settings_field_section_divider( $args ) {
		echo '<hr />';
		if ( ! empty( $args['description'] ) ) {
			printf(
				'<p class="description">%s</p><hr />',
				$args['description']
			);
		}
	}

	/**
	 * This function provides text inputs for settings fields
	 * @param  array $args Array of field arguments.
	 */
	public function settings_field_input_text( $args ) {
		printf(
			'<input type="text" name="%s" id="%s" value="%s" class="%s" data-lpignore="true" /><p class="description">%s</p>',
			esc_attr( $args['field'] ),
			esc_attr( $args['field'] ),
			esc_attr( $args['value'] ),
			sanitize_html_class( $args['class'] ),
			esc_html( $args['description'] )
		);
	} // END public function settings_field_input_text($args)

	/**
	 * This function provides password inputs for settings fields
	 * @param  array $args Array of field arguments.
	 */
	public function settings_field_input_password( $args ) {
		printf(
			'<input type="password" name="%s" id="%s" value="%s" class="%s" data-lpignore="true" /><p class="description">%s</p>',
			esc_attr( $args['field'] ),
			esc_attr( $args['field'] ),
			esc_attr( $args['value'] ),
			sanitize_html_class( $args['class'] ),
			$args['description']
		);
	} // END public function settings_field_input_text($args)

	/**
	 * This function provides number inputs for settings fields
	 * @param  array $args Array of field arguments.
	 */
	public function settings_field_input_number( $args ) {
		$args['description'] = self::format_description( esc_html( $args['description'] ) );
		printf(
			'<input type="number" name="%1$s" id="%2$s" value="%3$s" min="%4$s" max="%5$s" step="%6$s" class="%7$s" /><p class="description">%8$s</p>',
			esc_attr( $args['field'] ),            // 1
			esc_attr( $args['field'] ),            // 2
			(int) $args['value'],                  // 3
			(int) $args['min'],                    // 4
			(int) $args['max'],                    // 5
			(int) $args['step'],                   // 6
			sanitize_html_class( $args['class'] ), // 7
			$args['description']                   // 8
		);
	} // END public function settings_field_input_number($args)

	/**
	 * This function provides textarea for settings fields
	 * @param  array $args Array of field arguments.
	 */
	public function settings_field_textarea( $args ) {
		if ( empty( $args['rows'] ) ) {
			$args['rows'] = 7;
		}
		printf(
			'<textarea name="%s" id="%s" rows="%s" class="%s">%s</textarea><p class="description">%s</p>',
			esc_attr( $args['field'] ),
			esc_attr( $args['field'] ),
			(int) $args['rows'],
			sanitize_html_class( $args['class'] ),
			esc_textarea( $args['value'] ),
			esc_html( $args['description'] )
		);
	} // END public function settings_field_textarea($args)

	/**
	 * This function provides select for settings fields
	 * @param  array $args Array of field arguments.
	 */
	public function settings_field_select( $args ) {
		if ( empty( $args['class'] ) ) {
			$args['class'] = 'regular-text';
		}
		printf(
			'<select id="%1$s" name="%1$s" class="%2$s">',
			esc_attr( $args['field'] ),
			sanitize_html_class( $args['class'] )
		);
		foreach ( $args['items'] as $key => $val ) {
			$selected = ( strval( $args['value'] ) === strval( $key ) ) ? 'selected=selected' : '';
			printf(
				'<option %1$s value="%2$s">%3$s</option>',
				esc_attr( $selected ),      // 1
				sanitize_key( $key ),       // 2
				sanitize_text_field( $val ) // 3
			);
		}
		printf(
			'</select><p class="description">%s</p>',
			wp_kses(
				$args['description'],
				array(
					'a' => array(
						'href'   => array(),
						'target' => array( '_blank' ),
					),
					'strong',
					'em',
					'pre',
					'code',
				)
			)
		);
	} // END public function settings_field_select($args)

	/**
	 * This function provides checkbox for settings fields
	 * @param  array $args Array of field arguments.
	 */
	public function settings_field_checkbox( $args ) {
		$checked = ( ! empty( $args['value'] ) ) ? 'checked="checked"' : '';
		printf(
			'<label for="%1$s"><input type="checkbox" name="%1$s" id="%1$s" value="1" class="%2$s" %3$s />%4$s</label>',
			esc_attr( $args['field'] ),
			$args['class'],
			$checked,
			self::format_description( $args['description'] )
		);
	} // END public function settings_field_checkbox($args) {

	/**
	 * Generate colour picker field
	 * @param  array $args Array of field arguments.
	 */
	public function settings_field_colour_picker( $args ) {
		printf(
			'<input type="text" name="%1$s" id="%2$s" value="%3$s" class="wpau-color-field" /> <p class="description">%4$s</p>',
			esc_attr( $args['field'] ),
			esc_attr( $args['field'] ),
			esc_attr( $args['value'] ),
			esc_html( $args['description'] )
		);
	} // END public function settings_field_colour_picker($args)

	/**
	 * Basic markdown formatter for descriptions
	 * @param  string $text Raw ASCII text
	 * @return string       HTML formatted text
	 */
	public function format_description( $text ) {
		$pattern     = '/(\*\*)([^\*]+)(\*\*)/';
		$replacement = '<strong>${2}</strong>';
		return preg_replace( $pattern, $replacement, $text );
	} // END function format_description( $text )

	/**
	 * Sanitize settings options
	 * @param  array $input Array of option values entered on settings page.
	 * @return array        Sanitized settings values
	 */
	public function sanitize_options( $options ) {

		$sanitized        = get_option( $this->option_name );
		$previous_options = $sanitized;

		// If there is no POST option_page keyword, return initial plugin options
		if ( empty( $_POST['option_page'] ) ) {
			return $sanitized;
		}
		foreach ( $options as $key => $value ) {
			switch ( $key ) {
				case 'avapikey':
					// Allow only numbers (0-9) and English uppercase letters (A-Z)
					$value = preg_replace( '/[^0-9A-Z]+/', '', $value );
					break;
				case 'av_api_tier':
					if ( ! in_array( (int) $value, array( 5, 30, 75, 150, 300, 600, 1200 ), true ) ) {
						$value = 5;
					}
					break;
				case 'symbols':
					// Always uppercase
					$value = Wpau_Stock_Ticker::sanitize_symbols( $value );
					$value = self::alpha_symbols( $value, 'symbols' );
					break;
				case 'all_symbols':
					// Always uppercase
					$value = Wpau_Stock_Ticker::sanitize_symbols( $value );
					$value = self::alpha_symbols( $value, 'all_symbols' );
					// Add error if there is not supported exchanges
					break;
				case 'legend':
				case 'loading_message':
				case 'error_message':
				case 'style':
					$value = strip_tags( stripslashes( $value ) );
					break;
				case 'zero':
				case 'minus':
				case 'plus':
					$value = preg_replace( '/\#[^0-9a-f]/i', '', $value );
					break;
				case 'show':
					$value = strip_tags( stripslashes( $value ) );
					if ( ! in_array( $value, array( 'name', 'symbol' ), true ) ) {
						$value = 'name';
					}
					break;
				case 'template':
					$value = strip_tags( $value, '<span><em><strong>' );
					break;
				case 'cache_timeout':
					$value = (int) $value;
					$value = ! empty( $value ) ? $value : 180;
					break;
				case 'fetch_timeout':
				case 'timeout':
					$value = (int) $value;
					$value = ! empty( $value ) ? $value : 2;
					break;
				case 'reload_timeout':
					$value = (int) $value;
					$value = ! empty( $value ) ? $value : 5 * MINUTE_IN_SECONDS;
					break;
				case 'speed':
					$value = (int) $value;
					$value = ! empty( $value ) ? $value : 50;
					break;
				case 'decimals':
					$value = (int) $value;
					$value = ! empty( $value ) ? $value : 2;
					break;
				case 'number_format':
					$value = strip_tags( stripslashes( $value ) );
					if ( ! in_array( $value, array( 'dc', 'sd', 'sc', 'cd' ), true ) ) {
						$value = 'dc'; // dot comma
					}
					break;
				// Checkboxes
				case 'reload':
				case 'globalassets':
					$value = true;
					break;
			}
			$sanitized[ $key ] = $value;
		}

		// Sanitize checkboxes
		$checkboxes = array( 'reload', 'globalassets' );
		foreach ( $checkboxes as $checkbox_name ) {
			if ( empty( $options[ $checkbox_name ] ) ) {
				$sanitized[ $checkbox_name ] = false;
			}
		}

		// Generate static CSS
		$css  = "ul.stock_ticker li .sqitem{{$sanitized['style']}}";
		$css .= "ul.stock_ticker li.zero .sqitem,ul.stock_ticker li.zero .sqitem:hover {color:{$sanitized['zero']}}";
		$css .= "ul.stock_ticker li.minus .sqitem,ul.stock_ticker li.minus .sqitem:hover {color:{$sanitized['minus']}}";
		$css .= "ul.stock_ticker li.plus .sqitem,ul.stock_ticker li.plus .sqitem:hover {color:{$sanitized['plus']}}";

		// Now write content to file
		$upload_dir = wp_upload_dir();
		if ( ! file_put_contents( $upload_dir['basedir'] . '/stock-ticker-custom.css', $css, LOCK_EX ) ) {
			$error = error_get_last();
			add_settings_error(
				'stock-ticker-update',
				esc_attr( 'stock-ticker-custom-css' ),
				sprintf(
					// translators: %1$s is replaced with error message, %2$s is replaced with CSS code
					__( 'Failed to write custom CSS file because of error <em>%1$s</em><br />Please create mentioned file manually and add following code to it:<br /><code>%2$s</code>', 'stock-ticker' ),
					$error['message'],
					$css
				),
				'error'
			);
		}
		unset( $css );

		// Generate static reload JS
		if ( ! empty( $sanitized['reload'] ) ) {
			$js = sprintf( 'var stockTickers = setInterval(function(){ stocktickers_load() }, %s);', $sanitized['reload_timeout'] * 1000 );
			if ( ! file_put_contents( $upload_dir['basedir'] . '/stock-ticker-reload.js', $js, LOCK_EX ) ) {
				$error = error_get_last();
				add_settings_error(
					'stock-ticker-update',
					esc_attr( 'stock-ticker-custom-js' ),
					sprintf(
						// translators: %1$s is replaced with error message, %2$s is replaced with JavaScript code
						__( 'Failed to write custom JS file for auto reloading ticker because of error <em>%1$s</em><br />Please create mentioned file manually and add following code to it:<br /><code>%2$s</code>', 'stock-ticker' ),
						$error['message'],
						$js
					),
					'error'
				);
			}
			unset( $js );
		}

		// Clear transient but only if changed one of:
		// API key, All Stock Symbols, Cache Timeout or Fetch Timeout
		// @TODO remove cache_timeout
		if (
			$previous_options['avapikey'] !== $sanitized['avapikey'] ||
			$previous_options['all_symbols'] !== $sanitized['all_symbols'] ||
			$previous_options['cache_timeout'] !== $sanitized['cache_timeout'] ||
			$previous_options['timeout'] !== $sanitized['timeout']
		) {
			Wpau_Stock_Ticker::log( 'Stock Ticker: Restarting data fetching from first symbol' );
			Wpau_Stock_Ticker::restart_av_fetching();
		}

		Wpau_Stock_Ticker::log( 'Stock Ticker: Settings have been updated' );
		return $sanitized;
	} // END public function sanitize_options($sanitized) {

	/**
	 * Add a menu
	 */
	public function add_menu() {
		global $wpau_stockticker;
		// Add a page to manage this plugin's settings.
		add_options_page(
			__( 'Stock Ticker', 'stock-ticker' ),
			__( 'Stock Ticker', 'stock-ticker' ),
			'manage_options',
			$this->slug,
			array( &$this, 'plugin_settings_page' )
		);
	} // END public function add_menu()

	/**
	 * Menu Callback
	 */
	public function plugin_settings_page() {
		if ( ! current_user_can( 'manage_options' ) ) {
			wp_die( __( 'You do not have sufficient permissions to access this page.' ) );
		}

		// Render the settings template.
		include sprintf( '%s/../templates/settings.php', __DIR__ );
	} // END public function plugin_settings_page()

	/**
	 * Strip unsupported stock symbols and throw message with list of removed symbols
	 * @param  string $symbols All stock symbols
	 * @param  string $control Name of field where symbols goes
	 * @return string          Only symbols supported by AlphaVantage.co
	 */
	private function alpha_symbols( $symbols, $control ) {
		$symbols_supported = array();
		$symbols_removed   = array();
		$symbols_arr       = explode( ',', $symbols );
		// Remove unsupported stock exchanges from global array to prevent API errors
		foreach ( $symbols_arr as $symbol_pos => $symbol_to_check ) {
			$symbol_to_check = trim( $symbol_to_check );
			// If there is semicolon, it's symbol with exchange
			if ( strpos( $symbol_to_check, ':' ) ) {
				// Explode symbol so we can get exchange code
				$symbol_exchange = explode( ':', $symbol_to_check );
				// If exchange code is supported, add symbol to query array
				if ( ! empty( Wpau_Stock_Ticker::$exchanges['supported'][ strtoupper( trim( $symbol_exchange[0] ) ) ] ) ) {
					$symbols_supported[] = $symbol_to_check;
				} else {
					$symbols_removed[] = $symbol_to_check;
				}
			} elseif ( ! empty( $symbol_to_check ) ) {
				// Add symbol w/o exchange to query array
				$symbols_supported[] = $symbol_to_check;
			}
		}
		// Remove duplicate symbols
		$symbols_supported = array_unique( $symbols_supported );
		// Set back supported symbols
		$symbols = implode( ',', $symbols_supported );
		// If we have removed symbols, add settings error message
		if ( ! empty( $symbols_removed ) ) {
			$symbols_removed_str = implode( ', ', $symbols_removed );
			$opt_name            = 'all_symbols' === $control ? 'All Stock Symbols' : 'Stock Symbols';
			add_settings_error(
				$control,
				$control,
				sprintf(
					'Field %1$s had symbols from unsupported exchange markets, so we removed them: %2$s',
					$opt_name,
					$symbols_removed_str
				),
				'updated'
			);
		}
		return $symbols;
	} // END private function alpha_symbols( $symbols, $control )
} // END class Wpau_Stock_Ticker_Settings
