<!-- Plantilla base para todas las paginas-->
<?php
session_start();
?>
<!DOCTYPE html>
<html lang="en">
<head>
	<meta charset="UTF-8">
	<?php include "includes/scripts.php";?>
	<title>Reportes</title>
</head>
<body>
	<?php include "includes/header.php";?>
	<section id="container">
		<div class="divContainer">
			<div>
				<h1 class="titlePanelControl">Reportes de gestión</h1>
			</div>
			<div class="dashboard">
			<a href="reporte_general.php">
				<i class="fas fa-database fa-4x"></i>
				<p>
					<br>
					<strong>GENERAL</strong>
				</p>
			</a>
			<a href="#">
				<i class="fas fa-users fa-4x"></i>
				<p>
					<br>
					<strong>CLIENTES</strong>
				</p>
			</a>
			<a href="#">
				<i class="fas fa-truck fa-4x"></i>
				<p>
					<br>
					<strong>PROVEEDORES</strong>
				</p>
			</a>
			</div>
			<br>
			<div class="dashboard">
			<a href="#">
				<i class="fas fa-boxes fa-4x"></i>
				<p>
					<br>
					<strong>PRODUCTOS</strong>
				</p>
			</a>				
			<a href="#">
				<i class="fas fa-cart-arrow-down fa-4x"></i>
				<p>
					<br>
					<strong>VENTAS</strong>
				</p>
			</a>
			<a href="app/reportes/reportusuarios.php">
				<i class="fas fa-id-badge fa-4x"></i>
				<p>
					<br>
					<strong>USUARIOS</strong>
				</p>
			</a>			
			</div>
		</div>
	</section>
	<?php include "includes/footer.php";?>
</body>
</html>