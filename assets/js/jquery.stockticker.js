var stockticker_loaded = false;
var stocktickers_load = function() {
	var stocktickers = jQuery('.stock-ticker-wrapper');
	if ( 'undefined' !== typeof stocktickers ) {
		jQuery.each(stocktickers, function(i,v){
			var obj = jQuery(this);
			jQuery.ajax({
				type: 'post',
				dataType: 'json',
				url: stockTickerJs.ajax_url,
				data: {
					'action': 'stockticker_load',
					'symbols': jQuery(this).data('stockticker_symbols'),
					'show': jQuery(this).data('stockticker_show'),
					'number_format': jQuery(this).data('stockticker_number_format'),
					'decimals': jQuery(this).data('stockticker_decimals'),
					'static': jQuery(this).data('stockticker_static'),
					'class': jQuery(this).data('stockticker_class'),
					'speed': jQuery(this).data('stockticker_speed'),
					'empty': jQuery(this).data('stockticker_empty'),
					'duplicate': jQuery(this).data('stockticker_duplicate'),
					'nonce': stockTickerJs.nonce
				},
				success: function(response) {
					if ( response.status == 'success' ) {
						stockticker_loaded = true;
						obj.html(response.message);
						if ( ! obj.data('stockticker_static') ) {
							jQuery(obj).find('.stock_ticker').stockTicker({ startEmpty:jQuery(obj).data('stockticker_empty'), duplicate:jQuery(obj).data('stockticker_duplicate'), speed:jQuery(obj).data('stockticker_speed') });
						}
					}
				}
			});
		});
	}
};

jQuery(document).ready(function() {
	stocktickers_load();
	var stockTickerReload = setInterval(function() {
		if ( stockticker_loaded ) {
			clearInterval(stockTickerReload);
		} else {
			stocktickers_load();
		}
	}, 5000);
	// Update AlphaVantage quotes
	setTimeout(function() {
		jQuery.ajax({
			type: 'post',
			dataType: 'json',
			async: true,
			url: stockTickerJs.ajax_url,
			data: {
				'action': 'stockticker_update_quotes',
				'nonce': stockTickerJs.nonce
			}
		}).done(function(response){
			console.log( 'Stock Ticker update quotes response: ' + response.message );
		});
	}, 2000);

	// Short-circuit selective refresh events if not in customizer preview or pre-4.5.
	if ( 'undefined' === typeof wp || ! wp.customize || ! wp.customize.selectiveRefresh ) {
		return;
	}
	// Re-load Stock Ticker widgets when a partial is rendered.
	wp.customize.selectiveRefresh.bind( 'partial-content-rendered', function( placement ) {
		if ( placement.container ) {
			stocktickers_load();
		}
	} );
});
