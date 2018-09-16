<?php
/**
 * Stock Ticker General Settings page template
 *
 * @category Template
 * @package Stock Ticker
 * @author Aleksandar Urosevic
 * @license https://www.gnu.org/copyleft/gpl.html GNU General Public License
 * @link https://urosevic.net
 */
// Exit if accessed directly
if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

global $wpau_stockticker;
?>
<div class="wrap" id="stock_ticker_settings">
	<h2><?php echo $wpau_stockticker->plugin_name . ' ' . __( 'Settings', 'wpaust' ); ?></h2>
	<em>Plugin version: <?php echo $wpau_stockticker::VER; ?></em>
	<div class="stock_ticker_wrapper">
		<div class="content_cell">
			<form method="post" action="options.php">
				<?php settings_fields( 'wpau_stock_ticker' ); ?>
				<?php do_settings_sections( $wpau_stockticker->plugin_slug ); ?>
				<?php submit_button(); ?>
			</form>
		</div><!-- .content_cell -->

		<div class="sidebar_container">
			<a href="https://urosevic.net/wordpress/donate/?donate_for=stock-ticker" class="aust-button paypal_donate" target="_blank">Donate</a>
			<br />
			<a href="https://wordpress.org/plugins/stock-ticker/faq/" class="aust-button" target="_blank">FAQ</a>
			<br />
			<a href="https://wordpress.org/support/plugin/stock-ticker" class="aust-button" target="_blank">Community Support</a>
			<h2><?php esc_attr_e( 'Disclaimer', 'wpaust' ); ?></h2>
			<p class="description"><?php
				printf(
				__( 'Since %1$s version %2$s source for all stock data used in plugin is provided by %3$s, displayed for informational and educational purposes only and should not be considered as investment advise. <br />Author of the plugin does not accept liability or responsibility for your use of plugin, including but not limited to trading and investment results.' ),
				__( 'Stock Ticker', 'wpaust' ),
				'3.0.0',
				'<strong>Alpha Vantage</strong>'
				);
			?></p>

		</div><!-- .sidebar_container -->
	</div><!-- .stock_ticker_wrapper -->

	<div class="help">
		<h2><?php esc_attr_e( 'Help', 'wpaust' ); ?></h2>
		<p><?php printf( esc_attr__( 'You also can use shortcode %s where:', 'wpaust' ), '<code>[stock_ticker symbols="" show="" number_format="" decimals="" static="" speed="" class=""]</code>' ); ?>
			<ul>
				<li><code><strong>symbols</strong></code> <?php esc_attr_e( 'represent array of stock symbols (default from this settings page used if no custom set by shortcode)', 'wpaust' ); ?></li>
				<li><code><strong>show</strong></code> <?php printf( esc_attr__( 'can be %1$s to represent company with Company Name (default), or %2$s to represent company with Stock Symbol', 'wpaust' ), '<code>name</code>', '<code>symbol</code>' ); ?></li>
				<li><code><strong>number_format</strong></code> <?php printf( __( 'override default number format for values (default from this settings page used if no custom set by shortcode). Valid options are: %s and %s', 'wpaust' ), '<code>cd</code> for <em>0.000,00</em>; <code>dc</code> for <em>0,000.00</em>; <code>sd</code> for <em>0 000.00</em>', '<code>sc</code> for <em>0 000,00</em>' ); ?></li>
				<li><code><strong>decimals</strong></code> <?php _e( 'override default number of decimal places for values (default from this settings page used if no custom set by shortcode). Valud values are: 1, 2, 3 and 4', 'wpaust' ); ?></li>
				<li><code><strong>static</strong></code> <?php printf( esc_attr__( 'disables scrolling ticker and makes it static if set to %1$s or %2$s', 'wpaust' ), '<code>1</code>', '<code>true</code>' ); ?></li>
				<li><code><strong>prefill</strong></code> <?php printf( esc_attr__( 'to start with pre-filled instead empty ticker set to %1$s or %2$s', 'wpaust' ), '<code>1</code>', '<code>true</code>' ); ?></li>
				<li><code><strong>duplicate</strong></code> <?php printf( esc_attr__( 'if there is less items than visible on the ticker, set this to %1$s or %2$s to make it continuous', 'wpaust' ), '<code>1</code>', '<code>true</code>' ); ?></li>
				<li><code><strong>speed</strong></code> <?php echo esc_attr__( 'define speed of ticker rendered by shortcode block, different that default speed set on this global settings page', 'wpaust' ); ?></li>
				<li><code><strong>class</strong></code> <?php echo esc_attr__( 'to customize block look and feel set custom CSS class (optional)', 'wpaust' ); ?></li>

			</ul>
		</p>

		<h2><?php esc_attr_e( 'Supported Stock Exchanges', 'wpaust' ); ?></h2>
		<ul>
			<?php
			foreach ( $wpau_stockticker::$exchanges as $symbol => $name ) {
				printf(
					'<li><strong>%1$s</strong> - %2$s</li>',
					$symbol,
					$name
				);
			}
			?>
		</ul>
	</div><!-- .help_cell -->
</div>
<script type="text/javascript">
jQuery(document).ready(function($){
	$('.wpau-color-field').wpColorPicker();
});
</script>
