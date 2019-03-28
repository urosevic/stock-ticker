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
		var av_api_tier = $('select[name="stockticker_defaults[av_api_tier]"]').val();
		var av_api_timeout = ( 60 / av_api_tier ) * 1000;

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
				$('.st_force_data_fetch').html( 'Reset fetching loop and fetch data again. We`ll make pause ' + (av_api_timeout / 1000) + ' second(s) between each symbol fetch. Please wait...<br /><br />' );
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
							var fetch_url = stockTickerJs.avurl;
							if ( 'wait' == response.method ) {
								$('.st_force_data_fetch').append( '[WAIT] ' + response.message + '<br />');
							} else if ( response.message.indexOf('Operation timed out') >= 0 ) {
								$('.st_force_data_fetch').append( '[Timeout] ' + response.symbol + '<br />');
							} else if ( response.message.indexOf('Invalid API call') >= 0 ) {
								$('.st_force_data_fetch').append( '[Invalid API call] ' + response.symbol + ' (<a href="' + stockTickerJs.avurl + response.symbol + '" target="_blank">test</a>)<br />');
							} else if ( response.message.indexOf('Bad API response') >= 0 ) {
								$('.st_force_data_fetch').append( '[Bad API response] ' + response.symbol + ' (<a href="' + stockTickerJs.avurl + response.symbol + '" target="_blank">test</a> OR try to prefix stock symbol with stock exchange)<br />');
							} else {
								$('.st_force_data_fetch').append( '[OK] ' + response.symbol + '<br />');
							}
							setTimeout(function() {
								fetchNextSymbol();
							}, av_api_timeout);
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