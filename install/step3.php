<!DOCTYPE html>
<html lang="es">
<head>
    <title>Instalador NetBillX</title>
    <link rel="shortcut icon" type="image/x-icon" href="img/favicon.ico">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">

    <!--[if lt IE 9]>
    <script src="http://html5shim.googlecode.com/svn/trunk/html5.js"></script>
    <![endif]-->

    <link type='text/css' href='css/style.css' rel='stylesheet'/>
    <link type='text/css' href="css/bootstrap.min.css" rel="stylesheet">
</head>

<body style='background-color: #FBFBFB;'>
	<div id='main-container'>
        <img src="img/logo.png" class="img-responsive" alt="Logo" />
        <hr>

		<div class="span12">
			<h4> Instalador NetBillX </h4>
			<?php
			if (isset($_GET['_error']) && ($_GET['_error']) == '1') {
				echo '<h4 style="color: red;"> No se pudo conectar a la base de datos, por favor asegúrese de que la información de la base de datos es correcta e inténtelo de nuevo. </h4>';
			}//

			$cururl = (((!empty($_SERVER['HTTPS']) && $_SERVER['HTTPS'] !== 'off')|| $_SERVER['SERVER_PORT'] == 443)?'https':'http').'://' . $_SERVER['HTTP_HOST'] . $_SERVER['REQUEST_URI'];
			$appurl = str_replace('/install/step3.php', '', $cururl);
			$appurl = str_replace('?_error=1', '', $appurl);
			$appurl = str_replace('/system', '', $appurl);
			?>

			<form action="step4.php" method="post">
				<fieldset>
					<legend>Conexión a la Base de Datos y Configuración del Sitio</legend>

					<div class="form-group">
						<label for="appurl">URL de la Aplicación</label>
						<input type="text" class="form-control" id="appurl" name="appurl" value="<?php echo $appurl; ?>">
						<span class='help-block'>URL de la aplicación sin la barra al final (ej. http://172.16.10.10). Mantén el valor predeterminado si no estás seguro.</span>
					</div>
					<div class="form-group">
						<label for="dbhost">Servidor de la Base de Datos</label>
						<input type="text" class="form-control" id="dbhost" required name="dbhost">
					</div>
					<div class="form-group">
						<label for="dbuser">Usuario de la Base de Datos</label>
						<input type="text" class="form-control" id="dbuser" required name="dbuser">
					</div>
					<div class="form-group">
						<label for="dbpass">Contraseña de la Base de Datos</label>
						<input type="text" class="form-control" id="dbpass" required name="dbpass">
					</div>

					<div class="form-group">
						<label for="dbname">Nombre de la Base de Datos</label>
						<input type="text" class="form-control" id="dbname" required name="dbname">
					</div>

                    <div class="form-group">
						<label for="radius"><input type="checkbox" class="form-" id="radius" name="radius" value="yes"> ¿Instalar tabla <a href="https://github.com/freedarwuin/NetBillX/wiki/FreeRadius" target="_blank">Radius</a>?</label>
						<span class='help-block'>No necesitas esto si planeas usar <a href="https://github.com/freedarwuin/NetBillX/wiki/FreeRadius-Rest" target="_blank">FreeRadius REST</a></span>
					</div>

					<button type="submit" class="btn btn-primary">Iniciar instalación</button>
				</fieldset>
			</form>
		</div>
	</div>
	<div class="footer">Copyright &copy; 2025 NetBillX. Todos los Derechos Reservados<br/><br/></div>
</body>
</html>