<?php
function sendRequest($url) {
    $ch = curl_init();
    curl_setopt($ch, CURLOPT_URL, $url);
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
    $output = curl_exec($ch);
    curl_close($ch);
    return $output;
}
// Function to fetch candle data from Binance API
function fetchCandlesData($symbol, $timeframe) {
    $limit = 120; // Limit for data points
    $url = "https://api.binance.com/api/v3/klines?symbol={$symbol}&interval={$timeframe}&limit={$limit}";
    $response = sendRequest($url); // Send the HTTP request to the API
    if ($response === false) {
        return null; // Return null if the request fails
    }
    $data = json_decode($response, true); // Decode the JSON response
    return $data;
}

// Function to create a candlestick chart from fetched data
function createCandlestickChart($candles, $symbol, $timeframe) {
    // Chart dimensions and padding
    $width = 1000;
    $height = 550;
    $padding_left = 90;
    $padding_right = 40;
    $padding_top = 50;
    $padding_bottom = 90;
    
    // Chart width and height after considering padding
    $chart_width = $width - $padding_left - $padding_right;
    $chart_height = $height - $padding_top - $padding_bottom;

    // Create image for the chart
    $image = imagecreatetruecolor($width, $height);
    $background_color = imagecolorallocate($image, 25, 25, 25);
    $border_color = imagecolorallocate($image, 85, 85, 85);
    $grid_color = imagecolorallocate($image, 50, 50, 50);
    $green_color = imagecolorallocate($image, 15, 255, 15); // For bullish candles
    $red_color = imagecolorallocate($image, 255, 0, 0); // For bearish candles
    $text_color = imagecolorallocate($image, 255, 255, 255); // Text color

    // Fill background and draw border
    imagefill($image, 0, 0, $background_color);
    imagerectangle($image, $padding_left - 10, $padding_top - 10, $width - $padding_right + 10, $height - $padding_bottom + 10, $border_color);

    // Draw horizontal grid lines
    $num_grid_lines = 10;
    $grid_spacing = $chart_height / $num_grid_lines;
    for ($i = 0; $i <= $num_grid_lines; $i++) {
        $y = $padding_top + $i * $grid_spacing;
        imageline($image, $padding_left, $y, $width - $padding_right, $y, $grid_color);
    }

    // Calculate max and min values from the candles
    $max_value = max(array_column($candles, 2));
    $min_value = min(array_column($candles, 3));
    $value_range = $max_value - $min_value;

    // Set decimal places based on the value range
    $decimal_places = ($max_value < 1) ? 6 : 2;

    // Draw price labels along the left
    for ($i = 0; $i <= $num_grid_lines; $i++) {
        $price = $max_value - $i * ($value_range / $num_grid_lines);
        $y = $padding_top + $i * ($chart_height / $num_grid_lines);
        imagestring($image, 3, $padding_left - 80, $y - 7, number_format($price, $decimal_places), $text_color);
    }

    // Draw the candlestick bars
    $bar_width = $chart_width / count($candles);
    for ($i = 0; $i < count($candles); $i++) {
        $open = (float)$candles[$i][1];
        $close = (float)$candles[$i][4];
        $high = (float)$candles[$i][2];
        $low = (float)$candles[$i][3];

        $x = $padding_left + $i * $bar_width;
        $y_open = $padding_top + $chart_height - (($open - $min_value) / $value_range * $chart_height);
        $y_close = $padding_top + $chart_height - (($close - $min_value) / $value_range * $chart_height);
        $y_high = $padding_top + $chart_height - (($high - $min_value) / $value_range * $chart_height);
        $y_low = $padding_top + $chart_height - (($low - $min_value) / $value_range * $chart_height);

        // Draw the candlestick lines
        imageline($image, $x + $bar_width / 2, $y_high, $x + $bar_width / 2, $y_low, $text_color);
        
        // Fill the rectangle for the candlestick (green for bullish, red for bearish)
        if ($close >= $open) {
            imagefilledrectangle($image, $x + 2, $y_open, $x + $bar_width - 2, $y_close, $green_color);
        } else {
            imagefilledrectangle($image, $x + 2, $y_close, $x + $bar_width - 2, $y_open, $red_color);
        }
    }

    // Draw the time labels at the bottom
    $time_interval = 10;
    for ($i = 0; $i < count($candles); $i += $time_interval) {
        $time = date('H:i', $candles[$i][0] / 1000); // Convert timestamp to time
        $x = $padding_left + $i * $bar_width + $bar_width / 4;
        imagestring($image, 3, $x, $height - $padding_bottom + 15, $time, $text_color);
    }

    // Add the chart title with symbol and timeframe
    imagestring($image, 5, $padding_left, 15, "Symbol: $symbol | Interval: $timeframe", $text_color);

    // Footer text with credits
    $footer_text = "@CodeCraftersTeam / @DevErfi";
    $font_size = 8;
    $text_width = imagefontwidth($font_size) * strlen($footer_text);
    $x_position = ($width - $text_width) / 2;
    $y_position = $height - 40;
    imagestring($image, $font_size, $x_position, $y_position, $footer_text, $text_color);

    // Save the image to a PNG file
    imagepng($image, 'chart.png');
    imagedestroy($image); // Free up memory
}

// Define timeframe and symbol for the chart
$timeframe = '1h';
$symbol = 'BTCUSDT';

// Fetch candle data for the given symbol and timeframe
$candles = fetchCandlesData($symbol, $timeframe);

// Create the chart with the fetched data
createCandlestickChart($candles, $symbol, $timeframe);
?>
