<?php

$api_key = htmlentities($_POST['api-key']);
function prepare_dashboards($response) {
    $dashboards = $response;

    foreach ($dashboards as $index => $dashboard) {
        $dashboards[$index] = array_merge( $dashboard);
    }

    return $dashboards;
}

$ch = curl_init();

curl_setopt($ch, CURLOPT_URL, "https://api.veeqo.com/dashboard");
curl_setopt($ch, CURLOPT_RETURNTRANSFER, TRUE);
curl_setopt($ch, CURLOPT_HEADER, FALSE);

curl_setopt($ch, CURLOPT_HTTPHEADER, array(
    "Content-Type: application/json",
    "x-api-key: $api_key"
));

$response = curl_exec($ch);

$responseSize = curl_getinfo($ch, CURLINFO_SIZE_DOWNLOAD);
$time = curl_getinfo($ch, CURLINFO_TOTAL_TIME);

$err = curl_error($ch);

curl_close($ch);

$response = json_decode($response, true);

$results = [
    'dashboard' => [],
    'error' => false,
    'time' => $time,
    'responseSize' => $responseSize
];

if ($err) {
    $results['error'] = "cURL Error #:" . $err ;
} elseif(isset($response['error_messages'])) {
    $results['error'] = "API error: " . $response['error_messages'];
} else {
    $results['dashboard'] = prepare_dashboards($response);
}

return $results;