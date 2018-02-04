jQuery(document).ready(function($) {
	var cache_timeout_advisor = function() {
		// Cache Timeout advisor
		var symbols_arr = $('input[name="stockticker_defaults[all_symbols]"]').val().toString().split(',');
		var min_cache_timeout = ( symbols_arr.length * $('input[name="stockticker_defaults[timeout]"]').val() ) * 1.50;
		$('#fetch_timeout_advisor .min').html( Math.ceil( min_cache_timeout ) );
	};
	// Append advisor placeholder
	$('input[name="stockticker_defaults[cache_timeout]"]').after('&nbsp;<span id="fetch_timeout_advisor">at least <span class="min"></span></span>');
	// Update advisor on load
	cache_timeout_advisor();
	// Update advisor on change
	$('input[name="stockticker_defaults[timeout]"], input[name="stockticker_defaults[all_symbols]"]').on('change click keydown', function(ev){
		cache_timeout_advisor();
	});
	// Fetch Magic
	$('button[name="st_force_data_fetch_stop"]').on('click', function(e){
		e.preventDefault();
		$(this).data('stop','true');
	});
	$('button[name="st_force_data_fetch"]').on('click', function(e){
		e.preventDefault();
		var fetch_button = this;
		var fetch_button_stop = $('button[name="st_force_data_fetch_stop"]');
		// disable button
		$(fetch_button).prop('disabled',true);
		$(fetch_button_stop).addClass('enabled');
		/* First reset fetching loop */
		$.ajax({
			type: 'post',
			dataType: 'json',
			async: true,
			url: stockTickerJs.ajax_url,
			data: {
				'action': 'stockticker_purge_cache'
			}
		}).done( function(response) {
				// Update log container
				$('.st_force_data_fetch').html( 'Reset fetching loop and fetch data again. Please wait...<br /><br />' );
				function fetchNextSymbol() {
					/* Then do AJAX request */
					$.ajax({
						type: 'post',
						dataType: 'json',
						async: true,
						url: stockTickerJs.ajax_url,
						data: {
							'action': 'stockticker_update_quotes'
						}
					}).done(function(response) {
						if ( ! response.done && 'true' != $(fetch_button_stop).data('stop') ) {
							// different progress character for timedout request
							if ( response.message.indexOf('Operation timed out') >= 0 ) {
								$('.st_force_data_fetch').append( '[Timeout] ' + response.symbol + '<br />');
							} else if ( response.message.indexOf('Invalid API call') >= 0 ) {
								var fetch_url = stockTickerJs.avurl;
								if ( 'intraday' == response.method ) {
									fetch_url = stockTickerJs.avurli;
								}
								$('.st_force_data_fetch').append( '[Invalid API call] ' + response.symbol + ' (<a href="' + fetch_url + response.symbol + '" target="_blank">test</a>)<br />');
							} else {
								$('.st_force_data_fetch').append( '[OK] ' + response.symbol + '<br />');
							}
							setTimeout(function() {
								fetchNextSymbol();
							}, 2000);
						} else {
							if ( response.message != 'DONE' ) {
								$('.st_force_data_fetch').append( '<br />[' + response.symbol + '] ' + response.message );
							} else {
								$('.st_force_data_fetch').append( '<br />DONE' );
							}
							// Enable button again when all is finished
							$(fetch_button).prop('disabled',false);
							$(fetch_button_stop).removeClass('enabled').data('stop','false');
						}
					}).fail(function(response) {
						$('.st_force_data_fetch').append( '<br />[Error] ' + response.message );
					});
				};
				fetchNextSymbol();
		});
	});
});