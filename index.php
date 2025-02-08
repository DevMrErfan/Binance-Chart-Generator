<?php
require_once 'src/Candeldata.php';
require_once 'src/createChart.php';

$timeframe = '1h'; // Set the timeframe for the candlestick data
$symbol = 'BTCUSDT'; // Set the cryptocurrency symbol

$candles = Candeldata($symbol, $timeframe);

if ($candles !== null) {
    createChart($candles, $symbol, $timeframe);
} else {
    echo "Failed to fetch candle data.";
}
?>
