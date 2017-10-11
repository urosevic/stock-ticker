<?php
/**
 * Stock Ticker General Settings
 *
 * @category Wpau_Stock_Ticker_Settings
 * @package Stock Ticker
 * @author Aleksandar Urosevic
 * @license https://www.gnu.org/copyleft/gpl.html GNU General Public License
 * @link https://urosevic.net
 */

if ( ! class_exists( 'Wpau_Stock_Ticker_Settings' ) ) {

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
		/**
		 * Construct the plugin object
		 */
		public function __construct() {
			// Register actions.
			add_action( 'admin_init', array( &$this, 'admin_init' ) );
			add_action( 'admin_menu', array( &$this, 'add_menu' ) );
		} // END public function __construct

		/**
		 * Hook into WP's admin_init action hook
		 */
		public function admin_init() {
			// Get default values.
			$defaults = Wpau_Stock_Ticker::defaults();

			// Register plugin's settings.
			register_setting( 'wpaust_default', 'stock_ticker_defaults', array( &$this, 'stock_ticker_sanitize' ) );
			register_setting( 'wpaust_advanced', 'stock_ticker_defaults', array( &$this, 'stock_ticker_sanitize' ) );

			// Add general settings section.
			add_settings_section(
				'wpaust_default',
				__( 'Default Settings', 'wpaust' ),
				array( &$this, 'settings_default_section_description' ),
				'wpau_stock_ticker'
			);

			// Add setting's fields.
			add_settings_field(
				'wpau_stock_ticker-symbols',
				__( 'Stock Symbols', 'wpaust' ),
				array( &$this, 'settings_field_input_text' ),
				'wpau_stock_ticker',
				'wpaust_default',
				array(
					'field'       => 'stock_ticker_defaults[symbols]',
					'description' => __( 'Enter stock symbols separated with comma. This simbols are used as default for shortcodes w/o provided symbols, but not for widgets as widget have own symbold setting.', 'wpaust' ),
					'class'       => 'widefat',
					'value'       => $defaults['symbols'],
				)
			);
			add_settings_field(
				'wpau_stock_ticker-show',
				__( 'Show Company as', 'wpaust' ),
				array( &$this, 'settings_field_select' ),
				'wpau_stock_ticker',
				'wpaust_default',
				array(
					'field'       => 'stock_ticker_defaults[show]',
					'description' => sprintf(
						__( 'What to show as Company identifier by default for shortcodes if not provided shortcode parameter %s. Widget have own setting for this.', 'wpaust' ),
						"'show'"
					),
					'items'       => array(
						'name'   => __( 'Company Name', 'wpaust' ),
						'symbol' => __( 'Stock Symbol', 'wpaust' ),
					),
					'value' => $defaults['show'],
				)
			);
			// Color pickers.
			// Unchanged.
			add_settings_field(
				'wpau_stock_ticker-quote_zero',
				__( 'Unchanged Quote', 'wpaust' ),
				array( &$this, 'settings_field_colour_picker' ),
				'wpau_stock_ticker',
				'wpaust_default',
				array(
					'field'       => 'stock_ticker_defaults[zero]',
					'description' => __( 'Set colour for unchanged quote', 'wpaust' ),
					'value'       => $defaults['zero'],
				)
			);
			// Minus.
			add_settings_field(
				'wpau_stock_ticker-quote_minus',
				__( 'Netagive Change', 'wpaust' ),
				array( &$this, 'settings_field_colour_picker' ),
				'wpau_stock_ticker',
				'wpaust_default',
				array(
					'field'       => 'stock_ticker_defaults[minus]',
					'description' => __( 'Set colour for negative change', 'wpaust' ),
					'value'       => $defaults['minus'],
				)
			);
			// Plus.
			add_settings_field(
				'wpau_stock_ticker-quote_plus',
				__( 'Positive Change', 'wpaust' ),
				array( &$this, 'settings_field_colour_picker' ),
				'wpau_stock_ticker',
				'wpaust_default',
				array(
					'field'       => 'stock_ticker_defaults[plus]',
					'description' => __( 'Set colour for positive change', 'wpaust' ),
					'value'       => $defaults['plus'],
				)
			);

			// Add advanced settings section.
			add_settings_section(
				'wpaust_advanced',
				__( 'Advanced Settings', 'wpaust' ),
				array( &$this, 'settings_advanced_section_description' ),
				'wpau_stock_ticker'
			);
			// Add setting's fields.
			// Ticker speed.
			add_settings_field(
				'wpau_stock_ticker-speed',
				__( 'Ticker Speed', 'wpaust' ),
				array( &$this, 'settings_field_input_number' ),
				'wpau_stock_ticker',
				'wpaust_advanced',
				array(
					'field'       => 'stock_ticker_defaults[speed]',
					'description' => __( 'Define speed of ticker scrolling in pixels per second (default is 50)', 'wpaust' ),
					'class'       => 'num',
					'value'       => isset( $defaults['speed'] ) ? $defaults['speed'] : 50,
					'min'         => 10,
					'max'         => 200,
					'step'        => 1,
				)
			);
			// Default ticker item template.
			add_settings_field(
				'wpau_stock_ticker-template',
				__( 'Item Template', 'wpaust' ),
				array( &$this, 'settings_field_textarea' ),
				'wpau_stock_ticker',
				'wpaust_advanced',
				array(
					'field'       => 'stock_ticker_defaults[template]',
					'description' => sprintf(
						__( 'Custom template for item. You can use macro keywords %1$s and %2$s mixed with HTML tags %3$s and/or %4$s.', 'wpaust' ),
						'%exch_symbol%, %symbol%, %company%, %price%, %change%',
						'%changep%',
						'&lt;span&gt;, &lt;em&gt;',
						'&lt;strong&gt;'
					),
					'class' => 'widefat',
					'rows'  => 2,
					'value' => $defaults['template'],
				)
			);
			// Custom name.
			add_settings_field(
				'wpau_stock_ticker-legend',
				__( 'Custom Names', 'wpaust' ),
				array( &$this, 'settings_field_textarea' ),
				'wpau_stock_ticker',
				'wpaust_advanced',
				array(
					'field'       => 'stock_ticker_defaults[legend]',
					'class'       => 'widefat',
					'value'       => $defaults['legend'],
					'rows'        => 7,
					'description' => __(
						'Define custom names for symbols. Single symbol per row in format EXCHANGE:SYMBOL;CUSTOM_NAME',
						'wpaust'
					),
				)
			);
			// Caching timeout field.
			add_settings_field(
				'wpau_stock_ticker-cache_timeout',
				__( 'Cache Timeout', 'wpaust' ),
				array( &$this, 'settings_field_input_number' ),
				'wpau_stock_ticker',
				'wpaust_advanced',
				array(
					'field'       => 'stock_ticker_defaults[cache_timeout]',
					'description' => __( 'Define cache timeout for single quote set, in seconds', 'wpaust' ),
					'class'       => 'num',
					'value'       => isset( $defaults['cache_timeout'] ) ? $defaults['cache_timeout'] : 180,
					'min'         => 0,
					'max'         => DAY_IN_SECONDS,
					'step'        => 5,
				)
			);
			// Fetch timeout field.
			add_settings_field(
				'wpau_stock_ticker-timeout',
				__( 'Fetch Timeout', 'wpaust' ),
				array( &$this, 'settings_field_input_number' ),
				'wpau_stock_ticker',
				'wpaust_advanced',
				array(
					'field'       => 'stock_ticker_defaults[timeout]',
					'description' => __( 'Define timeout to fetch quote feed before give up and display error message, in seconds (default is 2)', 'wpaust' ),
					'class'       => 'num',
					'value'       => isset( $defaults['timeout'] ) ? $defaults['timeout'] : 2,
					'min'         => 1,
					'max'         => 60,
					'step'        => 1,
				)
			);
			// Default error message.
			add_settings_field(
				'wpau_stock_ticker-error_message',
				__( 'Error Message', 'wpaust' ),
				array( &$this, 'settings_field_input_text' ),
				'wpau_stock_ticker',
				'wpaust_advanced',
				array(
					'field'       => 'stock_ticker_defaults[error_message]',
					'description' => __(
						'When Stock Ticker fail to grab quote set from Google Finance, display this message in ticker',
						'wpaust'
					),
					'class'       => 'widefat',
					'value'       => $defaults['error_message'],
				)
			);

			// Default styling.
			add_settings_field(
				'wpau_stock_ticker-style',
				__( 'Custom Style', 'wpaust' ),
				array( &$this, 'settings_field_textarea' ),
				'wpau_stock_ticker',
				'wpaust_advanced',
				array(
					'field'       => 'stock_ticker_defaults[style]',
					'class'       => 'widefat',
					'rows'        => 2,
					'value'       => $defaults['style'],
					'description' => __( 'Define custom CSS style for ticker item (font family, size, weight)', 'wpaust' ),
				)
			);

			// Refresh checkbox.
			add_settings_field(
				'wpau_stock_ticker-refresh',
				__( 'Auto Refresh', 'wpaust' ),
				array( &$this, 'settings_field_checkbox' ),
				'wpau_stock_ticker',
				'wpaust_advanced',
				array(
					'field'       => 'stock_ticker_defaults[refresh]',
					'description' => __( 'Enable this option to auto refresh all stock tickers on page w/o requirement to reload page manually.', 'wpaust' ),
					'class'       => 'checkbox',
					'value'       => isset( $defaults['refresh'] ) ? $defaults['refresh'] : false,
				) // args
			);

			// Refresh timeout field.
			add_settings_field(
				'wpau_stock_ticker-refresh_timeout',
				__( 'Refresh Timeout', 'wpaust' ),
				array( &$this, 'settings_field_input_number' ),
				'wpau_stock_ticker',
				'wpaust_advanced',
				array(
					'field'       => 'stock_ticker_defaults[refresh_timeout]',
					'description' => __( 'Define auto refresh timeout, in seconds', 'wpaust' ),
					'class'       => 'num',
					'value'       => isset( $defaults['refresh_timeout'] ) ? $defaults['refresh_timeout'] : 2,
					'min'         => 0,
					'max'         => HOUR_IN_SECONDS,
					'step'        => 5,
				)
			);

			// Global enqueue assets.
			add_settings_field(
				'wpau_stock_ticker-globalassets',
				__( 'Load assets on all pages?', 'wpaust' ),
				array( &$this, 'settings_field_checkbox' ),
				'wpau_stock_ticker',
				'wpaust_advanced',
				array(
					'field'       => 'stock_ticker_defaults[globalassets]',
					'description' => __( 'By default, Stock Ticker will load corresponding JavaScript files on demand. But, if you need to load assets on all pages, check this option. (For example, if you plan to load have some plugin that load widgets or content via Ajax, you need to check this option)', 'wpaust' ),
					'class'       => 'checkbox',
					'value'       => isset( $defaults['globalassets'] ) ? $defaults['globalassets'] : false,
				) // args
			);


		} // END public static function admin_init()

		/**
		 * Print description for Default section
		 */
		public function settings_default_section_description() {
			// Think of this as help text for the section.
			esc_attr_e(
				'Predefine default settings for Stock Ticker. Here you can set stock symbols and how you wish to present companies in ticker.',
				'wpaust'
			);
		}

		/**
		 * Print description for Advanced section
		 */
		public function settings_advanced_section_description() {
			// Think of this as help text for the section.
			esc_attr_e( 'Set advanced options important for caching quote feeds.', 'wpaust' );
		}

		/**
		 * This function provides text inputs for settings fields
		 * @param  array $args Array of field arguments.
		 */
		public function settings_field_input_text( $args ) {
			printf(
				'<input type="text" name="%s" id="%s" value="%s" class="%s" /><p class="description">%s</p>',
				esc_attr( $args['field'] ),
				esc_attr( $args['field'] ),
				esc_attr( $args['value'] ),
				sanitize_html_class( $args['class'] ),
				esc_html( $args['description'] )
			);
		} // END public function settings_field_input_text($args)


		/**
		 * This function provides number inputs for settings fields
		 * @param  array $args Array of field arguments.
		 */
		public function settings_field_input_number( $args ) {
			printf(
				'<input type="number" name="%1$s" id="%2$s" value="%3$s" min="%4$s" max="%5$s" step="%6$s" class="%7$s" /><p class="description">%8$s</p>',
				esc_attr( $args['field'] ),            // 1
				esc_attr( $args['field'] ),            // 2
				(int) $args['value'],                  // 3
				(int) $args['min'],                    // 4
				(int) $args['max'],                    // 5
				(int) $args['step'],                   // 6
				sanitize_html_class( $args['class'] ), // 7
				esc_html( $args['description'] )       // 8
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
			printf( '<select id="%s" name="%s">', esc_attr( $args['field'] ), esc_attr( $args['field'] ) );
			foreach ( $args['items'] as $key => $val ) {
				$selected = ( $args['value'] == $key ) ? 'selected=selected' : '';
				printf(
					'<option %s value="%s">%s</option>',
					esc_attr( $selected ),
					sanitize_key( $key ),
					sanitize_text_field( $val )
				);
			}
			printf( '</select><p class="description">%s</p>', esc_html( $args['description'] ) );
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
				$args['description']
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
		 * Sanitize settings options
		 * @param  array $input Array of option values entered on settings page.
		 * @return array        Sanitized settings values
		 */
		public function stock_ticker_sanitize( $input ) {
			foreach ( $input as $key => $value ) {
				switch ( $key ) {
					case 'symbols':
					case 'legend':
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
						if ( ! in_array( $value, array( 'name', 'symbol' ) ) ) {
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
					case 'refresh_timeout':
						$value = (int) $value;
						$value = ! empty( $value ) ? $value : 5 * MINUTE_IN_SECONDS;
						break;
					case 'speed':
						$value = (int) $value;
						$value = ! empty( $value ) ? $value : 50;
						break;
					case 'refresh':
						$value = ! empty( $value ) ? true : false;
						break;
				}
				$input[ $key ] = $value;
			}

			// Generate static CSS
			$css = "ul.stock_ticker li .sqitem{{$input['style']}}";
			$css .= "ul.stock_ticker li.zero .sqitem,ul.stock_ticker li.zero .sqitem:hover {color:{$input['zero']}}";
			$css .= "ul.stock_ticker li.minus .sqitem,ul.stock_ticker li.minus .sqitem:hover {color:{$input['minus']}}";
			$css .= "ul.stock_ticker li.plus .sqitem,ul.stock_ticker li.plus .sqitem:hover {color:{$input['plus']}}";

			// Now write content to file
			$upload_dir = wp_upload_dir();
			if ( ! file_put_contents( $upload_dir['basedir'] . '/stock-ticker-custom.css', $css, LOCK_EX ) ) {
				$error = error_get_last();
				add_settings_error(
					'stock-ticker-update',
					esc_attr( 'stock-ticker-custom-css' ),
					sprintf(
						__( 'Failed to write custom CSS file because of error <em>%1$s</em><br />Please create mentioned file manually and add following code to it:<br /><code>%2$s</code>', 'stock-ticker' ),
						$error['message'],
						$css
					),
					'error'
				);
			}
			unset( $css );

			// Generate static refresh JS
			if ( ! empty( $input['refresh'] ) ) {
				$js = sprintf( 'var stockTickers = setInterval(function(){ stock_tickers_load() }, %s);', $input['refresh_timeout'] * 1000 );
				if ( ! file_put_contents( $upload_dir['basedir'] . '/stock-ticker-refresh.js', $js, LOCK_EX ) ) {
					$error = error_get_last();
					add_settings_error(
						'stock-ticker-update',
						esc_attr( 'stock-ticker-custom-js' ),
						sprintf(
							__( 'Failed to write custom JS file for auto refreshing ticker because of error <em>%1$s</em><br />Please create mentioned file manually and add following code to it:<br /><code>%2$s</code>', 'stock-ticker' ),
							$error['message'],
							$js
						),
						'error'
					);
				}
				unset( $js );
			}

			return $input;
		} // END public function stock_ticker_sanitize($input) {

		/**
		 * Add a menu
		 */
		public function add_menu() {
			// Add a page to manage this plugin's settings.
			add_options_page(
				__( 'Stock Ticker Settings', 'wpaust' ),
				__( 'Stock Ticker', 'wpaust' ),
				'manage_options',
				'wpau_stock_ticker',
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
			include( sprintf( '%s/../templates/settings.php', dirname( __FILE__ ) ) );
		} // END public function plugin_settings_page()

	} // END class Wpau_Stock_Ticker_Settings
} // END if(!class_exists( 'Wpau_Stock_Ticker_Settings' ))
