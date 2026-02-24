<?php
// system/widgets/bcv_rate_debug.php
$bcvFile = __DIR__ . '/../bcv_data.json';
$bcvData = file_exists($bcvFile) ? json_decode(file_get_contents($bcvFile), true) : [];
$bcv_rate    = $bcvData['bcv_rate'] ?? null;
$bcv_history = $bcvData['bcv_history'] ?? [];

?>
<!DOCTYPE html>
<html lang="es">
<head>
<meta charset="UTF-8">
<title>BCV Depuración</title>
</head>
<body>
<h2>💱 Tasa BCV (DEPURACIÓN)</h2>

<?php if ($bcv_rate !== null): ?>
    <p><strong>bcv_rate:</strong> <?php echo $bcv_rate; ?></p>
<?php else: ?>
    <p style="color:red;">bcv_rate NO está definido</p>
<?php endif; ?>

<?php if (!empty($bcv_history)): ?>
    <p><strong>bcv_history (<?php echo count($bcv_history); ?> registros):</strong></p>
    <ul>
    <?php foreach ($bcv_history as $day): ?>
        <li>Fecha: <?php echo $day['rate_date']; ?> — Tasa: <?php echo $day['rate']; ?> — Cambio: <?php echo $day['change']; ?></li>
    <?php endforeach; ?>
    </ul>
<?php else: ?>
    <p style="color:red;">bcv_history NO tiene registros</p>
<?php endif; ?>
</body>
</html>