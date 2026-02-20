<?php

/**
 * PHP Mikrotik Billing (https://github.com/freedarwuin/NetBillX/)
 * Script de actualización NetBillX
 */

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
                    } catch (PDOException $e) {}
                }
                $dones[] = $version;
            }
        }

        file_put_contents("system/cache/updates.done.json", json_encode($dones));
    }

    $step = 5;

} else {

    $path = 'ui/compiled/';
    if (file_exists($path)) {
        $files = scandir($path);
        foreach ($files as $filec) {
            if (is_file($path . $filec)) unlink($path . $filec);
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

function copyFolder($from, $to){
    if (!file_exists($from)) return;
    $files = scandir($from);
    foreach ($files as $file) {
        if ($file != '.' && $file != '..') {
            if (is_dir($from . $file)) {
                if (!file_exists($to . $file)) mkdir($to . $file);
                copyFolder($from . $file . DIRECTORY_SEPARATOR, $to . $file . DIRECTORY_SEPARATOR);
            } else {
                if (file_exists($to . $file)) unlink($to . $file);
                rename($from . $file, $to . $file);
            }
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
<title>NetBillX ISP Core Update</title>

<link rel="shortcut icon" href="ui/ui/images/logo.png">
<link rel="stylesheet" href="ui/ui/styles/bootstrap.min.css">
<link rel="stylesheet" href="ui/ui/fonts/font-awesome/css/font-awesome.min.css">

<?php if ($continue) { ?>
<meta http-equiv="refresh" content="3; ./update.php?step=<?= $step ?>">
<?php } ?>

<style>
body{
    background:#0b1220;
    color:#e5e7eb;
    font-family: 'Segoe UI', sans-serif;
}

.network-bg{
    position:fixed;
    width:100%;
    height:100%;
    background: radial-gradient(circle at 20% 30%, #1e3a8a 0%, transparent 40%),
                radial-gradient(circle at 80% 70%, #0ea5e9 0%, transparent 40%);
    opacity:0.15;
    z-index:-1;
}

.isp-panel{
    background:#111827;
    border:1px solid #1d4ed8;
    border-radius:12px;
    padding:40px;
    box-shadow:0 0 50px rgba(0,191,255,0.2);
}

.progress{
    height:25px;
    background:#1f2937;
}

.progress-bar{
    background:linear-gradient(90deg,#00c6ff,#0072ff);
    font-weight:bold;
}

.status{
    margin-top:15px;
    font-size:16px;
}

.footer-text{
    margin-top:30px;
    font-size:12px;
    color:#6b7280;
}
</style>

</head>
<body>

<div class="network-bg"></div>

<div class="container" style="margin-top:100px;">
<div class="row">
<div class="col-md-3"></div>
<div class="col-md-6 text-center">

<div class="isp-panel">

<h3><i class="fa fa-signal text-info"></i> NETBILLX ISP CORE SYSTEM</h3>
<hr>

<?php
$progress = 0;
if ($step == 1) $progress = 20;
if ($step == 2) $progress = 40;
if ($step == 3) $progress = 70;
if ($step == 4) $progress = 90;
if ($step == 5) $progress = 100;
?>

<div class="progress">
<div class="progress-bar progress-bar-striped active" style="width: <?= $progress ?>%">
<?= $progress ?>%
</div>
</div>

<div class="status">

<?php if (!empty($msgType)) { ?>
<div class="alert alert-<?= $msgType ?>">
<?= $msg ?>
</div>
<?php } ?>

<?php if ($step == 1) { ?>
<i class="fa fa-cloud-download text-info"></i> Descargando paquete del servidor central...

<?php } elseif ($step == 2) { ?>
<i class="fa fa-archive text-warning"></i> Extrayendo actualización...

<?php } elseif ($step == 3) { ?>
<i class="fa fa-cogs text-primary"></i> Aplicando cambios al núcleo del sistema...

<?php } elseif ($step == 4) { ?>
<i class="fa fa-database text-success"></i> Ejecutando migraciones de base de datos...

<?php } elseif ($step == 5) { ?>
<div class="alert alert-success">
<i class="fa fa-check-circle"></i>
Actualización completada<br>
Versión instalada: <b><?= $version ?></b>
</div>
<meta http-equiv="refresh" content="5; ./?_route=dashboard">
<?php } ?>

</div>

<div class="footer-text">
Infraestructura NetBillX • Plataforma Profesional para ISP
</div>

</div>

</div>
<div class="col-md-3"></div>
</div>
</div>

</body>
</html>