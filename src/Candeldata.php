<?php
function Candeldata($symbol, $timeframe) {
    $limit = 120;
    $url = "https://api.binance.com/api/v3/klines?symbol={$symbol}&interval={$timeframe}&limit={$limit}";
    $response = sendRequest($url);
    if ($response === false) {
        return null;
    }
    return json_decode($response, true);
}
?>
