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
            $passed = '';
            $ltext = '';
            if (version_compare(PHP_VERSION, '7.2.0') >= 0) {
                $ltext .= 'Para ejecutar NetBillX necesitas al menos la versión de PHP 7.2.0, tu versión actual de PHP es: ' . PHP_VERSION . " Prueba <strong>---APROBADA---</strong><br/>";
                $passed .= '1';
            } else {
                $ltext .= 'Para ejecutar NetBillX necesitas al menos la versión de PHP 7.2.0, tu versión actual de PHP es: ' . PHP_VERSION . " Prueba <strong>---FALLIDA---</strong><br/>";
                $passed .= '0';
            }

            if (extension_loaded('PDO')) {
                $ltext .= 'PDO está instalado en tu servidor: ' . "Prueba <strong>---APROBADA---</strong><br/>";
                $passed .= '1';
            } else {
                $ltext = 'PDO está instalado en tu servidor: ' . "Prueba <strong>---FALLIDA---</strong><br/>";
                $passed .= '0';
            }

            if (extension_loaded('pdo_mysql')) {
                $ltext .= 'El controlador PDO MySQL está habilitado en tu servidor: ' . "Prueba <strong>---APROBADA---</strong><br/>";
                $passed .= '1';
            } else {
                $ltext .= 'El controlador PDO MySQL no está habilitado en tu servidor: ' . "Prueba <strong>---FALLIDA---</strong><br/>";
                $passed .= '0';
            }

            if ($passed == '111') {
                echo ("<br/> $ltext <br/> ¡Genial! Prueba de sistema completada. Puedes ejecutar NetBillX en tu servidor. Haz clic en Continuar para el siguiente paso.
				<br><br>
                <a href=\"update.php\" class=\"btn btn-warning\">Actualizar Sistema Desde PHPMixBill</a>
                <br><br><br><br>
				<a href=\"step3.php\" class=\"btn btn-primary\">Continuar con la Instalación de NetBillX</a><br><br>");
            } else {
                echo ("<br/> $ltext <br/> Lo sentimos. Los requisitos de NetBillX no están disponibles en tu servidor.
				Contáctanos en Telegram <a href=\"https://t.me/freedarwuin\">@NetBillX</a> con este código- $passed o contacta al administrador de tu servidor
				<br><br>
				<a href=\"#\" class=\"btn btn-primary disabled\">Corrige el Problema para Continuar</a>");
            }
            ?>
        </div>
    </div>
    <div class="footer">Copyright &copy; 2025 NetBillX. Todos los Derechos Reservados<br /><br /></div>
</body>

</html>