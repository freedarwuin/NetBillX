<!DOCTYPE html>
<html lang="es">
<head>
<meta charset="utf-8">
<meta name="viewport" content="width=device-width, initial-scale=1">
<title>Actualizador NetBillX</title>

<link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">

<?php if ($continue) { ?>
<meta http-equiv="refresh" content="3; ./update.php?step=<?= $step ?>">
<?php } ?>

<style>
body{
    background: linear-gradient(135deg,#0d6efd,#0a58ca);
    min-height:100vh;
    display:flex;
    align-items:center;
    justify-content:center;
}
.card{
    border-radius:20px;
    box-shadow:0 10px 30px rgba(0,0,0,0.2);
}
.progress{
    height:20px;
}
</style>
</head>

<body>

<div class="container">
    <div class="row justify-content-center">
        <div class="col-md-6">

            <div class="card p-4 text-center">

                <h3 class="mb-4">Actualizar NetBillX</h3>

                <?php if (!empty($msgType) && !empty($msg)) { ?>
                    <div class="alert alert-<?= $msgType ?>">
                        <?= $msg ?>
                    </div>
                <?php } ?>

                <?php
                $progress = 0;
                if($step == 1) $progress = 25;
                if($step == 2) $progress = 50;
                if($step == 3) $progress = 75;
                if($step == 4) $progress = 90;
                if($step == 5) $progress = 100;
                ?>

                <div class="progress mb-4">
                    <div class="progress-bar progress-bar-striped progress-bar-animated"
                        role="progressbar"
                        style="width: <?= $progress ?>%">
                        <?= $progress ?>%
                    </div>
                </div>

                <?php if ($step == 1) { ?>
                    <p>📥 Descargando actualización...</p>
                <?php } elseif ($step == 2) { ?>
                    <p>📦 Extrayendo archivos...</p>
                <?php } elseif ($step == 3) { ?>
                    <p>⚙ Instalando archivos...</p>
                <?php } elseif ($step == 4) { ?>
                    <p>🗄 Actualizando base de datos...</p>
                <?php } elseif ($step == 5) { ?>
                    <div class="alert alert-success">
                        ✅ Actualización completada<br>
                        Versión instalada: <strong><?= $version ?></strong>
                    </div>
                    <meta http-equiv="refresh" content="5; ./?_route=dashboard">
                <?php } ?>

            </div>

        </div>
    </div>
</div>

</body>
</html>