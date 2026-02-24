<?php

class bcv_rate
{
    public function getWidget()
    {
        global $ui;

        // ===== Ruta absoluta al JSON =====
        $tmpFile = realpath(__DIR__ . '/../../system/bcv_data.json');

        $bcv_rate = null;
        $bcv_history = [];

        echo "<pre>DEBUG BCV\n";

        if ($tmpFile === false) {
            echo "❌ No se pudo resolver la ruta del JSON.\n";
        } else {
            echo "📂 Leyendo archivo JSON en: $tmpFile\n";

            if (!file_exists($tmpFile)) {
                echo "❌ Archivo no encontrado.\n";
            } else {
                $json = file_get_contents($tmpFile);
                if ($json === false) {
                    echo "❌ Error al leer el archivo JSON.\n";
                } else {
                    $data = json_decode($json, true);
                    if ($data === null) {
                        echo "❌ JSON inválido o vacío.\n";
                    } else {
                        $bcv_rate = $data['bcv_rate'] ?? null;
                        $bcv_history = $data['bcv_history'] ?? [];

                        echo "✅ Datos leídos correctamente:\n";
                        echo "bcv_rate: $bcv_rate\n";
                        echo "bcv_history:\n";
                        print_r($bcv_history);
                    }
                }
            }
        }

        echo "</pre>\n";

        // ===== Asignar variables al UI/Smarty =====
        $ui->assign([
            'bcv_rate'    => $bcv_rate,
            'bcv_history' => $bcv_history
        ]);

        // ===== Retornar el tpl del widget =====
        return $ui->fetch('widget/bcv_rate.tpl');
    }
}