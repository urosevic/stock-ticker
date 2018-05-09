<?php
if ( empty( $_GET['fetch'] ) ) {
	return;
}
define('WP_USE_THEMES', false);
require( dirname(dirname(dirname(dirname( __FILE__ )))) . '/wp-blog-header.php' );
$Wpau_Stock_Ticker = new Wpau_Stock_Ticker();
$data = $Wpau_Stock_Ticker->get_alphavantage_quotes();
header("HTTP/1.1 200 OK");
header('Content-Type: application/json');
echo json_encode($data);
