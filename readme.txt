=== Stock Ticker ===
Contributors: urkekg, techwebux
Donate link: https://urosevic.net/wordpress/donate/?donate_for=stock-ticker
Tags: stock, stock ticker, sotck quote, ticker, trading, forex
Requires at least: 4.9
Tested up to: 6.2
Stable tag: 3.23.0
Requires PHP: 5.6
License: GPLv3
License URI: http://www.gnu.org/licenses/gpl-3.0.html

Easy add customizable moving or static ticker tapes with stock information for custom stock symbols.

== Description ==

A simple and easy configurable plugin for WordPress that allows you to insert stock ticker with stock price information to posts, pages, widgets or even to template files. Insertion is enabled by shortcode or multi instance widget.

Please note, stock data has been provided by [Alpha Vantage](https://www.alphavantage.co/) which does not support indexes since mid-2020!

Stock Ticker is advanced variation of [Stock Quote](https://wordpress.org/plugins/stock-quote/) plugin.

**Multisite WordPress is not supported yet**

**IMPORTANT** Stock Ticker does not have own Gutenberg Block, so you can use Shortcode Block or Common Block to insert Stock Ticker within the post/page content.

== Disclaimer ==

All stock data used in **Stock Ticker** is provided by **Alpha Vantage**, displayed for informational and educational purposes only and should not be considered as investment advise.

Author of the **Stock Ticker** plugin does not accept liability or responsibility for your use of plugin, including but not limited to trading and investment results. Along to that, author of **Stock Ticker** plugin can not guarantee that stock prices are always accurate as they are provided by 3rd party service for free.

== Features ==

* Set global set of symbols you'll use site-wide
* Configure default set of stock symbols that will be displayed in ticker inserted by empty shortcode
* Configure default presence of company as Company Name or as Stock Symbol
* Configure colours for unchanged quote, negative and positive changes with colour picker
* Disable scrolling ticker and make it static
* Define custom names for companies to be used instead of the symbols
* Define custom elements as a part of visible value
* Optional (BETA) Intraday time series for equity. Known issues because of 15min timeframe: RANGE and VOLUME are wrong. Because indexes does not have VOLUME, so for indexes and currencies TIME_SERIES_DAILY will be used

You can set custom template for visible change value. Default format is `%company% %price% %change% %changep%`. As a macro keywords you can use:

* `%exch_symbol%` - Symbol with exchange, like *NASDAQ:AAPL*
* `%symbol%` - Company symbol, like *AAPL*
* `%company%` - Company name after filtered by custom names, like *Apple Inc.*
* `%price%` - Price value, like *125.22*
* `%change%` - Change value, like *-5.53*
* `%changep%` - Change percentage, like *-4.23%*
* `%ltrade%` - Last trade day (like *2020-09-25*), which can be followed by [the PHP date format](https://www.php.net/manual/en/datetime.format.php) to customize date output, separate by pipe character, eg. *|l, jS \of F Y*

For feature requests or help [send feedback](https://urosevic.net/wordpress/plugins/stock-ticker/ "Official plugin page") or use [official WordPress support forum](https://wordpress.org/support/plugin/stock-ticker/).

== How To Use ==

You can add Stock Ticker to posts, pages or widgets by shortcode or widget (**Appearance** -> **Widgets**).

= Shortcode =
Use simple shortcode `[stock_ticker]` without any parameter in post or page, to display ticker with default settings. You can tweak single shortcode with parameters:

* `symbols` - string with single or comma separated array of stock symbols
* `show` - string that define how will company be represent on ticker; can be `name` for Company Name, or `symbol` for Stock Symbol
* `number_format` - override default number format for values (default from this settings page used if no custom set by shortcode). Valid options are: `cd` for *0.000,00*; `dc` for *0,000.00*; `sd` for *0 000.00* and `sc` for *0 000,00*
* `decimals` - override default number of decimal places for values (default from this settings page used if no custom set by shortcode). Valid values are: `1`, `2`, `3` and `4`
* `static` - (boolean) to enable static unordered list instead scroling ticker set to `1` or `true`
* `prefill` - (boolean) to start with pre-filled instead empty ticker set to `1` or `true`
* `duplicate` - (boolean) if there is less items than visible on the ticker, set this to `1` or `true` to make it continuous
* `speed` - (integer) tune speed of StockTicker block rendered by shortcode
* `class` - customize block look and feel set custom CSS class (optional)

= Examples =

* Scrolling ticker
`[stock_ticker symbols="BABA,EURGBP,LLOY.LON" show="symbol"]`
* Static unordered list
`[stock_ticker symbols="BABA,EURGBP,LLOY.LON" show="symbol" static="1"]`

== Supported Stock Exchange Markets ==

Alpha Vantage provide stock data for following stock exchange markets:

* **BOM** - Bombay Stock Exchange
* **BIT** - Borsa Italiana Milan Stock Exchange
* **TSE** - Canadian/Toronto Securities Exchange
* **FRA** - Deutsche Boerse Frankfurt Stock Exchange
* **ETR** - Deutsche Boerse Frankfurt Stock Exchange
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
* **ASX** - Australian Securities Exchange ([since since May 2020](https://twitter.com/moinzaman/status/1262522914227712000)) - eg. `ASX:MSB`
* **SGX** - Singapore Exchange ([since July 13th 2020](https://kpo-and-czm.blogspot.com/2017/11/bye-yahoo-finance-hi-alpha-vantage.html?showComment=1596075191464#c3946519402226422619)) - eg. `C29.SI`
* **NSE** - National Stock Exchange of India ([since July 2020](https://twitter.com/sachinmankapure/status/1279794312210010114)) - eg. `NSE:VB`
* **STO** - NASDAQ OMX Stockholm (since October 2021) - eg. `STO:ATCO-A`

== Installation ==

To install Stock Ticker and make initial setting to work, please follow instructions below

https://youtu.be/_tSQ5-ODVfs

1. Go to `Plugins` -> `Add New`
1. Search for `Stock Ticker` plugin
1. Install and activate `Stock Ticker`
1. Get a free API Key from [AlphaVantage.co](https://www.alphavantage.co/support/#api-key)
1. In WordPress Dashboard go to `Settings` -> `Stock Ticker`
1. Enter to field `AlphaVantage.co API Key` Alpha Vantage API Key you received in previous step (check [this screenshot](https://goo.gl/3PKxLM))
1. Enter to field `All Stock Symbols` all stock symbols you’ll use on whole website in various widgets and shortcodes, separated by comma. This field is used to fetch stock data from AlphaVantage.co API by AJAX in background. Because AV have only API to get data for single symbol, that can take a while to get. Please note, for default shortcode symbols there is still have field in Default Settings section of plugin.
1. Save settings and click button `Fetch Stock Data Now!` to initially fetch stock data to database and wait for a while until we get all symbols from AlphaVantage.co for the very first time.
1. Insert shortcode `[stock_ticker]` to page or post, or `Stock Ticker` Widget to preferred Widget Area.

== Screenshots ==

1. Global plugin settings page (version 3.2.0)
2. Widget settings
3. Stock ticker in action
4. Stock ticker in Customizer > top Widgets top
5. Stock ticker in Customizer > sidebar Widgets
6. Stock ticker through Gutenberg Shortcode block

== Hall of Fame ==

A big **thank you** goes to [@flexer](https://wordpress.org/support/users/flexer/), [@khunmax](https://wordpress.org/support/users/khunmax/), [@k2_1971](https://wordpress.org/support/users/k2_1971/) and [@vijaleshk](https://wordpress.org/support/users/vijaleshk/) who do tests with alpha versions of plugin until we finally released v3.0.0. And also important to mention [@eigood](https://wordpress.org/support/users/eigood/) who pointed me to AlphaVantage.co as a replacement for Google Finance.

== Frequently Asked Questions ==

= How to know which stock symbols to use? =

You can use standard symbols from stock exchanges supported by AlphaVantage.co.
For example, you can try with AAPL, MSFT, IBM, CSCO, GOOG, YHOO, AMZN (Apple Inc; Microsoft Corporation; International Business Machines Corporation; Cisco Systems, Inc.; Google Inc; Yahoo! Inc; Amazon.com, Inc.)
To check if AlphaVantage.co support your preferred symbol(s), you can use *Symbol Search & Test* tool on plugin settings page to search for keysords and symbols on AlphaVantage.co directly from your WordPress dashboard.

= Stock Exchange or Symbol I need does not work! =

Try to find correct symbol on AlphaVantage.co by looing for it in *Symbol Search & Test* tool. Even try alternatives or company name. If that does not help, search Alpha Vantage community forum [www.alpha-vantage.community](https://www.alpha-vantage.community/)

= How to get Dow Jones Industrial Average or other Indexes? =

Unfortunately, AlphaVantage.co no longer supports indexes (mid-2020). That is why Stock Ticker no longer provide quotes for them.

= How to get Crude Oil, Gold and other commodities? =

Unfortunately, AlphaVantage.co does not support commodities (metals, energies, grains, meats, softs). That is why Stock Ticker can't provide quotes for them.

= How to get currency exchange rate? =

Use Currency exchange symbols like `EURGBP` to get rate of `1 Euro` = `? British Pounds`
Please note, since mid-2020 AlphaVantage.co does not support anymore format `EURGBP=X` so use syntax without `=X`.

= How to get proper stock price from proper stock exchange? =

Enter symbol in format `EXCHANGE:SYMBOL` like `LON:LLOY` or `SYMBOL.EXCHANGE` like `LLOY.LON` for Lloyds Banking Group Plc from London Stock Exchange market.
Please note that AlphaVantage.co does not provide stock data always for all existing stocks.

= How to get descriptive title for currency exchange rates? =

Add to `Custom Names` legend currency exchange symbol w/o `=X` part, like:

`EURGBP;Euro (€) ⇨ British Pound Sterling (£)`

= How to add Stock Ticker to header theme file? =

Add this to your template file (you also can add custom parameters for shortcode):

`<?php echo do_shortcode('[stock_ticker]'); ?>`

= How to customize quote output? =

On Settings page for plugin you can set custom Value template. You can use macro keywords `%exch_symbol%`, `%symbol%`, `%company%`, `%price%`, `%volume%`, `%change%`, `%changep%` and `%ltrade%` mixed with HTML tags `<span>`, `<em>` and/or `<strong>`.

Default template is `%company% %price% %change% %changep%` but you can format it like:

`<span style="color:#333">%company%</span> <em>%price%</em> <strong>%change%</strong> %changep%`

= I set to show `%company%` but symbol is displayed instead =

Please note that Alpha Vantage does not provide company name in retrieved feeds. You'll need to set company name in *Custom Names* field on plugin settings page.

= How to resolve error `Unfortunately, we could not get stock quotes this time`? =

This can be temporary issue. First try to access front-end page with appended parameter `?stockticker_purge_cache=1`.

If that does not help, next try to increase *Fetch Timeout* option on general plugin settings and then visit frontend page with appended address parameter `?stockticker_purge_cache=1`.

If you still experiencing issue, please contact us through [support forum](https://wordpress.org/support/plugin/stock-ticker) and don't forget to provide URL to your website where you have inserted Stock Ticker.

= Can I get stock data for my custom code? =

Since version 3.1 of Stock Ticker you can get stock data in custom functions. Fore example:

`
<?php
if ( class_exists( 'Wpau_Stock_Ticker' ) ) {
	$stock_data = Wpau_Stock_Ticker::get_stock_from_db( 'AAPL,MSFT' );
	var_dump( $stock_data );
}
?>
`

That will return associated array for requested symbols:
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
* Fix: Checkbox options can not be disabled (`Intraday, `Auto Refresh`, `Load assets on all pages`). Thanks to @cmyee for reporting bug.
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
