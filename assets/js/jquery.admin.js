jQuery(document).ready(function($) {
	$('button[name="st_force_data_fetch"]').on('click', function(e){
		e.preventDefault();
		var fetch_button = this;
		// disable button
		$(fetch_button).prop('disabled',true);
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
				$('.st_force_data_fetch').html( 'Stock Ticker fetching loop has been reset. Now let we fetch data, please wait...<br />' );
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
						if ( ! response.done ) {
							// different progress character for timedout request
							if ( response.message.indexOf('Operation timed out') >= 0 ) {
								$('.st_force_data_fetch').append('-');
							} else {
								$('.st_force_data_fetch').append('+');
							}
							setTimeout(function() {
								fetchNextSymbol();
							}, 2000);
						} else {
							$('.st_force_data_fetch').append( '<br />' + response.message );
							// Enable button again when all is finished
							$(fetch_button).prop('disabled',false);
						}
					}).fail(function(response) {
						$('.st_force_data_fetch').append( '<br />' + response.message );
					});
				};
				fetchNextSymbol();
		});
	});
});