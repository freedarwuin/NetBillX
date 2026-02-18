<!DOCTYPE html>
<html lang="es">

<head>
    <title>Instalador NetBillX</title>
    <link rel="shortcut icon" type="image/x-icon" href="img/favicon.ico">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">

    <!--[if lt IE 9]>
    <script src="http://html5shim.googlecode.com/svn/trunk/html5.js"></script>
    <![endif]-->

    <link type='text/css' href='css/style.css' rel='stylesheet' />
    <link type='text/css' href="css/bootstrap.min.css" rel="stylesheet">
</head>
<?php
$sourceDir = $_SERVER['DOCUMENT_ROOT'].'/pages_template';
$targetDir = $_SERVER['DOCUMENT_ROOT'].'/pages';

function copyDir($src, $dst) {
    $dir = opendir($src);
    if (!$dir) {
        throw new Exception("No se puede abrir el directorio: $src");
    }

    if (!file_exists($dst)) {
        if (!mkdir($dst, 0777, true)) {
            throw new Exception("Error al crear el directorio: $dst");
        }
    }

    while (false !== ($file = readdir($dir))) {
        if (($file != '.') && ($file != '..')) {
            if (is_dir($src . '/' . $file)) {
                copyDir($src . '/' . $file, $dst . '/' . $file);
            } else {
                if (!copy($src . '/' . $file, $dst . '/' . $file)) {
                    throw new Exception("Error al copiar $src/$file a $dst/$file");
                }
            }
        }
    }
    closedir($dir);
}

function removeDir($dir) {
    if (!is_dir($dir)) return;
    $objects = scandir($dir);
    foreach ($objects as $object) {
        if ($object == '.' || $object == '..') continue;
        if (is_dir($dir . '/' . $object))
            removeDir($dir . '/' . $object);
        else
            if (!unlink($dir . '/' . $object)) {
                throw new Exception("Error al eliminar el archivo: $dir/$object");
            }
    }
    if (!rmdir($dir)) {
        throw new Exception("Error al eliminar el directorio: $dir");
    }
}

try {
    if (!file_exists($sourceDir)) {
        throw new Exception("El directorio de origen no existe.");
    }

    copyDir($sourceDir, $targetDir);
    removeDir($sourceDir);

} catch (Exception $e) {
    echo 'Error: ', $e->getMessage(), "\n";
}
?>
<body style='background-color: #FBFBFB;'>
    <div id='main-container'>
        <img src="img/logo.png" class="img-responsive" alt="Logo" />
        <hr>
        <div class="span12">
            <h4> Instalador NetBillX </h4>
            <p>
                <strong>¡Felicidades!</strong><br>
                ¡Has instalado NetBillX correctamente!<br><br>
                <span class="text-danger">¡Pero espera!<br>
                    <ol>
                        <li>No olvides renombrar la carpeta <b>pages_example</b> a <b>pages</b>.<br>
                            si aún no ha sido renombrada</li>
                        <li>Activa el <a href="https://github.com/freedarwuin/NetBillX/wiki/Cron-Jobs" target="_blank">Cronjob</a> para vencimientos y recordatorios.</li>
                        <li>Revisa <a href="https://github.com/freedarwuin/NetBillX/wiki/How-It-Works---Cara-kerja" target="_blank">cómo funciona NetBillX</a></li>
                        <li><a href="https://github.com/freedarwuin/NetBillX/wiki#login-page-mikrotik" target="_blank">cómo enlazar el inicio de sesión de Mikrotik con NetBillX</a></li>
                        <li>o usa la <a href="https://github.com/freedarwuin/NetBillX-mikrotik-login-template" target="_blank">Plantilla de Login Mikrotik para NetBillX</a></li>
                    </ol>
                </span><br><br>
                Para ingresar al Portal de Administración:<br>
                Usa este enlace -
                <?php
                $cururl = (((!empty($_SERVER['HTTPS']) && $_SERVER['HTTPS'] !== 'off') || $_SERVER['SERVER_PORT'] == 443) ? 'https' : 'http') . '://' . $_SERVER['HTTP_HOST'] . $_SERVER['REQUEST_URI'];
                $appurl = str_replace('/install/step5.php', '', $cururl);
                $appurl = str_replace('/system', '', $appurl);
                echo '<a href="' . $appurl . '/admin">' . $appurl . '/admin</a>';
                ?>
                <br>
                Usuario: admin<br>
                Contraseña: admin<br>
                Por seguridad, elimina el directorio <b>install</b> dentro de la carpeta system.
            </p>
        </div>
    </div>
    <div class="footer">Copyright &copy; 2025 NetBillX. Todos los Derechos Reservados<br /><br /></div>
</body>

</html>