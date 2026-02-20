<?php

/**
 * PHP Mikrotik Billing (https://github.com/freedarwuin/NetBillX/)
 *
 * Este script es para actualizar NetBillX
 **/
session_start();
include "config.php";

if($db_password != null && ($db_pass == null || empty($db_pass))){
    $db_pass = $db_password;
}

if (empty($update_url)) {
    $update_url = 'https://github.com/freedarwuin/NetBillX/archive/refs/heads/master.zip';
}

if(isset($_REQUEST['update_url']) && !empty($_REQUEST['update_url'])){
    $update_url = $_REQUEST['update_url'];
    $_SESSION['update_url'] = $update_url;
}

if(isset($_SESSION['update_url']) && !empty($_SESSION['update_url']) && $_SESSION['update_url'] != $update_url){
    $update_url = $_SESSION['update_url'];
}

if (!isset($_SESSION['aid']) || empty($_SESSION['aid'])) {
    r2("./?_route=login&You_are_not_admin", 'e', 'No eres administrador');
}

set_time_limit(-1);

if (!is_writeable(pathFixer('system/cache/'))) {
    r2("./?_route=community", 'e', 'La carpeta system/cache/ no tiene permisos de escritura');
}
if (!is_writeable(pathFixer('.'))) {
    r2("./?_route=community", 'e', 'La carpeta web no tiene permisos de escritura');
}

$step = $_GET['step'] ?? null;
$continue = true;

if (!extension_loaded('zip')) {
    $msg = "No está disponible la extensión ZIP de PHP";
    $msgType = "danger";
    $continue = false;
}

$file = pathFixer('system/cache/NetBillX.zip');
$folder = pathFixer('system/cache/NetBillX-' . basename($update_url, ".zip") . '/');

if (empty($step)) {
    $step = 1;

} else if ($step == 1) {

    if (file_exists($file)) unlink($file);

    $fp = fopen($file, 'w+');
    $ch = curl_init($update_url);
    curl_setopt($ch, CURLOPT_POST, 0);
    curl_setopt($ch, CURLOPT_CONNECTTIMEOUT, 600);
    curl_setopt($ch, CURLOPT_TIMEOUT, 600);
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
    curl_setopt($ch, CURLOPT_FOLLOWLOCATION, true);
    curl_setopt($ch, CURLOPT_FILE, $fp);
    curl_exec($ch);
    curl_close($ch);
    fclose($fp);

    if (file_exists($file)) {
        $step = 2;
    } else {
        $msg = "Error al descargar el archivo de actualización";
        $msgType = "danger";
        $continue = false;
    }

} else if ($step == 2) {

    $zip = new ZipArchive();
    if ($zip->open($file) === TRUE) {
        $zip->extractTo(pathFixer('system/cache/'));
        $zip->close();
    }

    if (file_exists($folder)) {
        $step = 3;
    } else {
        $msg = "Error al extraer el archivo de actualización";
        $msgType = "danger";
        $continue = false;
    }

    if (file_exists($file)) unlink($file);

} else if ($step == 3) {

    deleteFolder('system/autoload/');
    deleteFolder('system/vendor/');
    deleteFolder('ui/ui/');

    copyFolder($folder, pathFixer('./'));

    deleteFolder('install/');
    deleteFolder($folder);

    if (!file_exists($folder . pathFixer('/system/'))) {
        $step = 4;
    } else {
        $msg = "Error al instalar el archivo de actualización.";
        $msgType = "danger";
        $continue = false;
    }

} else if ($step == 4) {

    if (file_exists("system/updates.json")) {

        $db = new PDO(
            "mysql:host=$db_host;dbname=$db_name",
            $db_user,
            $db_pass,
            array(PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION)
        );

        $updates = json_decode(file_get_contents("system/updates.json"), true);
        $dones = [];

        if (file_exists("system/cache/updates.done.json")) {
            $dones = json_decode(file_get_contents("system/cache/updates.done.json"), true);
        }

        foreach ($updates as $version => $queries) {
            if (!in_array($version, $dones)) {
                foreach ($queries as $q) {
                    try {
                        $db->exec($q);
                    } catch (PDOException $e) {
                        // ignorar si ya existe
                    }
                }
                $dones[] = $version;
            }
        }

        file_put_contents("system/cache/updates.done.json", json_encode($dones));
    }

    $step = 5;

} else {

    $path = 'ui/compiled/';
    $files = scandir($path);
    foreach ($files as $filec) {
        if (is_file($path . $filec)) {
            unlink($path . $filec);
        }
    }

    $version = json_decode(file_get_contents('version.json'), true)['version'];
    $continue = false;
}

function pathFixer($path){
    return str_replace("/", DIRECTORY_SEPARATOR, $path);
}

function r2($to, $ntype = 'e', $msg = ''){
    if ($msg == '') {
        header("location: $to");
        die();
    }
    $_SESSION['ntype'] = $ntype;
    $_SESSION['notify'] = $msg;
    header("location: $to");
    die();
}

function copyFolder($from, $to, $exclude = []){
    $files = scandir($from);
    foreach ($files as $file) {
        if (is_file($from . $file) && !in_array($file, $exclude)) {
            if (file_exists($to . $file)) unlink($to . $file);
            rename($from . $file, $to . $file);
        } else if (is_dir($from . $file) && !in_array($file, ['.', '..'])) {
            if (!file_exists($to . $file)) mkdir($to . $file);
            copyFolder($from . $file . DIRECTORY_SEPARATOR, $to . $file . DIRECTORY_SEPARATOR);
        }
    }
}

function deleteFolder($path){
    if (!file_exists($path)) return;
    $files = scandir($path);
    foreach ($files as $file) {
        if ($file != '.' && $file != '..') {
            if (is_dir($path . $file)) {
                deleteFolder($path . $file . DIRECTORY_SEPARATOR);
            } else {
                unlink($path . $file);
            }
        }
    }
    rmdir($path);
}

?>
<!DOCTYPE html>
<html>
<head>
<meta charset="utf-8">
<meta name="viewport" content="width=device-width, initial-scale=1">
<title>Actualizador NetBillX</title>

<link rel="shortcut icon" href="ui/ui/images/logo.png">
<link rel="stylesheet" href="ui/ui/styles/bootstrap.min.css">
<link rel="stylesheet" href="ui/ui/fonts/font-awesome/css/font-awesome.min.css">
<link rel="stylesheet" href="ui/ui/styles/modern-AdminLTE.min.css">

<?php if ($continue) { ?>
<meta http-equiv="refresh" content="3; ./update.php?step=<?= $step ?>">
<?php } ?>

</head>

<body class="hold-transition skin-blue">

<div class="container" style="margin-top:60px;">
    <div class="row">
        <div class="col-md-3"></div>
        <div class="col-md-6">

            <div class="box box-primary">
                <div class="box-header text-center">
                    <h3 class="box-title">
                        <i class="fa fa-refresh"></i> Actualizando NetBillX
                    </h3>
                </div>

                <div class="box-body text-center">

                    <?php
                    $progress = 0;
                    if ($step == 1) $progress = 20;
                    if ($step == 2) $progress = 40;
                    if ($step == 3) $progress = 70;
                    if ($step == 4) $progress = 90;
                    if ($step == 5) $progress = 100;
                    ?>

                    <div class="progress">
                        <div class="progress-bar progress-bar-striped active"
                             style="width: <?= $progress ?>%">
                            <?= $progress ?>%
                        </div>
                    </div>

                    <br>

                    <?php if (!empty($msgType)) { ?>
                        <div class="alert alert-<?= $msgType ?>">
                            <?= $msg ?>
                        </div>
                    <?php } ?>

                    <?php if ($step == 1) { ?>
                        <h4><i class="fa fa-download text-primary"></i> Descargando actualización...</h4>

                    <?php } elseif ($step == 2) { ?>
                        <h4><i class="fa fa-file-archive-o text-warning"></i> Extrayendo archivos...</h4>

                    <?php } elseif ($step == 3) { ?>
                        <h4><i class="fa fa-cogs text-info"></i> Instalando archivos...</h4>

                    <?php } elseif ($step == 4) { ?>
                        <h4><i class="fa fa-database text-purple"></i> Actualizando base de datos...</h4>

                    <?php } elseif ($step == 5) { ?>
                        <div class="alert alert-success">
                            <h4><i class="fa fa-check"></i> Actualización completada</h4>
                            Versión instalada: <b><?= $version ?></b>
                        </div>
                        <meta http-equiv="refresh" content="5; ./?_route=dashboard">
                    <?php } ?>

                </div>
            </div>

        </div>
        <div class="col-md-3"></div>
    </div>
</div>

</body>
</html>