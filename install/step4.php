<?php

/**
 *  PHP Mikrotik Billing (https://github.com/freedarwuin/NetBillX/)
 *  por https://wa.me/584224512433
 **/

//error_reporting (0);
$appurl = $_POST['appurl'];
$db_host = $_POST['dbhost'];
$db_user = $_POST['dbuser'];
$db_pass = $_POST['dbpass'];
$db_name = $_POST['dbname'];
$cn = '0';
try {
    $dbh = new pdo(
        "mysql:host=$db_host;dbname=$db_name",
        "$db_user",
        "$db_pass",
        array(PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION)
    );
    $cn = '1';
} catch (PDOException $ex) {
    $cn = '0';
}

if ($cn == '1') {
    if (isset($_POST['radius']) && $_POST['radius'] == 'yes') {
        $input = '<?php

$protocol = (!empty($_SERVER["HTTPS"]) && $_SERVER["HTTPS"] !== "off" || $_SERVER["SERVER_PORT"] == 443) ? "https://" : "http://";
$host = $_SERVER["HTTP_HOST"];
$baseDir = rtrim(dirname($_SERVER["SCRIPT_NAME"]), "/\\\\");
define("APP_URL", $protocol . $host . $baseDir);

// Producción, Desarrollo, Demo
$_app_stage = "Live";

// Base de Datos NetBillX
$db_host	    = "' . $db_host . '";
$db_user        = "' . $db_user . '";
$db_pass    	= "' . $db_pass . '";
$db_name	    = "' . $db_name . '";

// Base de Datos Radius
$radius_host	    = "' . $db_host . '";
$radius_user        = "' . $db_user . '";
$radius_pass    	= "' . $db_pass . '";
$radius_name	    = "' . $db_name . '";

if($_app_stage!="Live"){
    error_reporting(E_ERROR);
    ini_set("display_errors", 1);
    ini_set("display_startup_errors", 1);
}else{
    error_reporting(E_ERROR);
    ini_set("display_errors", 0);
    ini_set("display_startup_errors", 0);
}';
    } else {
        $input = '<?php
$protocol = (!empty($_SERVER["HTTPS"]) && $_SERVER["HTTPS"] !== "off" || $_SERVER["SERVER_PORT"] == 443) ? "https://" : "http://";
$host = $_SERVER["HTTP_HOST"];
$baseDir = rtrim(dirname($_SERVER["SCRIPT_NAME"]), "/\\\\");
define("APP_URL", $protocol . $host . $baseDir);

// Producción, Desarrollo, Demo
$_app_stage = "Live";

// Base de Datos NetBillX
$db_host	    = "' . $db_host . '";
$db_user        = "' . $db_user . '";
$db_pass	    = "' . $db_pass . '";
$db_name	    = "' . $db_name . '";

if($_app_stage!="Live"){
    error_reporting(E_ERROR);
    ini_set("display_errors", 1);
    ini_set("display_startup_errors", 1);
}else{
    error_reporting(E_ERROR);
    ini_set("display_errors", 0);
    ini_set("display_startup_errors", 0);
}';
    }
    $wConfig = "../config.php";
    $fh = fopen($wConfig, 'w') or die("No se puede crear el archivo de configuración, tu servidor no soporta la función 'fopen',
	por favor crea un archivo llamado - config.php con el siguiente contenido- <br/>$input");
    fwrite($fh, $input);
    fclose($fh);
    $sql = file_get_contents('NetBillX.sql');
    $qr = $dbh->exec($sql);
    if (isset($_POST['radius']) && $_POST['radius'] == 'yes') {
        $sql = file_get_contents('radius.sql');
        $qrs = $dbh->exec($sql);
    }
} else {
    header("location: step3.php?_error=1");
    exit;
}

?>
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

<body style='background-color: #FBFBFB;'>
    <div id='main-container'>
        <img src="img/logo.png" class="img-responsive" alt="Logo" />
        <hr>

        <div class="span12">
            <h4> Instalador NetBillX </h4>
            <?php
            if ($cn == '1') {
            ?>
                <p><strong>Archivo de Configuración Creado y Base de Datos Importada.</strong><br></p>
                <form action="step5.php" method="post">
                    <fieldset>
                        <legend>Haz clic en Continuar</legend>
                        <button type='submit' class='btn btn-primary'>Continuar</button>
                    </fieldset>
                </form>
            <?php
            } elseif ($cn == '2') {
            ?>
                <p> La conexión a MySQL fue exitosa. Ocurrió un error al agregar datos en MySQL. Instalación no completada.
                    Por favor, revisa la instalación manual en github.com/freedarwuin/NetBillX/wiki o contacta en WhatsApp @freedarwuin para
                    obtener ayuda con la instalación.</p>
            <?php
            } else {
            ?>
                <p> La conexión a MySQL falló.</p>
            <?php
            }
            ?>
        </div>
    </div>

    <div class="footer">Copyright &copy; 2025 NetBillX. Todos los Derechos Reservados<br /><br /></div>
</body>

</html>