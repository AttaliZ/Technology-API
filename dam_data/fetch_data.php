<?php
// fetch_data.php

$apiUrl = "https://app.rid.go.th/reservoir/api/reservoir/public/2022-01-09";

function fetchData($apiUrl) {
    $ch = curl_init();
    curl_setopt($ch, CURLOPT_URL, $apiUrl);
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
    curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
    curl_setopt($ch, CURLOPT_TIMEOUT, 10);
    $response = curl_exec($ch);
    $httpCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
    curl_close($ch);
    if ($httpCode != 200) {
        return null;
    }
    return $response;
}

$response = fetchData($apiUrl);

if ($response === null) {
    $reservoirs = []; // กรณีเกิดข้อผิดพลาด
} else {
    $data = json_decode($response, true);
    $reservoirs = [];
    if (is_array($data) && !empty($data['data'])) {
        foreach ($data['data'] as $regionData) {
            if (!empty($regionData['reservoir'])) {
                $reservoirs = array_merge($reservoirs, $regionData['reservoir']);
            }
        }
    }
}
?>
