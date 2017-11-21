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
	$current_db_ver = get_option( 'stockticker_db_ver', 0 );

	// this is the target version that we need to reach
	$target_db_ver = Wpau_Stock_Ticker::DB_VER;

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
	update_option( 'stockticker_version', $target_db_ver );

} // END function au_stockticker_update()


/**
 * Migrate pre-0.3.0 to 0.3.0 version
 */
function au_stockticker_update_routine_1() {

	// Move settings from old option to new option and delete old option
	if ( $old_option_value = get_option( 'stock_ticker_defaults' ) ) {
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

	// Add 0.3.0 options
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

	$table_name = $wpdb->prefix . 'stock_ticker_data';
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

	require_once( ABSPATH . 'wp-admin/includes/upgrade.php' );
	dbDelta( $sql );

} // END function au_stockticker_update_routine_2()

function au_stockticker_update_routine_3() {
	// Delete all transients as we don't use them anymore since 0.2.99alpha7
	global $wpdb;
	$ret = $wpdb->query( "DELETE FROM $wpdb->options WHERE option_name LIKE '%\_transient\_stockticker\_av\_%' OR option_name LIKE '%\_transient\_timeout\_stockticker\_av\_%'" );
} // END function au_stockticker_update_routine_3()
