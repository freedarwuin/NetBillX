<?php
$tmpFile = __DIR__ . '/../bcv_data.json';

if (file_exists($tmpFile)) {
    $json = file_get_contents($tmpFile);
    $data = json_decode($json, true);

    echo "<pre>";
    if ($data) {
        echo "BCV rate: " . ($data['bcv_rate'] ?? 'nulo') . "\n";
        echo "Historial:\n";
        print_r($data['bcv_history']);
    } else {
        echo "Error: JSON vacío o malformado\n";
    }
    echo "</pre>";
} else {
    echo "Error: archivo $tmpFile no existe";
}