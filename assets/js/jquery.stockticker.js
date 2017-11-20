var stock_ticker_loaded = false;
var stock_tickers_load = function() {
	var stock_tickers = jQuery('.stock-ticker-wrapper');
	if ( 'undefined' !== typeof stock_tickers ) {
		jQuery.each(stock_tickers, function(i,v){
			var obj = jQuery(this);
			jQuery.ajax({
				type: 'post',
				dataType: 'json',
				url: stockTickerJs.ajax_url,
				data: {
					'action': 'stock_ticker_load',
					'symbols': jQuery(this).data('stockticker_symbols'),
					'show': jQuery(this).data('stockticker_show'),
					'number_format': jQuery(this).data('stockticker_number_format'),
					'decimals': jQuery(this).data('stockticker_decimals'),
					'static': jQuery(this).data('stockticker_static'),
					'class': jQuery(this).data('stockticker_class'),
					'speed': jQuery(this).data('stockticker_speed'),
					'empty': jQuery(this).data('stockticker_empty'),
					'duplicate': jQuery(this).data('stockticker_duplicate')
				},
				success: function(response) {
					if ( response.status == 'success' ) {
						console.log(response);
						stock_ticker_loaded = true;
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
	stock_tickers_load();
	var stockTickerReload = setInterval(function() {
		if ( stock_ticker_loaded ) {
			clearInterval(stockTickerReload);
		} else {
			stock_tickers_load();
		}
	}, 5000);
	// Update AlphaVantage quotes
	setTimeout(function() {
		jQuery.ajax({
			type: 'post',
			dataType: 'json',
			url: stockTickerJs.ajax_url,
			data: {
				'action': 'stock_ticker_update_quotes'
			},
			success: function(response) {
				console.log( 'Stock Ticker update quotes response: ' + response );
			}
		});
	}, 2000);

	// Short-circuit selective refresh events if not in customizer preview or pre-4.5.
	if ( 'undefined' === typeof wp || ! wp.customize || ! wp.customize.selectiveRefresh ) {
		return;
	}
	// Re-load Stock Ticker widgets when a partial is rendered.
	wp.customize.selectiveRefresh.bind( 'partial-content-rendered', function( placement ) {
		if ( placement.container ) {
			stock_tickers_load();
		}
	} );
});
