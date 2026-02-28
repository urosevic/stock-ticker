=== Stock Ticker ===
Contributors: urkekg, techwebux
Donate link: https://urosevic.net/wordpress/donate/?donate_for=stock-ticker
Tags: stock ticker, stock, ticker, trading, forex
Requires at least: 5.2
Tested up to: 6.9.1
Stable tag: 3.26.2
Requires PHP: 7.3
License: GPLv3
License URI: https://www.gnu.org/licenses/gpl-3.0.html

Easy add customizable moving or static ticker tapes with stock information for custom stock symbols.

== Description ==

A simple and easy configurable plugin for WordPress that allows you to insert a stock ticker with stock price information to posts, pages, widgets, or even to template files. Insertion is enabled by a shortcode or multi-instance widget.

Stock data is fetched by the API from [Alpha Vantage](https://www.alphavantage.co/). You'll need an AlphaVantage.co API Key.

Stock Ticker is an advanced variation of the [Stock Quote](https://wordpress.org/plugins/stock-quote/) plugin.

**Multisite WordPress is not supported yet.**

**IMPORTANT:** Stock Ticker does not have its Gutenberg Block. You can use Shortcode Block or Common Block to insert the Stock Ticker within the post/page content.

== Disclaimer ==

All stock data used in **Stock Ticker** is provided by **Alpha Vantage**, displayed for informational and educational purposes only and should not be considered as investment advice.

As of the end of 2023, AlphaVantage limited the Free API tier to 5 requests per minute and 25 requests per day.

Before presenting stock data on your website publicly, ensure that you comply with the Alpha Vantage [Terms of Service](https://www.alphavantage.co/terms_of_service/) and have a valid commercial license!

The author of the **Stock Ticker** plugin does not accept liability or responsibility for your use of the plugin, including but not limited to trading and investment results. Additionally, the author of the **Stock Ticker** plugin can not guarantee that stock prices are always accurate, as they are provided by a third-party service for free.

== Features ==

* Set a global set of symbols you'll use site-wide.
* Configure the default set of stock symbols that will be displayed in the ticker inserted by the empty shortcode.
* Configure the default presence of the company as Company Name or as a Stock Symbol.
* Configure colours for unchanged quote, negative and positive changes with the colour picker.
* Disable scrolling ticker and make it static.
* Define custom names for companies to be used instead of the symbols.
* Define custom elements as a part of the visible value.

You can set a custom template for a visible change value. Default format is `%company% %price% %change% %changep%`. As macro keywords, you can use:

* `%exch_symbol%` - Symbol with exchange, like *NASDAQ:AAPL*
* `%symbol%` - Company symbol, like *AAPL*
* `%company%` - Company name after filtered by custom names, like *Apple Inc.*
* `%price%` - Price value, like *125.22*
* `%change%` - Change value, like *-5.53*
* `%changep%` - Change percentage, like *-4.23%*
* `%ltrade%` - Last trade day (like *2020-09-25*), which can be followed by [the PHP date format](https://www.php.net/manual/en/datetime.format.php) to customise date output, separate by pipe character, eg *|l, jS \of F Y*

For help, use [the official WordPress support forum](https://wordpress.org/support/plugin/stock-ticker/).

== How To Use ==

You can add a Stock Ticker to posts, pages or widgets by shortcode or widget (**Appearance** -> **Widgets**).

= Shortcode =

Use the simple shortcode `[stock_ticker]` without any parameters in a post or page to display the ticker with default settings. You can tweak a single shortcode with parameters:

* `symbols` - string with asingle or comma-separated array of stock symbols
* `show` - a string that defines how the company will be represented on the ticker; can be the `name` for Company Name, or a `symbol` for Stock Symbol
* `number_format` - override default number format for values (default from this settings page used if no custom set by shortcode). Valid options are: `cd` for *0.000,00*; `dc` for *0,000.00*; `sd` for *0 000.00* and `sc` for *0 000,00*
* `decimals` - override default number of decimal places for values (default from this settings page used if no custom set by shortcode). Valid values are: `1`, `2`, `3` and `4`
* `static` - (boolean) to enable static unordered list instead of scrolling ticker, set to `1` or `true`
* `prefill` - (boolean) to start with pre-filled instead of an empty ticker set to `1` or `true`
* `duplicate` - (boolean) if there are fewer items than visible on the ticker, set this to `1` or `true` to make it continuous
* `speed` - (integer) tune speed of StockTicker block rendered by shortcode
* `class` - (optional) customise block look and feel, set custom CSS class

= Examples =

* Scrolling ticker
`[stock_ticker symbols="BABA,EURGBP,LLOY.LON" show="symbol"]`
* Static unordered list
`[stock_ticker symbols="BABA,EURGBP,LLOY.LON" show="symbol" static="1"]`

== Supported Stock Exchange Markets ==

Alpha Vantage provide stock data for the following stock exchange markets:

* **BOM** - Bombay Stock Exchange
* **TSE** - Canadian/Toronto Securities Exchange
* **FRA** - Deutsche Börse Frankfurt Stock Exchange
* **ETR** - Deutsche Börse Frankfurt Stock Exchange
* **AMS** - Euronext Amsterdam
* **EBR** - Euronext Brussels
* **ELI** - Euronext Lisbon
* **EPA** - Euronext Paris
* **LON** - London Stock Exchange
* **NASDAQ** - NASDAQ Exchange
* **CPH** - NASDAQ OMX Copenhagen
* **HEL** - NASDAQ OMX Helsinki
* **ICE** - NASDAQ OMX Iceland
* **NYSE** - New York Stock Exchange
* **SHA** - Shanghai Stock Exchange
* **SHE** - Shenzhen Stock Exchange
* **TPE** - Taiwan Stock Exchange
* **TYO** - Tokyo Stock Exchange

Not supported:

* **MCX** - Moscow Exchange (since December 2018) - eg. `MCX:GAZP`
* **ASX** - Australian Securities Exchange ([since May 2020](https://twitter.com/moinzaman/status/1262522914227712000)) - eg, `ASX:MSB`
* **SGX** - Singapore Exchange ([since July 13th 2020](https://kpo-and-czm.blogspot.com/2017/11/bye-yahoo-finance-hi-alpha-vantage.html?showComment=1596075191464#c3946519402226422619)) - eg, `C29.SI`
* **NSE** - National Stock Exchange of India ([since July 2020](https://twitter.com/sachinmankapure/status/1279794312210010114)) - eg, `NSE:VB`
* **STO** - NASDAQ OMX Stockholm (since October 2021) - eg, `STO:ATCO-A`
* **BIT** - Borsa Italiana Milan Stock Exchange ([since December 2023](https://wordpress.org/support/topic/bit-not-working/)) - eg, `BIT:OLI`

== Installation ==

To install Stock Ticker and make initial settings to work, please follow the instructions below.

https://youtu.be/_tSQ5-ODVfs

1. Go to `Plugins` > `Add New`.
2. Search for the `Stock Ticker` plugin.
3. Install and activate `Stock Ticker`.
4. Get a free API Key from [AlphaVantage.co](https://www.alphavantage.co/support/#api-key).
5. In the WordPress Dashboard, navigate to `Settings` > `Stock Ticker`.
6. Enter the Alpha Vantage API Key you received in the previous step in the field `AlphaVantage.co API Key` (check [this screenshot](https://goo.gl/3PKxLM)).
7. Enter all stock symbols you'll use on the whole website in various widgets and shortcodes to the field `All Stock Symbols`, separated by a comma. This field is used to fetch stock data from AlphaVantage.co API by AJAX in the background. Because AV's free tier only offers an API to retrieve data for a single symbol, it can take some time to get. Please note that, for default shortcode symbols, a field remains in the Default Settings section of the plugin.
8. 1. Save the settings and click the `Fetch Stock Data Now!` button to initially fetch stock data into the database. Wait for a while until we receive all symbols from AlphaVantage.co for the first time.
9. Insert shortcode `[stock_ticker]` to a page or post, or the `Stock Ticker` Widget to the preferred Widget Area.

== Screenshots ==

1. Global plugin settings page (version 3.23.5)
2. Widget settings
3. Stock ticker in action
4. Stock ticker in Customizer > top Widgets top
5. Stock ticker in Customizer > sidebar Widgets
6. Stock ticker through Gutenberg Shortcode block

== Hall of Fame ==

Kudos to:

* [Patchstack](https://patchstack.com/database/vulnerability/stock-ticker) and [Wordfence](https://www.wordfence.com/threat-intel/vulnerabilities/wordpress-plugins/stock-ticker) researchers for early reporting of vulnerabilities.
* fellow alpha testers [@flexer](https://wordpress.org/support/users/flexer/), [@khunmax](https://wordpress.org/support/users/khunmax/), [@k2_1971](https://wordpress.org/support/users/k2_1971/), and [@vijaleshk](https://wordpress.org/support/users/vijaleshk/), for release v3.0.0.
* [@eigood](https://wordpress.org/support/users/eigood/), who pointed me to AlphaVantage.co as an alternative to Google Finance.
* [@rbrodrecht](https://profiles.wordpress.org/rbrodrecht/) for helping with Alpha Vantage entitlement implementation.

== Frequently Asked Questions ==

= How to know which stock symbols to use? =

You can use standard symbols from stock exchanges supported by AlphaVantage.co, such as AAPL, MSFT, IBM, CSCO, GOOG, YHOO, and AMZN (Apple Inc., Microsoft Corporation, International Business Machines Corporation, Cisco Systems, Inc., Google Inc., Yahoo! Inc., Amazon.com).

To check if AlphaVantage.co supports your preferred symbol(s), you can use the *Symbol Search & Test* tool on the plugin settings page to search for keywords and symbols on AlphaVantage.co directly from your WordPress dashboard.

= The stock exchange or symbol I need does not work! =

Try to find the correct symbol on AlphaVantage.co by looking for it in the *Symbol Search & Test* tool.  Even try alternatives or the company name. If that does not help, you can search the Alpha Vantage community forum [www.alpha-vantage.community](https://www.alpha-vantage.community/) or contact [Alpha Vantage support](https://www.alphavantage.co/support/#support).

= How to get Dow Jones Industrial Average or other Indexes? =

AlphaVantage.co does not support indexes since mid-2020.

= How to get Crude Oil, Gold and other commodities? =

Commodities are not supported by the Stock Ticker.

= How to obtain a currency exchange rate? =

Forex is not supported by the Stock Ticker.

= How to get the proper stock price from a proper stock exchange? =

Enter symbol in format `EXCHANGE:SYMBOL`, like `LON:LLOY` or `SYMBOL.EXCHANGE` like `LLOY.LON` for Lloyds Banking Group Plc from the London Stock Exchange market.

Please note that AlphaVantage.co does not always provide stock data for all existing stocks.

= How to get a descriptive title for currency exchange rates? =

Add to `Custom Names` legend currency exchange symbol w/o `=X` part, like:

`EURGBP;Euro (€) ⇨ British Pound Sterling (£)`

= How to add Stock Ticker to the header theme file? =

Add this to your template file (you can also add custom parameters for the shortcode):

`<?php echo do_shortcode('[stock_ticker]'); ?>`

= How to customise quote output? =

On the Settings page for the plugin, you can set a custom Value template. You can use macro keywords `%exch_symbol%`, `%symbol%`, `%company%`, `%price%`, `%volume%`, `%change%`, `%changep%` and `%ltrade%` mixed with HTML tags `<span>` (allowed `class` and `style` attribute), `<em>` and/or `<strong>`.

Default template is `%company% %price% %change% %changep%` but you can format it like:

`<span style="color:#333">%company%</span> <em>%price%</em> <strong>%change%</strong> %changep%`

= I set to show `%company%` but symbol is displayed instead =

Please note that Alpha Vantage does not provide the company name in retrieved feeds. You'll need to set the company name in the *Custom Names* field on the plugin settings page.

= How to resolve the error `Unfortunately, we could not get stock quotes this time`? =

This can be a temporary issue. First, try running `Fetch Stock Data Now!` on the plugin settings page.

Then try increasing the *Fetch Timeout* option in the general plugin settings and run `Fetch Stock Data Now!`.

If you continue to experience issues, please contact us through the [community support forum](https://wordpress.org/support/plugin/stock-ticker).

= Can I get stock data for my custom code? =

Since version 3.1 of Stock Ticker, you can get stock data in custom functions. For example:

`
<?php
if ( class_exists( 'Wpau_Stock_Ticker' ) ) {
	$stock_data = Wpau_Stock_Ticker::get_stock_from_db( 'AAPL,MSFT' );
	var_dump( $stock_data );
}
?>
`

That will return the associated array for the requested symbols:
`
array(2) {
  ["AAPL"]=>
  array(11) {
    ["symbol"]=>
    string(4) "AAPL"
    ["tz"]=>
    string(10) "US/Eastern"
    ["last_refreshed"]=>
    string(19) "2018-09-14 00:00:00"
    ["last_open"]=>
    string(8) "225.7500"
    ["last_high"]=>
    string(8) "226.8400"
    ["last_low"]=>
    string(8) "222.5220"
    ["last_close"]=>
    string(8) "223.8400"
    ["last_volume"]=>
    string(8) "31999289"
    ["change"]=>
    string(7) "-2.5700"
    ["changep"]=>
    string(7) "-1.1351"
    ["range"]=>
    string(19) "222.5220 - 226.8400"
  }
  ["MSFT"]=>
  array(11) {
    ["symbol"]=>
    string(4) "MSFT"
    ["tz"]=>
    string(10) "US/Eastern"
    ["last_refreshed"]=>
    string(19) "2018-09-14 00:00:00"
    ["last_open"]=>
    string(8) "113.3600"
    ["last_high"]=>
    string(8) "113.7300"
    ["last_low"]=>
    string(8) "112.4400"
    ["last_close"]=>
    string(8) "113.3700"
    ["last_volume"]=>
    string(8) "19122349"
    ["change"]=>
    string(6) "0.4600"
    ["changep"]=>
    string(6) "0.4074"
    ["range"]=>
    string(19) "112.4400 - 113.7300"
  }
}
`

== Changelog ==

= 3.26.2 (20260226) =
* Security: Patch CVE-2026-2722
* Improve: Adding Support for AlphaVantage API Entitlements (thanks @rbrodrecht)
* Improve: Readme

= 3.24.6 (20240628) =
* Fix: Stored Cross-Site Scripting discovered by Dale Mavers via Wordfence
* Change: Code simplification
* Tested: WordPress 6.5.5 with Twenty Twenty-Four 1.1 and PHP 8.3.7

= 3.24.4 (20240420) =
* Fix: API Key Tier always show as Free in plugin settings
* Tested: WordPress 6.5.2 with Twenty Twenty-Three 1.4 and PHP 8.3.6

= 3.23.5 (20231216) =
* Security: Fix XSS in shortvode() method (reported by resecured.io via patchstack)
* Tested: WordPress 6.4.2 with Twenty Twenty-Three 1.3 and PHP 8.2.13
* Change: Discard symbols that contains carret and equals sign
* Change: AlphaVantage introduced 25 requests per day for Free tier
* Change: Deprecated Premium tiers 15, 60, 120, 360, added new Premium tiers 30, 75, 150 and 1200 requests per minute
* Simplify: Plugin Settings page sidebar
* Readme: Removed BIT Italian Stock Exchange from supported by AlphaVantage

= 3.23.4 (20230810) =
* Security: Fix CSS of stockticker_load
* Tested: WordPress 6.3 with Twenty Twenty-Three 1.2 and PHP 8.2.8

= 3.23.3 (20230717) =
* Security: Fix XSS of Symbol Search & Test

= 3.23.2 (20230622) =
* Fix: webTicker jQuery library punch CPU to 100% on window resize
* Tested: WordPress 6.2.2 with Twenty Twenty-Three 1.1 and PHP 8.2.7

= 3.23.1 (20230223) =
* Security: Patch Broken Access Control
* Security: Remove URL parameter `stockticker_purge_cache` which allow unauthorised user to purge stock cache (from now purge stock cache by updating `All Stock Symbols` or running `Fetch Stock Data Now` on plugin settings page)

= 3.23.0 (20230223) =
* Security: Fix CSRF vulnerability, thanks to [Mika/Patchstack](https://patchstack.com/database/researcher/5ade6efe-f495-4836-906d-3de30c24edad)
* Tested: WordPress 6.2-beta3 with theme Twenty Twenty-Three and PHP 8.2.1

= 3.2.2 (20220102) =
* Fix: Fix bug introduced with release 3.2.1 which prevent stocks to be updated
* Tested: WordPress 5.9 and PHP 8.0.11

= 3.2.1 (20211113) =
* Fix: custom `number_format` has no effect
* Tested: WordPress 5.8.2 and PHP 7.4.24

= 3.2.0 (20201107) =
* Update: FAQ
* Improve: Help section on plugin settings page
* Improve: spelling and grammar
* Change: rename option `Auto Refresh` to `Auto Reload` to be clear what it is for
* Tested: WordPress 5.5.3 and PHP 7.4.10
* (20201002) Improve: translatable strings and update Text Domain to from `wpaust` to `stock-ticker`
* Add: *Symbol Search & Test* to help users find correct symbol notation on AlphaVantage.co
* (20200825) Add: new template keyword `%ltrade%` with customizable date format like `%ltrade|l, jS \of F Y%`

= 3.1.0.1 (20200810) =
* Tested: WordPress 5.5-RC2-48768 and PHP 7.4.1
* (20190328) Fix: infinite Bad API response introduced in release 3.1

= 3.1 (20190328) =
* Bump supported WordPress version
* Remove MCX from supported exchanges because AlphaVantage does not provide data for Moscow Stock Exchange
* Fix/Improve: Infinite loop for bad API responses
* Improve: Update disclaimer and readme
* (20181122) Fix: Infinite fetch loop
* Improve: Tier pause between symbol fetches
* Improve: Allow dash in stock symbol
* (20180916) Improve: Make Force Fetch to wait between each symbol fetch regarding to the API Tier
* Improve: Remove duplicate symbols on settings update
* Simplify: Merge 3 settings sections to single register_settings
* Improve: Move routine to extract symbol to fetch to self method `get_symbol_to_fetch()`
* Improve: Move stock data to DB to self method `data_to_db()`
* Change: Make method `get_stock_from_db()` public so user can access Stock data in DB from custom functions
* Change: Move method `sanitize_symbols()` to main class and make it public static so user can access it from custom functions
* (20180824) Add Alpha Vantage Tier option for better fetch timeout control
* Switch to GLOBAL_QUOTE API mode and eliminate requirement to calculate change amount from TIME_SERIES_DAILY and TIME_SERIES_INTRADAY
* Remove Intraday option from settings

= 3.0.5.4 (20180823) =
* Fix: Better sanitization for AllSymbols
* (20180403) Fix: Undefined index: message in wp-content/plugins/stock-ticker/stock-ticker.php on line 483
* (20180321) Fix: Division by zero in stock-ticker\stock-ticker.php on line 1259 for not fully supported indices like `^DJBWR`

= 3.0.5.3 (20180228) =
* Fix: Safer fix for Checkbox options

= 3.0.5.2 (20180228) =
* Fix: Checkbox options can not be disabled (`Intraday`, `Auto Refresh`, `Load assets on all pages`). Thanks to @cmyee for reporting bug.
* Fix: `Cache Timeout` can not be saved and always reset to zero on settings update.

= 3.0.5.1 (20180204) =
* Fix: JavaScript error `Uncaught ReferenceError: stock_tickers_load is not defined` reported by @wparold

= 3.0.5 (20180204) =
* Fix: stock price was by mistake taken from last_open instead of last_close, reported by @cartmen123
* Fix: INTRADAY option set as `BETA`
* Fix: undefined variable $symbol and $method
* Improve: description for `Intraday`
* Improve: description for `Refresh Timeout` option and rename to `Auto Refresh Timeout`
* Improve: description for `Cache Timeout` option
* Improve: add advised minimal cache timeout value, based on number of symbols in `All Stock Symbols` and `Fetch Timeout` value.
* (20180118) Add: support for TIME_SERIES_INTRADAY as optional method for refular symbols (excluding currencies and indexes)

= 3.0.4 (20171212) =
* Add: Button to `Stop Fetch` of forced fetching stock data on settings page
* Improve: Description for `Fetch Stock Data` on settings page
* Improve: Response info for `Fetch Stock Data` on settings page with link to test symbol in case of error message `Invalid API call`

= 3.0.3 (20171211) =
* Fix: Users of 0.2.99-alpha could have uncreated stock ticker table in database because of broken upgrade script in alpha versions
* Add: Admin notification for AlphaVantage.co API Key and All Stock Symbols
* (20171207) Add: Routine to stripe unsupported stock exchanges from all symbols when doing fetch from AlphaVantage.co to prevent API errors
* Add: Routine to strip symbols from unsupported stock markets from `All Stock Symbols` and `Stock Symbols` and display message about removed symbols on settings update
* Improve: sanitization for stock symbols on settings update
* Change: API Key input type set to password

= 3.0.2 (20171205) =
* Readme: add required PHP version and link profiles in Hall of Fame
* Add: Notice about not supported multisite
* Add: Activation routine to check and deactivate plugin on multisite as not supported at the moment

= 3.0.1 (20171204) =
* Fix: Plugin table has not created for fresh installations
* Fix: Stuck fetching data from AlphaVantage.co after first symbol fetch fail
* Add: Force fetching data from settings page
* Add: Fill stockticker.log only if WP_DEBUG is enabled
* Change: Log and AJAX messages wording

= 3.0.0 (20171203) =
* Release working version of plugin

== Upgrade Notice ==

= 3.0.0 =
Switch to AlphaVantage.co free API for stock data.
