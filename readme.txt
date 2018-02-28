=== Stock Ticker ===
Contributors: urkekg
Donate link: https://urosevic.net/wordpress/donate/?donate_for=stock-ticker
Tags: widget, stock, ticker, securities, quote, financial, finance, exchange, bank, market, trading, investment, stock symbols, stock quotes, forex, nasdaq, nyse, wall street
Requires at least: 4.0.0
Tested up to: 4.9.4
Stable tag: 3.0.5.3
Requires PHP: 5.5
License: GPLv3 or later
License URI: http://www.gnu.org/licenses/gpl-3.0.html

Easy add customizable moving or static ticker tapes with stock information for custom stock symbols.

== Description ==

A simple and easy configurable plugin for WordPress that allows you to insert stock ticker with stock price information to posts, pages, widgets or even to template files. Insertion is enabled by shortcode or multi instance widget.

Please note, stock data has been provided by [Alpha Vantage](https://www.alphavantage.co/)

Stock Ticker is advanced variation of [Stock Quote](https://wordpress.org/plugins/stock-quote/) plugin.

**Multisite WordPress is not supported jet**

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

For feature requests or help [send feedback](https://urosevic.net/wordpress/plugins/stock-ticker/ "Official plugin page") or use support forum on WordPress.

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
`[stock_ticker symbols="BABA,.DJI,EURGBP=X,LON:FFX" show="symbol"]`
* Static unordered list
`[stock_ticker symbols="BABA,.DJI,EURGBP=X,LON:FFX" show="symbol" static="1"]`

== Supported Stock Exchange Markets ==

Alpha Vantage provide stock data for following stock exchange markets:

* **ASX** - Australian Securities Exchange
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
* **MCX** - Moscow Exchange
* **NASDAQ** - NASDAQ Exchange
* **CPH** - NASDAQ OMX Copenhagen
* **HEL** - NASDAQ OMX Helsinki
* **ICE** - NASDAQ OMX Iceland
* **STO** - NASDAQ OMX Stockholm
* **NSE** - National Stock Exchange of India
* **NYSE** - New York Stock Exchange
* **SGX** - Singapore Exchange
* **SHA** - Shanghai Stock Exchange
* **SHE** - Shenzhen Stock Exchange
* **TPE** - Taiwan Stock Exchange
* **TYO** - Tokyo Stock Exchange

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

1. Global plugin settings page
2. Widget settings
3. Stock ticker in action

== Hall of Fame ==

A big **thank you** goes to [@flexer](https://wordpress.org/support/users/flexer/), [@khunmax](https://wordpress.org/support/users/khunmax/), [@k2_1971](https://wordpress.org/support/users/k2_1971/) and [@vijaleshk](https://wordpress.org/support/users/vijaleshk/) who do tests with alpha versions of plugin until we finally released v3.0.0. And also important to mention [@eigood](https://wordpress.org/support/users/eigood/) who pointed me to AlphaVantage.co as a replacement for Google Finance.

== Frequently Asked Questions ==

= How to know which stock symbols to use? =

You can use standard symbols from supported stock exchanges.
To start with you can try with AAPL,MSFT,IBM,CSCO,GOOG,YHOO,AMZN (Apple Inc; Microsoft Corporation; International Business Machines Corporation; Cisco Systems, Inc.; Google Inc; Yahoo! Inc; Amazon.com, Inc.)

= How to get Dow Jones Industrial Average or other Indexes? =

Since version 3.0.0 we use Alpha Vantage, which support Indexes. To get quote for index, simply add symbol as `.DJI` (or `^DJI` if you prefer).

= How to get currency exchange rate? =

Use Currency symbols like `EURGBP=X` to get rate of `1 Euro` = `? British Pounds`

= How to get Crude Oil, Gold and other commodities? =

Unfortunately, Alpha Vantage does not provide data for commodities (metals, energies, grains, meats, softs). That is why Stock Ticker can't provide quotes for them.

= How to get proper stock price from proper stock exchange? =

Enter symbol in format `EXCHANGE:SYMBOL` like `LON:FFX` for FairFX Group PLC from London Stock Exchange market.

= Stock Exchange or Symbol I need does not work! =

If Stock Exchange or symbol you need does not work (like `BVMF:BVMF3`), first look for your symbol on [Yahoo! Finance](https://finance.yahoo.com) and try to use symbol Yahoo put in parenthesys (like `BVMF3.SA` for our example, or `^BSESN` for index of Bombay Stock Exchange of India).

If you already set proper symbol but no data got fetched from AlphaVantage, please verify that Alpha Vantage have data for your symbol. If they don't support symbol you need, feel free to ask AlphaVantage support to include it to their data set by community forum [www.alpha-vantage.community](https://www.alpha-vantage.community/)

= How to get descriptive title for currency exchange rates? =

Add to `Custom Names` legend currency exchange symbol w/o `=X` part, like:

`EURGBP;Euro (€) ⇨ British Pound Sterling (£)`

= How to add Stock Ticker to header theme file? =

Add this to your template file (you also can add custom parameters for shortcode):

`<?php echo do_shortcode('[stock_ticker]'); ?>`

= How to customize quote output? =

On Settings page for plugin you can set custom Value template. You can use macro keywords `%exch_symbol%`, `%symbol%`, `%company%`, `%price%`, `%volume%`, `%change%` and `%changep%` mixed with HTML tags `<span>`, `<em>` and/or `<strong>`.

Default template is `%company% %price% %change% %changep%` but you can format it like:

`<span style="color:#333">%company%</span> <em>%price%</em> <strong>%change%</strong> %changep%`

= I set to show `%company%` but symbol is displayed instead =

Please note that Alpha Vantage does not provide company name in retrieved feeds. You'll need to set company name in *Custom Names* field on plugin settings page.

= How to resolve error `Unfortunately, we could not get stock quotes this time`? =

This can be temporary problem. First try to access front-end page with appended parameter `?stockticker_purge_cache=1`.

If that does not help, next try to increase *Fetch Timeout* option on general plugin settings and then visit frontend page with appended address parameter `?stockticker_purge_cache=1`.

If you still experiencing issue, please contact us through [support forum](https://wordpress.org/support/plugin/stock-ticker) and don't forget to provide URL to your website where you have inserted Stock Ticker.

== Changelog ==
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

= 0.2.99-alpha11 (20171203) =
* Change: Add to plugin table ID as primary key column
* Change: Switch back to TIME_SERIES_DAILY because INTRADAY fail with currencies
* Fix: Plugin version in updater script

= 0.2.99-alpha10 (20171130) =
* Change: AlphaVantage function from TIME_SERIES_DAILY to TIME_SERIES_INTRADAY to get more acucrate date
* Clean: Remove console.log debugging from JavaScript

= 0.2.99-alpha9 (20171121) =
* Fix: Update script
* Fix: recognize zero Volume and use it from previous day
* Fix: Domain name for AV website
* Update: Disclaimer
* Clean: Remove not needed and commented out code
* Add: Plugin log file

= 0.2.99-alpha8 (20171120) =
* Revert: bring back cache timeout so we can have dellay between finished fetching and next round start
* Add: routine to pause fetching of next round until cache timeout expire
* Improve: do not mark symbol as fetched if AlphaVantage return some error (timeout or similar)
* Fix: broken options on multisite WordPress

= 0.2.99-alpha7 (20171118) =
* Remove: cahe timeout option because now stock data have been fetched in background and stored in database
* Change: packaged data from alphavantage
* Add: store stock data to database after success fetch
* Fix: currency get INF change_p during weekend

= 0.2.99-alpha6 (20171108) =
* Add: Create table `stock_ticker_data` to store stock data to DB by cron instead of the loading on page load
* Fix: default settings conversion from 0.2.3 to 0.2.99alpha5
* Change: preparation for multisite WP

= 0.2.99-alpha5 (20171011) =
* Fix: Number format and Decimal places setting are not saved

= 0.2.99-alpha4 (20171010) =
* Fix: Fatal error: Using $this when not in object context

= 0.2.99-alpha3 (20171009) =
* Add: plugin update script
* Add: customizable default 'Loading...' message
* Add: customizable number format and decimal places (globally, per shortcode and per widget)
* Add: fallback when AlphaVantage.co return second day with zeros
* Change: rewrite settings page
* Change: better handling update cache
* Change: option to unlink quotes because with AlphaVantage.co we don't have where to link
* Fix: typos

= 0.2.99-alpha2 (20170926) =
* Change: AlphaVantage update quotes action after 2s (instead 5s) after documentready
* Change: Prevent ticker loader to display 'Unfortunately...' message until we have stock data
* Add: List of Stock Exchanges supported by AlphaVantage API

= 0.2.99-alpha1 (20170926) =
* Change: Remove Google and use AlphaVantage.co API

= 0.2.3 (20170520) =
* (20170606) Fix: ticker initialization does not work on some GoDaddy hosts, so add ticker re-initialization on 5 second interval
* Add: option to load core JavaScript assets on all pages
* Change: fix coding standard to WordPress-Core
* (20170520) Fix: Undefined offset: 1 in wp-content/plugins/stock-ticker/stock-ticker.php on line 318

= 0.2.2 (20170224) =
* Fix: Update webTicker jQuery plugin to v2.2.0 and fix bug with second line for 20+ items in ticker with wide more than 10k pixels

= 0.2.1.1 (20160806) =
* Fix: Because of AJAXed requests, forced skip cache with URL parameter does not work. Now delete all Stock Ticker transients on demand (URL parameter `stockticker_purge_cache`).

= 0.2.1 (20160729) =
* Test compatibility with WordPress 4.6 and TwentySixteen 1.1
* Change: Move custom class from `stock_ticker` to `stock-ticker-wrapper` class element
* Add: Selective refresh for widgets
* Add: Speed parameter to widget
* Enhance: Optimize main CSS and fix cut-off ticker content because of widget list item top padding set in some themes

= 0.2.0.1 (20160627) =
* (20160627) Add nofollow rel for quote links in ticker
* (20160608) Add prefilled and duplicate options
* (20160523) Add error catch for failed write to file in settings

= 0.2.0 (20160522) =
* (20160522) Change: Class name to CamelCase
* Change: Make unique settings section ID's
* (20160222) Add: Ticker Speed option to tune scrolling speed (pixels per second).
* Add: Default values for integer parameters during sanitization.
* Fix: Fetch timeout setting not sanitized on update.
* (20160218) Add: Options to enable and set timeout for auto refresh
* (20160127) Add: Log when new feed is fetched if WordPress debug is enabled.
* Remove: Deprecated constant WPAU_STOCK_TICKER_CACHE_TIMEOUT
* Enhance: Description for some options on global settings page.
* (20160119) Fix: Rename class `newsticker` to `stockticker`, `tickercontainer` to `stock-ticker-container` and  JS object `webTicker` to `stockTicker` to prevent clash with other newsticker libraries
* Fix: Shortcode ticker echoed before content
* Enhance: Rearange Settings page and remove loading of external paypal assets
* (20151103) Enhance AJAX call with status messages
* Remove: colours selectors fromwidget and shortcode; make only global colours
* Add: Load content through AJAX to be friendly with caching plugins
* Add: Cache custom styles based on options provided at plugin settings page
* Add: Option `class` for shortcode and widget to set custom CSS class and customize block
* Optimize: minify front JavaScript

= 0.1.7.1 (20151106) =
* Fix: wording on Settings page - typo for `mesage` and resource name from `Yahoo` to `Google`

= 0.1.7 (20151102) =
* Add: Option to set fetch timeout and tune/increase time to retrieve data from Google server on slow servers
* Add: URL parameter stockticker_purge_cache to force fetching live data from Google
* Add: Display WP Error Message as HTML comment when 'Unfortunately...' message is displayed, to help with debugging issue
* Change: Fetch data from Google through HTTPS connection
* Update: FAQ section

= 0.1.6 (20150804) =
* Add: Settings values sanitization
* Add: Link to community Support forum and Donate to plugin links in row on Plugins page
* Change: Value template on Settings page changed to textarea
* Change: Timeout field on Settings page changed to HTML5 number field
* Change: Ticker ID length reduced fro 8 to 4 characters
* Change: Move all core methods inside class
* Make code fully compliant to WordPress Coding Standard
* Update FAQ

= 0.1.5.1 (20150801) =
* Fix: Widget not initialized on PHP <5.3

= 0.1.5 (20150723) =
* Add: Option to set custom template for visible change value (global plugin settings)

= 0.1.4.8 (20150607) =
* Fix: Make available to work with our Stock Quote plugin

= 0.1.4.7 (20150415) =
* Add: Google Disclaimer to Settings page and README file (Other Notes)
* Add: Provide alternative for inline quotes with new plugin Stock Quote

= 0.1.4.6 (20150331) =
* Add: (20150331) Strip HTML tags from shortcode symbol parameter
* Add: (20150308) Set UL container padding to 0 (to avoid cut-off in some themes)

= 0.1.4.5 (20150308) =
* Fix: (20150308) Custom quote colours in static block
* Fix: (20150218) Set exact name of class to get class vars
* Add: (20150308) Option to disable link to Google Finance on each stock quote.
* Add: (20150122) Support for custom company names in format EXCHANGE:SYMBOL
* Add: (20150308) Non-minified webticker jQuery library
* Improve: (20150308) Ticker style LESS
* Test: (20150308) on WordPress 4.2-alpha-31677 and Twenty Fifteen

= 0.1.4.4 (20150110) =
* Add: Option to display static stock ticker as unordered list instead scrolling ticker.
* Fix: Same widget output because cached widget.
* Fix: Prevent `No data` ticker by converting wrong encoded characters in Google feed to single-byte ISO-8859-1

= 0.1.4.3 =
* Fix: Add stock exchange code to symbol link to prevent mixing stocks like CVE:CXB instead ASX:CXB
* Fix: Add special character replacement to support symbols with amps like NSE:M&M
* Fix: Cache safe widget in Customizer - preview immediately after inserting widget to widget area

= 0.1.4.2 =
* Fix: broken support for PHP pre-5.3 introduced in previous release: syntax error, unexpected T_PAAMAYIM_NEKUDOTAYIM, expecting ')'

= 0.1.4.1 =
* Fix: Previous update does not output in Enfold theme
* Fix: Prevent jumping by displaying unordered list before output become scrolling ticker
* Change: Add change value and change percent for currency exchange rates
* Change: Remove option to toggle custom company name because Google Finance does not have company name returned in JSON
* Add: More default Custom Names (^DJI and EURGBP=X)
* Add: Option to set custom style for ticker item (font family, weight, size)

= 0.1.4 =
* Change: Deprecated Yahoo! Finance as source (violating the Terms of Service of Yahoo with regards to the used data), replaced with Google Finance
* Change: No more Volume info in quote tooltip (as Google Finance does not provide that data)
* Change: Link chart on Google Finance instead Yahoo Finance
* Tested on WordPress 4.0+

= 0.1.3 =
* Fix: correct placement for shortcode output buffer
* Fix: ignored custom error message from settings page
* Change: remove dashicons requirement and use default Yahoo Finance down/up symbols
* Change: class for error message from .minus to .error
* Improvement: ignore symbol case for custom names matching
* Cleanup disabled parts of code, tiny optimizations

= 0.1.2 =
* Fix: missing argument on settings page for do_settings_fields()
* Change: replace jQuery stock renderer with native WordPress/PHP functions
* Change: strip null change, change percent and volume for currencies
* Optimize: move default settings to single wp_options entry
* Add: settings: timeout to cache downloaded quotes
* Add: settings: message to show when no quote can be downloaded
* Add: settings: field for custom company names and option to enable custom names

= 0.1.1.1 =
* Move: generated CSS and JS to footer
* Remove: ajax setup from stock-ticker.js library
* Optimize: minify stock-ticker.js library

= 0.1.1 =
* Add: stock parser message when fail fetching quotes
* Fix: initializing widget syntax error: unexpected T_FUNCTION
* Remove: closing PHP tags

= 0.1.0 =
* Initial public release

= 0.0.9 =
* Private release
* Improved reusable jQuery code

= 0.0.8 =
* Fix: usable colour picker in widgets after add new widget (before widget save)

= 0.0.7 =
* Add: configurable widget
* Add: help section to settings page

= 0.0.6 =
* Add: settings page

= 0.0.5 =
* Add: shortcode option show - what to dsplay in ticker (company name or stock symbol)

= 0.0.4 =
* Add: shortcode option for custom symbols set
* Add: shortcode option for custom colours (zero/minus/plus)

= 0.0.3 =
* Add: shortcode with embedded options

= 0.0.2 =
* packaged JS code to WordPress plugin

= 0.0.1 =
* developed JavaScript code for parsing stock data

== Upgrade Notice ==

= 3.0.0 =
Switch to AlphaVantage.co free API for stock data.

= 0.1.2 =
Because we changed default options to single wp_options entry, after upgrade old defaults should be transformed to single entry. You can set custom names on settings page.

= 0.1.1 =
Fixed error for websites that run on PHP <5.3.0

= 0.1.0 =
Initial public release
