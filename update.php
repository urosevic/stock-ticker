<?php
/**
 * Run the incremental updates one by one.
 *
 * For example, if the current DB version is 3, and the target DB version is 6,
 * this function will execute update routines if they exist:
 *  - au_stockticker_update_routine_4()
 *  - au_stockticker_update_routine_5()
 *  - au_stockticker_update_routine_6()
 */

function au_stockticker_update() {
	// no PHP timeout for running updates
	set_time_limit( 0 );

	// this is the current database schema version number
	$current_db_ver = (int) get_option( 'stockticker_db_ver', 0 );

	// this is the target version that we need to reach
	$target_db_ver = (int) WPAU_STOCK_TICKER_DB_VER;

	// run update routines one by one until the current version number
	// reaches the target version number
	while ( $current_db_ver < $target_db_ver ) {
		// increment the current db_ver by one
		++$current_db_ver;

		// each db version will require a separate update function
		// for example, for db_ver 3, the function name should be solis_update_routine_3
		$func = "au_stockticker_update_routine_{$current_db_ver}";
		if ( function_exists( $func ) ) {
			call_user_func( $func );
		}

		// update the option in the database, so that this process can always
		// pick up where it left off
		update_option( 'stockticker_db_ver', $current_db_ver );
	}

	// Update plugin version number
	update_option( 'stockticker_version', WPAU_STOCK_TICKER_VER );
} // END function au_stockticker_update()

/**
 * Migrate pre-3.0.0 to 3.0.0 version
 */
function au_stockticker_update_routine_1() {
	// Move settings from old option to new option and delete old option
	$old_option_value = get_option( 'stock_ticker_defaults' );
	if ( $old_option_value ) {
		add_option( 'stockticker_defaults', $old_option_value );
		delete_option( 'stock_ticker_defaults' );
	}

	// Migrate legacy settings if still exists
	$defaults = get_option( 'stockticker_defaults' );

	if ( get_option( 'st_symbols' ) ) {
		$defaults['symbols'] = get_option( 'st_symbols' );
		delete_option( 'st_symbols' );
	}
	if ( get_option( 'st_show' ) ) {
		$defaults['show'] = get_option( 'st_show' );
		delete_option( 'st_show' );
	}
	if ( get_option( 'st_quote_zero' ) ) {
		$defaults['zero'] = get_option( 'st_quote_zero' );
		delete_option( 'st_quote_zero' );
	}
	if ( get_option( 'st_quote_minus' ) ) {
		$defaults['minus'] = get_option( 'st_quote_minus' );
		delete_option( 'st_quote_minus' );
	}
	if ( get_option( 'st_quote_plus' ) ) {
		$defaults['plus'] = get_option( 'st_quote_plus' );
		delete_option( 'st_quote_plus' );
	}

	// Add 3.0.0 options
	if ( empty( $defaults['avapikey'] ) ) {
		$defaults['avapikey'] = '';
	}
	$defaults['loading_message'] = 'Loading stock data...';
	$defaults['number_format']   = 'dc';
	$defaults['decimals']        = 2;

	// Update options
	update_option( 'stockticker_defaults', $defaults );

	// Clean alpha transients
	global $wpdb;
	$ret = $wpdb->query( "DELETE FROM $wpdb->options WHERE option_name LIKE '%\_transient\_st\_avdata\_%' OR option_name LIKE '%\_transient\_timeout\_st\_avdata\_%'" );

	// clear temporary vars
	unset( $old_option_value, $defaults );
} // END function au_stockticker_update_routine_1()

function au_stockticker_update_routine_2() {
	// Create database table for stock data caching since version 0.2.99alpha6
	global $wpdb;

	$table_name      = $wpdb->prefix . 'stock_ticker_data';
	$charset_collate = $wpdb->get_charset_collate();

	$sql = "CREATE TABLE $table_name (
		`symbol` varchar(20) NOT NULL,
		`raw` text NOT NULL,
		`last_refreshed` datetime DEFAULT '0000-00-00 00:00:00' NOT NULL,
		`tz` varchar(20) NOT NULL,
		`last_open` decimal(13,4) NOT NULL,
		`last_high` decimal(13,4) NOT NULL,
		`last_low` decimal(13,4) NOT NULL,
		`last_close` decimal(13,4) NOT NULL,
		`last_volume` int NOT NULL,
		`change` decimal(13,4) NOT NULL,
		`changep` decimal(13,4) NOT NULL,
		`range` varchar(60) DEFAULT '' NOT NULL,
		PRIMARY KEY  (`symbol`),
		UNIQUE `symbol` (`symbol`)
	) $charset_collate;";

	require_once ABSPATH . 'wp-admin/includes/upgrade.php';
	dbDelta( $sql );
} // END function au_stockticker_update_routine_2()

function au_stockticker_update_routine_3() {
	// Delete all transients as we don't use them anymore since 0.2.99alpha7
	global $wpdb;
	$ret = $wpdb->query( "DELETE FROM $wpdb->options WHERE option_name LIKE '%\_transient\_stockticker\_av\_%' OR option_name LIKE '%\_transient\_timeout\_stockticker\_av\_%'" );
} // END function au_stockticker_update_routine_3()

function au_stockticker_update_routine_4() {
	// Add id as a primary column for 0.2.99alpha10
	global $wpdb;

	$table_name = $wpdb->prefix . 'stock_ticker_data';

	// Because WordPress dbDelta missing features as noted in ticket https://core.trac.wordpress.org/ticket/40357
	// We must to run direct primary key switch
	$wpdb->query( "ALTER TABLE $table_name DROP PRIMARY KEY" );
	$wpdb->query( "ALTER TABLE $table_name ADD id INT(10) NOT NULL AUTO_INCREMENT PRIMARY KEY FIRST" );

	// Delete unused keys
	delete_option( 'stockticker_av_latest' );
	delete_option( 'stockticker_av_latest_timestamp' );
} // END function au_stockticker_update_routine_4()

function au_stockticker_update_routine_5() {
	// If fetch timeout is shorted than 4 seconds, increase timeout to 4 seconds
	$defaults = get_option( 'stockticker_defaults' );
	if ( (int) $defaults['timeout'] < 4 ) {
		$defaults['timeout'] = 4;
		update_option( 'stockticker_defaults', $defaults );
	}
} // END function au_stockticker_update_routine_5()

function au_stockticker_update_routine_6() {
	// Fix issue with skipped upgrades for DB VER from 1 to 4

	// remove legacy settings if they remain
	$old_option = get_option( 'stock_ticker_defaults' );
	if ( $old_option ) {
		if ( false !== $old_option ) {
			delete_option( 'stock_ticker_defaults' );
		}
	}

	// create stockticker table if missing
	global $wpdb;
	$table_name = $wpdb->prefix . 'stock_ticker_data';
	if ( $wpdb->get_var( "SHOW TABLES LIKE '$table_name'" ) !== $table_name ) {
		//table not in database. Create new table
		$charset_collate = $wpdb->get_charset_collate();

		$sql = "CREATE TABLE $table_name (
			`id` INT(10) NOT NULL AUTO_INCREMENT,
			`symbol` varchar(20) NOT NULL,
			`raw` text NOT NULL,
			`last_refreshed` datetime DEFAULT '0000-00-00 00:00:00' NOT NULL,
			`tz` varchar(20) NOT NULL,
			`last_open` decimal(13,4) NOT NULL,
			`last_high` decimal(13,4) NOT NULL,
			`last_low` decimal(13,4) NOT NULL,
			`last_close` decimal(13,4) NOT NULL,
			`last_volume` int NOT NULL,
			`change` decimal(13,4) NOT NULL,
			`changep` decimal(13,4) NOT NULL,
			`range` varchar(60) DEFAULT '' NOT NULL,
			PRIMARY KEY  (`id`),
			UNIQUE `symbol` (`symbol`)
		) $charset_collate;";

		require_once ABSPATH . 'wp-admin/includes/upgrade.php';
		dbDelta( $sql );
	}

	// Remove legacy transients
	au_stockticker_update_routine_3();

	// Delete temporary options used in alpha's
	delete_option( 'stockticker_av_latest' );
	delete_option( 'stockticker_av_latest_timestamp' );
} // END function au_stockticker_update_routine_6()

// Intraday setting since 3.0.5
function au_stockticker_update_routine_7() {
	$defaults = get_option( 'stockticker_defaults' );
	if ( ! isset( $defaults['intraday'] ) ) {
		$defaults['intraday'] = false;
		update_option( 'stockticker_defaults', $defaults );
	}
} // END function au_stockticker_update_routine_7()

// Fix for Uncaught ReferenceError: stock_tickers_load is not defined
function au_stockticker_update_routine_8() {
	$defaults = get_option( 'stockticker_defaults' );
	if ( ! empty( $defaults['refresh'] ) ) {
		try {
			$upload_dir = wp_upload_dir();
			$js         = sprintf( 'var stockTickers = setInterval(function(){ stocktickers_load() }, %s);', intval( $defaults['refresh_timeout'] ) * 1000 );
			file_put_contents( $upload_dir['basedir'] . '/stock-ticker-refresh.js', $js, LOCK_EX );
		} catch ( Exception $w ) {
			//
		}
	}
} // END function au_stockticker_update_routine_8()

// Remove Intraday setting
function au_stockticker_update_routine_9() {
	$defaults = get_option( 'stockticker_defaults' );
	if ( isset( $defaults['intraday'] ) ) {
		try {
			unset( $defaults['intraday'] );
			$defaults['av_api_tier'] = 5; // 5 = free
			update_option( 'stockticker_defaults', $defaults );
		} catch ( Exception $w ) {
			//
		}
	}
} // END function au_stockticker_update_routine_9()

// Rename refresh to reload
function au_stockticker_update_routine_10() {
	$defaults = get_option( 'stockticker_defaults' );

	if ( isset( $defaults['refresh'] ) || isset( $defaults['refresh_timeout'] ) ) {
		try {
			// Rename refresh_timeout -> reload_timeout
			if ( isset( $defaults['refresh_timeout'] ) && intval( $defaults['refresh_timeout'] ) > 30 ) {
				$defaults['reload_timeout'] = $defaults['refresh_timeout'];
				unset( $defaults['refresh_timeout'] );
			} else {
				$defaults['reload_timeout'] = 5 * MINUTE_IN_SECONDS;
			}
			// Rename refresh -> reload
			if ( isset( $defaults['refresh'] ) ) {
				$defaults['reload'] = $defaults['refresh'];
				unset( $defaults['refresh'] );
				// Regenerate reload JS if required.
				$upload_dir = wp_upload_dir();
				$js         = sprintf( 'var stockTickers = setInterval(function(){ stocktickers_load() }, %s);', intval( $defaults['reload_timeout'] ) * 1000 );
				file_put_contents( $upload_dir['basedir'] . '/stock-ticker-reload.js', $js, LOCK_EX );
			} else {
				$defaults['reload'] = false;
			}

			update_option( 'stockticker_defaults', $defaults );
		} catch ( Exception $w ) {
			//
		}
	}
} // END function au_stockticker_update_routine_10()

// Replace Premium tiers
function au_stockticker_update_routine_11() {
	$defaults = get_option( 'stockticker_defaults' );

	if ( isset( $defaults['av_api_tier'] ) ) {
		try {
			switch ( intval( $defaults['av_api_tier'] ) ) {
				case 15:
					$defaults['av_api_tier'] = 30;
					break;
				case 60:
					$defaults['av_api_tier'] = 75;
					break;
				case 120:
					$defaults['av_api_tier'] = 150;
					break;
				case 360:
					$defaults['av_api_tier'] = 300;
					break;
				default:
					$defaults['av_api_tier'] = 5;
					break;
			}

			update_option( 'stockticker_defaults', $defaults );
		} catch ( Exception $w ) {
			//
		}
	}
} // END function au_stockticker_update_routine_11()
