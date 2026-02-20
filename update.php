<?php

/**
 * PHP Mikrotik Billing (https://github.com/freedarwuin/NetBillX/)
 *
 * Este script es para actualizar NetBillX
 **/
session_start();
include "config.php";

if($db_password != null && ($db_pass == null || empty($db_pass))){
    // compatibilidad para versión antigua
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

$step = $_GET['step'];
$continue = true;
if (!extension_loaded('zip')) {
    $msg = "No está disponible la extensión ZIP de PHP";
    $msgType = "danger";
    $continue = false;
}


$file = pathFixer('system/cache/NetBillX.zip');
$folder = pathFixer('system/cache/NetBillX-' . basename($update_url, ".zip") . '/');

if (empty($step)) {
    $step++;
} else if ($step == 1) {
    if (file_exists($file)) unlink($file);

    // Descargar actualización
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
        $step++;
    } else {
        $msg = "Error al descargar el archivo de actualización";
        $msgType = "danger";
        $continue = false;
    }
} else if ($step == 2) {
    $zip = new ZipArchive();
    $zip->open($file);
    $zip->extractTo(pathFixer('system/cache/'));
    $zip->close();
    if (file_exists($folder)) {
        $step++;
    } else {
        $msg = "Error al extraer el archivo de actualización";
        $msgType = "danger";
        $continue = false;
    }
    // eliminar el zip descargado
    if (file_exists($file)) unlink($file);
} else if ($step == 3) {
    deleteFolder('system/autoload/');
    deleteFolder('system/vendor/');
    deleteFolder('ui/ui/');
    copyFolder($folder, pathFixer('./'));
    deleteFolder('install/');
    deleteFolder($folder);
    if (!file_exists($folder . pathFixer('/system/'))) {
        $step++;
    } else {
        $msg = "Error al instalar el archivo de actualización.";
        $msgType = "danger";
        $continue = false;
    }
} else if ($step == 4) {
    if (file_exists("system/updates.json")) {
        $db = new pdo(
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
                        // ignorar, ya existe
                    }
                }
                $dones[] = $version;
            }
        }
        file_put_contents("system/cache/updates.done.json", json_encode($dones));
    }
    $step++;
} else {
    $path = 'ui/compiled/';
    $files = scandir($path);
    foreach ($files as $file) {
        if (is_file($path . $file)) {
            unlink($path . $file);
        }
    }
    $version = json_decode(file_get_contents('version.json'), true)['version'];
    $continue = false;
}

function pathFixer($path)
{
    return str_replace("/", DIRECTORY_SEPARATOR, $path);
}

function r2($to, $ntype = 'e', $msg = '')
{

    if ($msg == '') {
        header("location: $to");
        die();
    }
    $_SESSION['ntype'] = $ntype;
    $_SESSION['notify'] = $msg;
    header("location: $to");
    die();
}

function copyFolder($from, $to, $exclude = [])
{
    $files = scandir($from);
    foreach ($files as $file) {
        if (is_file($from . $file) && !in_array($file, $exclude)) {
            if (file_exists($to . $file)) unlink($to . $file);
            rename($from . $file, $to . $file);
        } else if (is_dir($from . $file) && !in_array($file, ['.', '..'])) {
            if (!file_exists($to . $file)) {
                mkdir($to . $file);
            }
            copyFolder($from . $file . DIRECTORY_SEPARATOR, $to . $file . DIRECTORY_SEPARATOR);
        }
    }
}
function deleteFolder($path)
{
    $files = scandir($path);
    foreach ($files as $file) {
        if (is_file($path . $file)) {
            unlink($path . $file);
        } else if (is_dir($path . $file) && !in_array($file, ['.', '..'])) {
            deleteFolder($path . $file . DIRECTORY_SEPARATOR);
            rmdir($path . $file);
        }
    }
    rmdir($path);
}

?>
<!DOCTYPE html>
<html>

<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1, maximum-scale=1">
    <title>Actualizador NetBillX</title>
    <link rel="shortcut icon" href="ui/ui/images/logo.png" type="image/x-icon" />

    <link rel="stylesheet" href="ui/ui/styles/bootstrap.min.css">

    <link rel="stylesheet" href="ui/ui/fonts/ionicons/css/ionicons.min.css">
    <link rel="stylesheet" href="ui/ui/fonts/font-awesome/css/font-awesome.min.css">

    <link rel="stylesheet" href="ui/ui/styles/modern-AdminLTE.min.css">

    <?php if ($continue) { ?>
        <meta http-equiv="refresh" content="3; ./update.php?step=<?= $step ?>">
    <?php } ?>
    <style>
        ::-moz-selection {
            /* Código para Firefox */
            color: red;
            background: yellow;
        }

        ::selection {
            color: red;
            background: yellow;
        }
    </style>

</head>

<body class="hold-transition skin-blue">
    <div class="container">
        <section class="content-header">
            <h1 class="text-center">
                Actualizar NetBillX
            </h1>
        </section>

        <section class="content">
            <div class="row">
                <div class="col-md-4"></div>
                <div class="col-md-4">
                    <?php if (!empty($msgType) && !empty($msg)) { ?>
                        <div class="alert alert-<?= $msgType ?>" role="alert">
                            <?= $msg ?>
                        </div>
                    <?php } ?>
                    <?php if ($continue || $step == 5) { ?>
                        <?php if ($step == 1) { ?>
                            <div class="panel panel-primary">
                                <div class="panel-heading">Paso 1</div>
                                <div class="panel-body">
                                    Descargando actualización<br>
                                    Por favor espere....
                                </div>
                            </div>
                        <?php } else if ($step == 2) { ?>
                            <div class="panel panel-primary">
                                <div class="panel-heading">Paso 2</div>
                                <div class="panel-body">
                                    Extrayendo<br>
                                    Por favor espere....
                                </div>
                            </div>
                        <?php } else if ($step == 3) { ?>
                            <div class="panel panel-primary">
                                <div class="panel-heading">Paso 3</div>
                                <div class="panel-body">
                                    Instalando<br>
                                    Por favor espere....
                                </div>
                            </div>
                        <?php } else if ($step == 4) { ?>
                            <div class="panel panel-primary">
                                <div class="panel-heading">Paso 4</div>
                                <div class="panel-body">
                                    Actualizando base de datos...
                                </div>
                            </div>
                        <?php } else if ($step == 5) { ?>
                            <div class="panel panel-success">
                                <div class="panel-heading">Actualización Finalizada</div>
                                <div class="panel-body">
                                    NetBillX ha sido actualizado a la Versión <b><?= $version ?></b>
                                </div>
                            </div>
                            <meta http-equiv="refresh" content="5; ./?_route=dashboard">
                        <?php } ?>
                    <?php } ?>
                </div>
            </div>
        </section>
        <footer class="footer text-center">
            NetBillX por <a href="https://github.com/freedarwuin/NetBillX" rel="nofollow noreferrer noopener" target="_blank">freedarwuin</a>
        </footer>
    </div>
</body>

</html>