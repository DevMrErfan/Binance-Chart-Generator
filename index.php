<?php
include 'src/createChart.php';

$timeframe = '1h'; // Set the timeframe for the candlestick data
$symbol = 'BTCUSDT'; // Set the cryptocurrency symbol

$candles = fetchCandlesData($symbol, $timeframe);

if ($candles !== null) {
    createCandlestickChart($candles, $symbol, $timeframe);
} else {
    echo "Failed to fetch candle data.";
}
?>
