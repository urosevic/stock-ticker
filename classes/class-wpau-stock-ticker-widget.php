<?php
/**
 * Stock Ticker Widget
 *
 * @category Wpau_Stock_Ticker_Widget
 * @package Stock Ticker
 * @author Aleksandar Urosevic
 * @license http://www.gnu.org/copyleft/gpl.html GNU General Public License
 * @link https://urosevic.net
 */

/**
 * Wpau_Stock_Ticker_Widget Class provide widget settings and output for Stock Ticker plugin
 *
 * @category Class
 * @package Stock Ticker
 * @author Aleksandar Urosevic
 * @license https://www.gnu.org/copyleft/gpl.html GNU General Public License
 * @link https://urosevic.net
 */
class Wpau_Stock_Ticker_Widget extends WP_Widget {
	/**
	 * Sets up the widget name etc
	 */
	public function __construct() {
		// Widget actual processes.
		parent::__construct(
			'stock_ticker', // Base ID.
			__( 'Stock Ticker', 'stock-ticker' ), // Name.
			array(
				'description'                 => __( 'Show ticker with stock trends', 'stock-ticker' ),
				'customize_selective_refresh' => true,
			) // Args.
		);
	}

	/**
	 * Outputs the content of the widget
	 *
	 * @param array $args Array of widget parameters.
	 * @param array $instance Array of widget settings.
	 */
	public function widget( $args, $instance ) {
		// Use cached widget in customizer.
		if ( ! $this->is_preview() ) {
			$cached = wp_cache_get( $args['widget_id'] );
			if ( ! empty( $cached ) ) {
				echo $cached;
				return;
			}
			ob_start();
		}

		global $wpau_stockticker;

		// Get defaults in instance is empty (for customizer).
		if ( empty( $instance ) ) {
			$instance          = $wpau_stockticker->defaults;
			$instance['title'] = __( 'Stock Ticker', 'stock-ticker' );
		}

		// Outputs the content of the widget.
		if ( ! empty( $instance['title'] ) ) {
			$title = apply_filters( 'widget_title', $instance['title'] );
		}

		if ( empty( $instance['symbols'] ) ) {
			return;
		}

		$instance['number_format'] = empty( $instance['number_format'] ) ? 'dc' : $instance['number_format'];
		$instance['decimals']      = empty( $instance['decimals'] ) ? 2 : $instance['decimals'];
		$instance['static']        = empty( $instance['static'] ) ? '0' : '1';
		$instance['prefill']       = empty( $instance['prefill'] ) ? '0' : '1';
		$instance['duplicate']     = empty( $instance['duplicate'] ) ? '0' : '1';
		$instance['speed']         = isset( $instance['speed'] ) ? intval( $instance['speed'] ) : 50;

		// Output live stock ticker widget.
		echo $args['before_widget'];

		if ( ! empty( $title ) ) {
			echo $args['before_title'] . $title . $args['after_title'];
		}

		echo $wpau_stockticker->shortcode( $instance );

		echo $args['after_widget'];

		// End cache in customizer.
		if ( ! $this->is_preview() ) {
			$cached = ob_get_flush();
			wp_cache_set( $args['widget_id'], $cached );
		}
	}

	/**
	 * Ouputs the options form on admin
	 *
	 * @param array $instance The widget options.
	 */
	public function form( $instance ) {

		global $wpau_stockticker;

		// Get defaults.
		$defaults = $wpau_stockticker->defaults;

		// Outputs the options form on admin.
		if ( isset( $instance['title'] ) ) {
			$title = $instance['title'];
		} else {
			$title = __( 'Stock Ticker', 'stock-ticker' );
		}
		if ( isset( $instance['symbols'] ) ) {
			$symbols = $instance['symbols'];
		} else {
			$symbols = $defaults['symbols'];
		}
		if ( isset( $instance['show'] ) ) {
			$show = $instance['show'];
		} else {
			$show = $defaults['show'];
		}

		if ( isset( $instance['static'] ) ) {
			$static = $instance['static'];
		} else {
			$static = '0';
		}

		$nolink = '0';

		if ( isset( $instance['prefill'] ) ) {
			$prefill = $instance['prefill'];
		} else {
			$prefill = '0';
		}

		if ( isset( $instance['duplicate'] ) ) {
			$duplicate = $instance['duplicate'];
		} else {
			$duplicate = '0';
		}

		if ( isset( $instance['class'] ) ) {
			$class = $instance['class'];
		} else {
			$class = '';
		}

		if ( isset( $instance['speed'] ) ) {
			$speed = $instance['speed'];
		} else {
			$speed = $defaults['speed'];
		}

		if ( isset( $instance['number_format'] ) ) {
			$number_format = $instance['number_format'];
		} else {
			$number_format = $defaults['number_format'];
		}
		if ( isset( $instance['decimals'] ) ) {
			$decimals = $instance['decimals'];
		} else {
			$decimals = $defaults['decimals'];
		}

		?>
		<p>
		<label for="<?php echo $this->get_field_id( 'title' ); ?>"><?php esc_attr_e( 'Title' ); ?>:</label>
		<input class="widefat" id="<?php echo $this->get_field_id( 'title' ); ?>" name="<?php echo $this->get_field_name( 'title' ); ?>" type="text" value="<?php echo esc_attr( $title ); ?>" />
		</p>
		<p>
		<label for="<?php echo $this->get_field_id( 'symbols' ); ?>"><?php esc_attr_e( 'Stock Symbols', 'stock-ticker' ); ?>:</label>
		<input class="widefat" id="<?php echo $this->get_field_id( 'symbols' ); ?>" name="<?php echo $this->get_field_name( 'symbols' ); ?>" type="text" value="<?php echo esc_attr( $symbols ); ?>" title="<?php esc_html_e( 'For currencies use format EURGBP=X; for Dow Jones use .DJI; for specific stock exchange use format EXCHANGE:SYMBOL like LON:FFX', 'stock-ticker' ); ?>" />
		</p>

		<p>
		<label for="<?php echo $this->get_field_id( 'show' ); ?>"><?php esc_attr_e( 'Represent Company as', 'stock-ticker' ); ?>:</label>
		<select class="widefat" id="<?php echo $this->get_field_id( 'show' ); ?>" name="<?php echo $this->get_field_name( 'show' ); ?>">
			<option <?php echo ( 'name' === $show ) ? 'selected="selected"' : ''; ?> value="name"><?php esc_attr_e( 'Company Name', 'stock-ticker' ); ?></option>
			<option <?php echo ( 'symbol' === $show ) ? 'selected="selected"' : ''; ?> value="symbol"><?php esc_attr_e( 'Stock Symbol', 'stock-ticker' ); ?></option>
		</select>
		</p>

		<p>
		<label for="<?php echo $this->get_field_id( 'number_format' ); ?>"><?php esc_attr_e( 'Number format', 'stock-ticker' ); ?>:</label>
		<select class="widefat" id="<?php echo $this->get_field_id( 'number_format' ); ?>" name="<?php echo $this->get_field_name( 'number_format' ); ?>">
			<option <?php echo ( 'cd' === $number_format ) ? 'selected="selected"' : ''; ?> value="cd">0,000.00</option>
			<option <?php echo ( 'dc' === $number_format ) ? 'selected="selected"' : ''; ?> value="dc">0.000,00</option>
			<option <?php echo ( 'sd' === $number_format ) ? 'selected="selected"' : ''; ?> value="sd">0 000.00</option>
			<option <?php echo ( 'sc' === $number_format ) ? 'selected="selected"' : ''; ?> value="sc">0 000,00</option>
		</select>
		</p>

		<p>
		<label for="<?php echo $this->get_field_id( 'decimals' ); ?>"><?php esc_attr_e( 'Decimal places', 'stock-ticker' ); ?>:</label>
		<select class="widefat" id="<?php echo $this->get_field_id( 'decimals' ); ?>" name="<?php echo $this->get_field_name( 'decimals' ); ?>">
			<option <?php echo ( 1 === $decimals ) ? 'selected="selected"' : ''; ?> value="1">One</option>
			<option <?php echo ( 2 === $decimals ) ? 'selected="selected"' : ''; ?> value="2">Two</option>
			<option <?php echo ( 3 === $decimals ) ? 'selected="selected"' : ''; ?> value="3">Three</option>
			<option <?php echo ( 4 === $decimals ) ? 'selected="selected"' : ''; ?> value="4">Four</option>
		</select>
		</p>

		<p>
		<label for="<?php echo $this->get_field_id( 'speed' ); ?>"><?php esc_attr_e( 'Ticker Speed', 'stock-ticker' ); ?>:</label>
		<input class="number small-text" id="<?php echo $this->get_field_id( 'speed' ); ?>" name="<?php echo $this->get_field_name( 'speed' ); ?>" type="number" value="<?php echo esc_attr( $speed ); ?>" />
		</p>

		<p>
		<label for="<?php echo $this->get_field_id( 'static' ); ?>">
		<input class="checkbox" id="<?php echo $this->get_field_id( 'static' ); ?>" name="<?php echo $this->get_field_name( 'static' ); ?>" type="checkbox" value="1" <?php checked( $static, true, true ); ?> />
		<?php esc_attr_e( 'Make this ticker static (disable scrolling)', 'stock-ticker' ); ?>
		</label>
		<br />
		<label for="<?php echo $this->get_field_id( 'prefill' ); ?>">
		<input class="checkbox" id="<?php echo $this->get_field_id( 'prefill' ); ?>" name="<?php echo $this->get_field_name( 'prefill' ); ?>" type="checkbox" value="1" <?php checked( $prefill, true, true ); ?> />
		<?php esc_attr_e( 'Start ticker prefilled with data', 'stock-ticker' ); ?>
		</label>
		<br />
		<label for="<?php echo $this->get_field_id( 'duplicate' ); ?>">
		<input class="checkbox" id="<?php echo $this->get_field_id( 'duplicate' ); ?>" name="<?php echo $this->get_field_name( 'duplicate' ); ?>" type="checkbox" value="1" <?php checked( $duplicate, true, true ); ?> />
		<?php esc_attr_e( 'Duplicate items to make ticker continuous', 'stock-ticker' ); ?>
		</label>
		</p>
		<p>
		<label for="<?php echo $this->get_field_id( 'class' ); ?>"><?php esc_attr_e( 'Cusom Class', 'stock-ticker' ); ?>:</label>
		<input class="widefat" id="<?php echo $this->get_field_id( 'class' ); ?>" name="<?php echo $this->get_field_name( 'class' ); ?>" type="text" value="<?php echo esc_attr( $class ); ?>" title="<?php esc_html_e( 'Set custom CSS class to customize block look', 'stock-ticker' ); ?>" />
		</p>
		<?php
	}

	/**
	 * Processing widget options on save
	 *
	 * @param array $new_instance The new options.
	 * @param array $old_instance The previous options.
	 */
	public function update( $new_instance, $old_instance ) {
		// Processes widget options to be saved.
		$instance                  = array();
		$instance['title']         = ( ! empty( $new_instance['title'] ) ) ? strip_tags( $new_instance['title'] ) : '';
		$instance['symbols']       = ( ! empty( $new_instance['symbols'] ) ) ? strip_tags( $new_instance['symbols'] ) : '';
		$instance['show']          = ( ! empty( $new_instance['show'] ) ) ? strip_tags( $new_instance['show'] ) : '';
		$instance['number_format'] = ( ! empty( $new_instance['number_format'] ) ) ? strip_tags( $new_instance['number_format'] ) : 'dc';
		$instance['decimals']      = ( ! empty( $new_instance['decimals'] ) ) ? intval( $new_instance['decimals'] ) : 2;
		$instance['static']        = ( ! empty( $new_instance['static'] ) ) ? '1' : '0';
		$instance['prefill']       = ( ! empty( $new_instance['prefill'] ) ) ? '1' : '0';
		$instance['duplicate']     = ( ! empty( $new_instance['duplicate'] ) ) ? '1' : '0';
		$instance['class']         = ( ! empty( $new_instance['class'] ) ) ? strip_tags( $new_instance['class'] ) : '';
		$instance['speed']         = ( ! empty( $new_instance['speed'] ) ) ? intval( $new_instance['speed'] ) : 50;

		return $instance;
	}
}

/**
 * Register widget
 */
function wpau_stock_ticker_widget_init() {
	register_widget( 'Wpau_Stock_Ticker_Widget' );
}
add_action( 'widgets_init', 'wpau_stock_ticker_widget_init' );
