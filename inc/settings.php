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
			global $wpau_stockticker;

			// Get default values.
			$this->slug = $wpau_stockticker->plugin_slug;
			$this->option_name = $wpau_stockticker->plugin_option;
			$this->defaults = $wpau_stockticker->defaults; // get_site_option( $this->option_name );

			add_action( 'admin_init', array( &$this, 'register_settings' ) );
			add_action( 'admin_menu', array( &$this, 'add_menu' ) );
		} // END public function __construct

		/**
		 * Hook into WP's register_settings action hook
		 */
		public function register_settings() {
			global $wpau_stockticker;

			// Add general settings section.
			add_settings_section(
				'wpaust_general',
				__( 'General', 'wpaust' ),
				array( &$this, 'settings_general_section_description' ),
				$wpau_stockticker->plugin_slug
			);

			// Add setting's fields.
			add_settings_field(
				$this->option_name . 'avapikey',
				__( 'AlphaVantage.co API Key', 'wpaust' ),
				array( &$this, 'settings_field_input_password' ),
				$wpau_stockticker->plugin_slug,
				'wpaust_general',
				array(
					'field'       => $this->option_name . '[avapikey]',
					'description' => sprintf(
						wp_kses(
							__( 'To get stock data we use AlphaVantage.co API. If you do not have it already, <a href="%1$s" target="_blank">%2$s</a> and enter it here.', 'wpaust' ),
							array(
								'a' => array(
									'href' => array(),
									'target' => array( '_blank' ),
								),
							)
						),
						esc_url( 'https://www.alphavantage.co/support/#api-key' ),
						__( 'Claim your free API Key', 'wpaust' )
					),
					'class'       => 'widefat',
					'value'       => $this->defaults['avapikey'],
				)
			);
			add_settings_field(
				$this->option_name . 'all_symbols',
				__( 'All Stock Symbols', 'wpaust' ),
				array( &$this, 'settings_field_input_text' ),
				$wpau_stockticker->plugin_slug,
				'wpaust_general',
				array(
					'field'       => $this->option_name . '[all_symbols]',
					'description' => __( 'You can use some or all of those symbils in any ticker on website. Please note, you have to define which symbols you will use per widget/shortcode. Enter stock symbols separated with comma.', 'wpaust' ),
					'class'       => 'widefat',
					'value'       => $this->defaults['all_symbols'],
				)
			);
			add_settings_field(
				$this->option_name . 'loading_message',
				__( 'Loading Message', 'wpaust' ),
				array( &$this, 'settings_field_input_text' ),
				$wpau_stockticker->plugin_slug,
				'wpaust_general',
				array(
					'field'       => $this->option_name . '[loading_message]',
					'description' => __( 'Customize message displayed to visitor until plugin load stock data through AJAX.', 'wpaust' ),
					'class'       => 'widefat',
					'value'       => $this->defaults['loading_message'],
				)
			);
			// Default error message.
			add_settings_field(
				$this->option_name . 'error_message',
				__( 'Error Message', 'wpaust' ),
				array( &$this, 'settings_field_input_text' ),
				$wpau_stockticker->plugin_slug,
				'wpaust_general',
				array(
					'field'       => $this->option_name . '[error_message]',
					'description' => __(
						'When we do not have pre-fetched stock data for symbols requested in block from AlphaVantage.co, display this message in ticker',
						'wpaust'
					),
					'class'       => 'widefat',
					'value'       => $this->defaults['error_message'],
				)
			);

			// --- Register setting General so $_POST handling is done ---
			register_setting(
				'wpaust_general',
				$this->option_name,
				array( &$this, 'sanitize_options' )
			);

			// Add default settings section.
			add_settings_section(
				'wpaust_default',
				__( 'Default', 'wpaust' ),
				array( &$this, 'settings_default_section_description' ),
				$wpau_stockticker->plugin_slug
			);
			// Add setting's fields.
			add_settings_field(
				$this->option_name . 'symbols',
				__( 'Stock Symbols', 'wpaust' ),
				array( &$this, 'settings_field_input_text' ),
				$wpau_stockticker->plugin_slug,
				'wpaust_default',
				array(
					'field'       => $this->option_name . '[symbols]',
					'description' => __( 'Those simbols are used as default for shortcodes w/o provided symbols, but not for widgets as widget have own symbold setting. Enter stock symbols separated with comma.', 'wpaust' ),
					'class'       => 'widefat',
					'value'       => $this->defaults['symbols'],
				)
			);
			add_settings_field(
				$this->option_name . 'show',
				__( 'Show Company as', 'wpaust' ),
				array( &$this, 'settings_field_select' ),
				$wpau_stockticker->plugin_slug,
				'wpaust_default',
				array(
					'field'       => $this->option_name . '[show]',
					'description' => sprintf(
						__( 'What to show as Company identifier by default for shortcodes if not provided shortcode parameter %s. Widget have own setting for this.', 'wpaust' ),
						"'show'"
					),
					'items'       => array(
						'name'   => __( 'Company Name', 'wpaust' ),
						'symbol' => __( 'Stock Symbol', 'wpaust' ),
					),
					'value' => $this->defaults['show'],
				)
			);

			add_settings_field(
				$this->option_name . 'number_format',
				__( 'Number format', 'wpaust' ),
				array( &$this, 'settings_field_select' ),
				$wpau_stockticker->plugin_slug,
				'wpaust_default',
				array(
					'field'       => $this->option_name . '[number_format]',
					'description' => __( 'Select default number format', 'stock-quote' ),
					'items'       => array(
						'cd' => '0,000.00',
						'dc' => '0.000,00',
						'sd' => '0 000.00',
						'sc' => '0 000,00',
					),
					'value' => $this->defaults['number_format'],
					'class'       => 'regular-text',
				)
			);
			add_settings_field(
				$this->option_name . 'decimals',
				__( 'Decimal places', 'wpaust' ),
				array( &$this, 'settings_field_select' ),
				$wpau_stockticker->plugin_slug,
				'wpaust_default',
				array(
					'field'       => $this->option_name . '[decimals]',
					'description' => __( 'Select amount of decimal places for numbers', 'stock-quote' ),
					'items'       => array(
						'1' => __( 'One', 'stock-quote' ),
						'2' => __( 'Two', 'stock-quote' ),
						'3' => __( 'Three', 'stock-quote' ),
						'4' => __( 'Four', 'stock-quote' ),
					),
					'value' => $this->defaults['decimals'],
					'class'       => 'regular-text',
				)
			);
			// Color pickers.
			// Unchanged.
			add_settings_field(
				$this->option_name . 'quote_zero',
				__( 'Unchanged Quote', 'wpaust' ),
				array( &$this, 'settings_field_colour_picker' ),
				$wpau_stockticker->plugin_slug,
				'wpaust_default',
				array(
					'field'       => $this->option_name . '[zero]',
					'description' => __( 'Set colour for unchanged quote', 'wpaust' ),
					'value'       => $this->defaults['zero'],
				)
			);
			// Minus.
			add_settings_field(
				$this->option_name . 'quote_minus',
				__( 'Negative Change', 'wpaust' ),
				array( &$this, 'settings_field_colour_picker' ),
				$wpau_stockticker->plugin_slug,
				'wpaust_default',
				array(
					'field'       => $this->option_name . '[minus]',
					'description' => __( 'Set colour for negative change', 'wpaust' ),
					'value'       => $this->defaults['minus'],
				)
			);
			// Plus.
			add_settings_field(
				$this->option_name . 'quote_plus',
				__( 'Positive Change', 'wpaust' ),
				array( &$this, 'settings_field_colour_picker' ),
				$wpau_stockticker->plugin_slug,
				'wpaust_default',
				array(
					'field'       => $this->option_name . '[plus]',
					'description' => __( 'Set colour for positive change', 'wpaust' ),
					'value'       => $this->defaults['plus'],
				)
			);

			// --- Register setting Default so $_POST handling is done ---
			register_setting(
				'wpaust_default',
				$this->option_name,
				array( &$this, 'sanitize_options' )
			);

			// Add advanced settings section.
			add_settings_section(
				'wpaust_advanced',
				__( 'Advanced', 'wpaust' ),
				array( &$this, 'settings_advanced_section_description' ),
				$wpau_stockticker->plugin_slug
			);
			// Add setting's fields.
			// Ticker speed.
			add_settings_field(
				$this->option_name . 'speed',
				__( 'Ticker Speed', 'wpaust' ),
				array( &$this, 'settings_field_input_number' ),
				$wpau_stockticker->plugin_slug,
				'wpaust_advanced',
				array(
					'field'       => $this->option_name . '[speed]',
					'description' => __( 'Define speed of ticker scrolling in pixels per second (default is 50)', 'wpaust' ),
					'class'       => 'num',
					'value'       => isset( $this->defaults['speed'] ) ? $this->defaults['speed'] : 50,
					'min'         => 10,
					'max'         => 200,
					'step'        => 1,
				)
			);
			// Default ticker item template.
			add_settings_field(
				$this->option_name . 'template',
				__( 'Item Template', 'wpaust' ),
				array( &$this, 'settings_field_textarea' ),
				$wpau_stockticker->plugin_slug,
				'wpaust_advanced',
				array(
					'field'       => $this->option_name . '[template]',
					'description' => sprintf(
						__( 'Custom template for item. You can use macro keywords %1$s and %2$s mixed with HTML tags %3$s and/or %4$s.', 'wpaust' ),
						'%exch_symbol%, %symbol%, %company%, %price%, %change%',
						'%changep%',
						'&lt;span&gt;, &lt;em&gt;',
						'&lt;strong&gt;'
					),
					'class' => 'widefat',
					'rows'  => 2,
					'value' => $this->defaults['template'],
				)
			);
			// Custom name.
			add_settings_field(
				$this->option_name . 'legend',
				__( 'Custom Names', 'wpaust' ),
				array( &$this, 'settings_field_textarea' ),
				$wpau_stockticker->plugin_slug,
				'wpaust_advanced',
				array(
					'field'       => $this->option_name . '[legend]',
					'class'       => 'widefat',
					'value'       => $this->defaults['legend'],
					'rows'        => 7,
					'description' => __(
						'Define custom names for symbols. Single symbol per row in format EXCHANGE:SYMBOL;CUSTOM_NAME',
						'wpaust'
					),
				)
			);
			/*
			// Caching timeout field.
			add_settings_field(
				$this->option_name . 'cache_timeout',
				__( 'Cache Timeout', 'wpaust' ),
				array( &$this, 'settings_field_input_number' ),
				$wpau_stockticker->plugin_slug,
				'wpaust_advanced',
				array(
					'field'       => $this->option_name . '[cache_timeout]',
					'description' => __( 'Define cache timeout for single quote set, in seconds', 'wpaust' ),
					'class'       => 'num',
					'value'       => isset( $this->defaults['cache_timeout'] ) ? $this->defaults['cache_timeout'] : 180,
					'min'         => 0,
					'max'         => DAY_IN_SECONDS,
					'step'        => 5,
				)
			);
			*/
			// Fetch timeout field.
			add_settings_field(
				$this->option_name . 'timeout',
				__( 'Fetch Timeout', 'wpaust' ),
				array( &$this, 'settings_field_input_number' ),
				$wpau_stockticker->plugin_slug,
				'wpaust_advanced',
				array(
					'field'       => $this->option_name . '[timeout]',
					'description' => __( 'Define timeout to fetch quote feed before give up and display error message, in seconds (default is 2)', 'wpaust' ),
					'class'       => 'num',
					'value'       => isset( $this->defaults['timeout'] ) ? $this->defaults['timeout'] : 2,
					'min'         => 1,
					'max'         => 60,
					'step'        => 1,
				)
			);

			// Default styling.
			add_settings_field(
				$this->option_name . 'style',
				__( 'Custom Style', 'wpaust' ),
				array( &$this, 'settings_field_textarea' ),
				$wpau_stockticker->plugin_slug,
				'wpaust_advanced',
				array(
					'field'       => $this->option_name . '[style]',
					'class'       => 'widefat',
					'rows'        => 2,
					'value'       => $this->defaults['style'],
					'description' => __( 'Define custom CSS style for ticker item (font family, size, weight)', 'wpaust' ),
				)
			);

			// Refresh checkbox.
			add_settings_field(
				$this->option_name . 'refresh',
				__( 'Auto Refresh', 'wpaust' ),
				array( &$this, 'settings_field_checkbox' ),
				$wpau_stockticker->plugin_slug,
				'wpaust_advanced',
				array(
					'field'       => $this->option_name . '[refresh]',
					'description' => __( 'Enable this option to auto refresh all stock tickers on page w/o requirement to reload page manually.', 'wpaust' ),
					'class'       => 'checkbox',
					'value'       => isset( $this->defaults['refresh'] ) ? $this->defaults['refresh'] : false,
				) // args
			);

			// Refresh timeout field.
			add_settings_field(
				$this->option_name . 'refresh_timeout',
				__( 'Refresh Timeout', 'wpaust' ),
				array( &$this, 'settings_field_input_number' ),
				$wpau_stockticker->plugin_slug,
				'wpaust_advanced',
				array(
					'field'       => $this->option_name . '[refresh_timeout]',
					'description' => __( 'Define auto refresh timeout, in seconds', 'wpaust' ),
					'class'       => 'num',
					'value'       => isset( $this->defaults['refresh_timeout'] ) ? $this->defaults['refresh_timeout'] : 2,
					'min'         => 0,
					'max'         => HOUR_IN_SECONDS,
					'step'        => 5,
				)
			);

			// Global enqueue assets.
			add_settings_field(
				$this->option_name . 'globalassets',
				__( 'Load assets on all pages?', 'wpaust' ),
				array( &$this, 'settings_field_checkbox' ),
				$wpau_stockticker->plugin_slug,
				'wpaust_advanced',
				array(
					'field'       => $this->option_name . '[globalassets]',
					'description' => __( 'By default, Stock Ticker will load corresponding JavaScript files on demand. But, if you need to load assets on all pages, check this option. (For example, if you plan to load have some plugin that load widgets or content via Ajax, you need to check this option)', 'wpaust' ),
					'class'       => 'checkbox',
					'value'       => isset( $this->defaults['globalassets'] ) ? $this->defaults['globalassets'] : false,
				) // args
			);

			// --- Register setting Advanced so $_POST handling is done ---
			register_setting(
				'wpaust_advanced',
				$this->option_name,
				array( &$this, 'sanitize_options' )
			);


		} // END public static function register_settings()

		/**
		 * Print description for General section
		 */
		public function settings_general_section_description() {
			// Think of this as help text for the section.
			esc_attr_e(
				'Predefine general settings for Stock Ticker. Here you can set API key and symbols used on whole website (in all ticker).',
				'wpaust'
			);
		}

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
		 * This function provides password inputs for settings fields
		 * @param  array $args Array of field arguments.
		 */
		public function settings_field_input_password( $args ) {
			printf(
				'<input type="text" name="%s" id="%s" value="%s" class="%s" /><p class="description">%s</p>',
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
			if ( empty( $args['class'] ) ) {
				$args['class'] = 'regular-text';
			}
			printf(
				'<select id="%1$s" name="%1$s" class="%2$s">',
				esc_attr( $args['field'] ),
				sanitize_html_class( $args['class'] )
			);
			foreach ( $args['items'] as $key => $val ) {
				$selected = ( $args['value'] == $key ) ? 'selected=selected' : '';
				printf(
					'<option %1$s value="%2$s">%3$s</option>',
					esc_attr( $selected ),      // 1
					sanitize_key( $key ),       // 2
					sanitize_text_field( $val ) // 3
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
		public function sanitize_options( $options ) {

			$sanitized = get_site_option( $this->option_name );
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
					case 'symbols':
					case 'all_symbols':
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
						if ( ! in_array( $value, array( 'name', 'symbol' ) ) ) {
							$value = 'name';
						}
						break;
					case 'template':
						$value = strip_tags( $value, '<span><em><strong>' );
						break;
					// case 'cache_timeout':
					// 	$value = (int) $value;
					// 	$value = ! empty( $value ) ? $value : 180;
					// 	break;
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
					case 'decimals':
						$value = (int) $value;
						$value = ! empty( $value ) ? $value : 2;
						break;
					case 'number_format':
						$value = strip_tags( stripslashes( $value ) );
						if ( ! in_array( $value, array( 'dc','sd','sc','cd' ) ) ) {
							$value = 'dc';
						}
						break;
				}
				$sanitized[ $key ] = $value;
			}

			// Generate static CSS
			$css = "ul.stock_ticker li .sqitem{{$sanitized['style']}}";
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
						__( 'Failed to write custom CSS file because of error <em>%1$s</em><br />Please create mentioned file manually and add following code to it:<br /><code>%2$s</code>', 'stock-ticker' ),
						$error['message'],
						$css
					),
					'error'
				);
			}
			unset( $css );

			// Generate static refresh JS
			if ( ! empty( $sanitized['refresh'] ) ) {
				$js = sprintf( 'var stockTickers = setInterval(function(){ stock_tickers_load() }, %s);', $sanitized['refresh_timeout'] * 1000 );
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

			// Clear transient but only if changed one of:
			// API key, All Stock Symbols, Cache Timeout or Fetch Timeout
			// @TODO remove cache_timeout
			if (
				$previous_options['avapikey'] !== $sanitized['avapikey'] ||
				$previous_options['all_symbols'] !== $sanitized['all_symbols'] ||
				// $previous_options['cache_timeout'] !== $sanitized['cache_timeout'] ||
				$previous_options['timeout'] !== $sanitized['timeout']
			) {
				error_log( 'Stock Ticker: restarting data fetching from first symbol' );
				// Wpau_Stock_Ticker::clean_transients();
				// error_log( 'Stock Ticker: clean transients after settings have been updated' );
				Wpau_Stock_Ticker::restart_av_fetching();
			}

			return $sanitized;
		} // END public function sanitize_options($sanitized) {

		/**
		 * Add a menu
		 */
		public function add_menu() {
			global $wpau_stockticker;
			// Add a page to manage this plugin's settings.
			add_options_page(
				__( 'Stock Ticker', 'wpaust' ),
				__( 'Stock Ticker', 'wpaust' ),
				'manage_options',
				$wpau_stockticker->plugin_slug,
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
