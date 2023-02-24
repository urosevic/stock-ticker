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
	<h2><?php printf( __( '%s Settings', 'stock-ticker' ), $wpau_stockticker->plugin_name ); ?></h2>
	<em><?php printf( __( 'Plugin version: %s', 'stock-ticker' ), $wpau_stockticker::VER ); ?></em>
	<div class="stock_ticker_wrapper">
		<div class="content_container">
			<form method="post" action="options.php">
				<?php settings_fields( 'wpau_stock_ticker' ); ?>
				<?php do_settings_sections( $wpau_stockticker->plugin_slug ); ?>
				<?php submit_button(); ?>
			</form>
		</div><!-- .content_container -->

		<div class="sidebar_container">
			<div class="references">
				<a href="https://wordpress.org/plugins/stock-ticker/#faq-header" class="aust-button" target="_blank"><?php _e( 'FAQ', 'stock-ticker' ); ?></a>
				<br />
				<a href="https://wordpress.org/support/plugin/stock-ticker/" class="aust-button" target="_blank"><?php _e( 'Community Support', 'stock-ticker' ); ?></a>
				<br />
				<a href="https://wordpress.org/support/plugin/stock-ticker/reviews/#new-post" class="aust-button" target="_blank"><?php _e( 'Review this plugin', 'stock-ticker' ); ?></a>
			</div><!-- .references -->
			<div class="disclaimer">
				<h2><?php esc_attr_e( 'Disclaimer', 'stock-ticker' ); ?></h2>
				<div class="description">
					<?php
					printf(
						'<p>' . __( 'Since %1$s version %2$s source for all stock data used in plugin is provided by %3$s. Author of Stock Ticker can not guarantee that stock prices are always accurate.', 'stock-ticker' ) . '</p>' .
						'<p>' . __( 'The information displayed by the %1$s is for informational and educational purposes only, and it is not investment advice. Seek a duly licensed professional for investment advice.', 'stock-ticker' ) . '</p>' .
						'<p>' . __( 'Author of %1$s does not accept liability or responsibility for your use of plugin, including but not limited to trading and investment results.', 'stock-ticker' ) . '</p>',
						__( 'Stock Ticker', 'stock-ticker' ),
						'3.0.0',
						'<strong>Alpha Vantage</strong>'
					);
					?>
				</div>
			</div><!-- .disclaimer -->
		</div><!-- .sidebar_container -->
	</div><!-- .stock_ticker_wrapper -->

	<div class="help">
		<div class="overview">
			<h2><?php esc_attr_e( 'Help', 'stock-ticker' ); ?></h2>
			<p><?php printf( esc_attr__( 'To insert %1$s to content, use shortcode %2$s where:', 'stock-ticker' ), esc_attr__( 'Stock Ticker', 'stock-ticker' ), '<code>[stock_ticker symbols="" show="" number_format="" decimals="" static="" speed="" class=""]</code>' ); ?>
			<p class="description"><strong><?php esc_attr_e( 'IMPORTANT', 'stock-ticker' ); ?></strong> <?php esc_attr_e( 'All shortcode parameters and values should be lowercase, except symbols which must be uppercase!', 'stock-ticker' ); ?></p>
			<dl>
				<dt class="head"><?php esc_attr_e( 'Parameter', 'stock-ticker' ); ?></dt>
				<dd class="head"><?php esc_attr_e( 'Usage', 'stock-ticker' ); ?></dd>

				<dt><code>symbols</code></dt>
				<dd><?php esc_attr_e( 'Represent an array of stock symbols (default from this settings page used if no custom set by shortcode)', 'stock-ticker' ); ?></dd>

				<dt><code>show</code></dt>
				<dd>
					<?php
					printf(
						esc_attr__( 'Can be %1$s to represent the company with %2$s, or %3$s to represent the company with %4$s', 'stock-ticker' ),
						'<code>name</code>',
						esc_attr__( 'Company Name', 'stock-ticker' ) . ' ' . esc_attr__( '(default)', 'stock-ticker' ),
						'<code>symbol</code>',
						esc_attr__( 'Stock Symbol', 'stock-ticker' )
					);
					?>
				</dd>

				<dt><code>number_format</code></dt>
				<dd>
					<?php
					printf(
						esc_attr__( 'Override default number format for values (default from this settings page used if no custom set by shortcode). Valid options are: %1$s and %2$s', 'stock-ticker' ),
						sprintf( '<code>cd</code> %1$s <em>0,000.00</em>; <code>dc</code> %1$s <em>0.000,00</em>; <code>sd</code> %1$s <em>0 000.00</em>', __( 'for', 'stock-ticker' ) ),
						sprintf( '<code>sc</code> %s <em>0 000,00</em>', __( 'for', 'stock-ticker' )
						)
					);
					?>
				</dd>

				<dt><code>decimals</code></dt>
				<dd>
					<?php
					printf(
						esc_attr__( 'Override default number of decimal places for values (default from this settings page used if no custom set by shortcode). Valid options are: %1$s and %2$s', 'stock-ticker' ),
						'<code>1</code>, <code>2</code>, <code>3</code>',
						'<code>4</code>'
					);
					?>
				</dd>

				<dt><code>static</code></dt>
				<dd>
					<?php
					printf(
						esc_attr__( 'Disables scrolling ticker and makes it static if set to %1$s or %2$s', 'stock-ticker' ),
						'<code>1</code>',
						'<code>true</code>'
					);
					?>
				</dd>
				<dt><code>prefill</code></dt>
				<dd>
					<?php
					printf(
						esc_attr__( 'To start with pre-filled instead empty ticker set to %1$s or %2$s', 'stock-ticker' ),
						'<code>1</code>',
						'<code>true</code>'
					);
					?>
				</dd>

				<dt><code>duplicate</code></dt>
				<dd>
					<?php
					printf(
						esc_attr__( 'If there are fewer items than visible on the ticker, set this to %1$s or %2$s to make it continuous', 'stock-ticker' ),
						'<code>1</code>',
						'<code>true</code>'
					);
					?>
				</dd>

				<dt><code>speed</code></dt>
				<dd>
					<?php
						esc_attr_e( 'Define the speed of ticker rendered by shortcode, different than default speed set on this global settings page', 'stock-ticker' );
					?>
				</dd>

				<dt><code>class</code></dt>
				<dd>
					<?php
						esc_attr_e( 'To customize block look and feel set custom CSS class (optional)', 'stock-ticker' );
					?>
				</dd>

			</dl>
		</div><!-- .overview -->

		<div class="exchanges">
			<div class="exchanges-supported">
				<h2><?php esc_attr_e( 'Supported Stock Exchanges', 'stock-ticker' ); ?></h2>
				<ul>
					<?php
					foreach ( $wpau_stockticker::$exchanges['supported'] as $symbol => $name ) {
						printf(
							'<li><strong>%1$s</strong> - %2$s</li>',
							$symbol,
							$name
						);
					}
					?>
				</ul>
			</div><!-- .exchanges-supported -->
			<div class="exchanges-unsupported">
				<h3><?php esc_attr_e( 'Unsupported Stock Exchanges', 'stock-ticker' ); ?></h3>
				<ul>
					<?php
					foreach ( $wpau_stockticker::$exchanges['unsupported'] as $symbol => $name ) {
						printf(
							'<li><strong>%1$s</strong> - %2$s</li>',
							$symbol,
							$name
						);
					}
					?>
				</ul>
			</div><!-- .exchanges-unsupported -->
		</div><!-- .exchanges -->
	</div><!-- .help_cell -->
</div>
<script type="text/javascript">
jQuery(document).ready(function($){
	$('.wpau-color-field').wpColorPicker();
});
</script>
