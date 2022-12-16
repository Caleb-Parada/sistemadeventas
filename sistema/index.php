<!-- Plantilla base para todas las paginas-->
<?php
session_start();
?>
<!DOCTYPE html>
<html lang="en">
<head>
	<meta charset="UTF-8">
	<?php include "includes/scripts.php";?>
	<title>Sistema De Ventas</title>
</head>
<body>
	<?php include "includes/header.php";?>
	<section id="container">

		<h1><i class="fas fa-warehouse fa-2x"></i> Bienvenido al sistema</h1>
		<img id="empresa" src="../img/logo_empresa.png">
		<br>
		<p id="impreso">DATOS DE LA EMPRESA</p>
		<hr>
		<p id = "Dato"><i class="fas fa-building"></i> Empresa: <span><?php echo $Empresa = "EL PUNTO PERFECTO"; ?></span></p>
		<br>
		<p id = "Dato1"><i class="fas fa-map-marker"></i> Dirección: <span><?php echo $Dirección ="B/ LA ESMERALDA"; ?></span></p>
		<br>
		<p id = "Dato2"><i class="fas fa-phone-square"></i> Contacto: <span><?php echo $Contacto ="316-289-7295"; ?></span></p>
		
	</section>
	<?php include "includes/footer.php";?>
</body>
</html>