<?php
/**
 * Función para obtener datos BCV desde JSON temporal
 * @return array ['bcv_rate'=>float|null, 'bcv_history'=>array]
 */
function getBCVData() {
    $tmpFile = __DIR__ . '/bcv_data.json';
    if (!file_exists($tmpFile)) {
        return ['bcv_rate'=>null,'bcv_history'=>[]];
    }
    $data = json_decode(file_get_contents($tmpFile), true);
    if (!is_array($data)) {
        return ['bcv_rate'=>null,'bcv_history'=>[]];
    }
    return $data;
}