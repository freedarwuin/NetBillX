<?php
$url = "https://ve.dolarapi.com/v1/dolares/oficial";

$ch = curl_init();
curl_setopt($ch, CURLOPT_URL, $url);
curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
$data = curl_exec($ch);

if(curl_errno($ch)){
    echo "Error cURL: " . curl_error($ch);
    exit;
}

curl_close($ch);

echo "<pre>";
var_dump($data);
echo "</pre>";

$json = json_decode($data, true);
echo "Tasa BCV: " . $json['promedio'];
