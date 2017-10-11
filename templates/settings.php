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

?>
<div class="wrap" id="stock_ticker_settings">
	<h2><?php esc_attr_e( 'Stock Ticker Settings', 'wpaust' ); ?></h2>
	<em>Plugin version: <?php echo WPAU_STOCK_TICKER_VER; ?></em>
	<div class="stock_ticker_wrapper">
		<div class="content_cell">
			<form method="post" action="options.php">
				<?php settings_fields( 'wpaust_default' ); ?>
				<?php settings_fields( 'wpaust_advanced' ); ?>
				<?php do_settings_sections( 'wpau_stock_ticker' ); ?>
				<?php submit_button(); ?>
			</form>
		</div><!-- .content_cell -->

		<div class="sidebar_container">
			<a href="https://urosevic.net/wordpress/donate/?donate_for=stock-ticker" class="aust-button paypal_donate" target="_blank">Donate</a>
			<br />
			<a href="https://wordpress.org/plugins/stock-ticker/faq/" class="aust-button" target="_blank">FAQ</a>
			<br />
			<a href="https://wordpress.org/support/plugin/stock-ticker" class="aust-button" target="_blank">Community Support</a>
			<h2><?php esc_attr_e( 'Free Tip', 'wpaust' ); ?></h2>
			<p>If you wish to insert quotes as inline elements in your posts or pages, consider using our related plugin <a href="https://wordpress.org/plugins/stock-quote/" target="_blank">Stock Quote</a>.</p>
		</div><!-- .sidebar_container -->
	</div><!-- .stock_ticker_wrapper -->

	<div class="help">
		<h2><?php esc_attr_e( 'Help', 'wpaust' ); ?></h2>
		<p><?php printf( esc_attr__( 'You also can use shortcode %s where:', 'wpaust' ), '<code>[stock_ticker symbols="" show="" static="" nolink="" speed="" class=""]</code>' ); ?>
			<ul>
				<li><code>symbols</code> <?php esc_attr_e( 'represent array of stock symbols (default from this settings page used if no custom set by shortcode)', 'wpaust' ); ?></li>
				<li><code>show</code> <?php printf( esc_attr__( 'can be %1$s to represent company with Company Name (default), or %2$s to represent company with Stock Symbol', 'wpaust' ), '<code>name</code>', '<code>symbol</code>' ); ?></li>
				<li><code>static</code> <?php printf( esc_attr__( 'disables scrolling ticker and makes it static if set to %1$s or %2$s', 'wpaust' ), '<code>1</code>', '<code>true</code>' ); ?></li>
				<li><code>nolink</code> <?php printf( esc_attr__( 'to disable link of quotes to Google Finance page set to %1$s or %2$s', 'wpaust' ), '<code>1</code>', '<code>true</code>' ); ?></li>
				<li><code>prefill</code> <?php printf( esc_attr__( 'to start with pre-filled instead empty ticker set to %1$s or %2$s', 'wpaust' ), '<code>1</code>', '<code>true</code>' ); ?></li>
				<li><code>duplicate</code> <?php printf( esc_attr__( 'if there is less items than visible on the ticker, set this to %1$s or %2$s to make it continuous', 'wpaust' ), '<code>1</code>', '<code>true</code>' ); ?></li>
				<li><code>speed</code> <?php echo esc_attr__( 'define speed of ticker rendered by shortcode block, different that default speed set on this global settings page', 'wpaust' ); ?></li>
				<li><code>class</code> <?php echo esc_attr__( 'to customize block look and feel set custom CSS class (optional)', 'wpaust' ); ?></li>
			</ul>
		</p>
		<h2><?php esc_attr_e( 'Disclaimer', 'wpaust' ); ?></h2>
		<p class="description">Data for Stock Ticker has provided by Google Finance and per their disclaimer,
	it can only be used at a noncommercial level. Please also note that Google has stated
	Finance API as deprecated and has no exact shutdown date.</p>
	<blockquote>
		<a href="http://www.google.com/intl/en-US/googlefinance/disclaimer/#disclaimers">Google Finance Disclaimer</a><br />
		<br />
		Data is provided by financial exchanges and may be delayed as specified
		by financial exchanges or our data providers. Google does not verify any
		data and disclaims any obligation to do so.
		<br />
		Google, its data or content providers, the financial exchanges and
		each of their affiliates and business partners (A) expressly disclaim
		the accuracy, adequacy, or completeness of any data and (B) shall not be
		liable for any errors, omissions or other defects in, delays or
		interruptions in such data, or for any actions taken in reliance thereon.
		Neither Google nor any of our information providers will be liable for
		any damages relating to your use of the information provided herein.
		As used here, “business partners” does not refer to an agency, partnership,
		or joint venture relationship between Google and any such parties.
		<br />
		You agree not to copy, modify, reformat, download, store, reproduce,
		reprocess, transmit or redistribute any data or information found herein
		or use any such data or information in a commercial enterprise without
		obtaining prior written consent. All data and information is provided “as is”
		for personal informational purposes only, and is not intended for trading
		purposes or advice. Please consult your broker or financial representative
		to verify pricing before executing any trade.
		<br />
		Either Google or its third party data or content providers have exclusive
		proprietary rights in the data and information provided.
		<br />
		Please find all listed exchanges and indices covered by Google along with
		their respective time delays from the table on the left.
		<br />
		Advertisements presented on Google Finance are solely the responsibility
		of the party from whom the ad originates. Neither Google nor any of its
		data licensors endorses or is responsible for the content of any advertisement
		or any goods or services offered therein.
		</blockquote>
	</div><!-- .help_cell -->
</div>
<script type="text/javascript">
jQuery(document).ready(function($){
	$('.wpau-color-field').wpColorPicker();
});
</script>
