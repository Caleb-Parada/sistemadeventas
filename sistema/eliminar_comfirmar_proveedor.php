<?php
	session_start();
	if ($_SESSION['rol'] != 1) {

		header("location: ./");
	}
	include "../conexion.php";

	if (!empty($_POST)){

		if (empty($_POST['idproveedor'])) {
			header('location: lista_proveedores.php');
			mysqli_close($obj_conexion);
		}

		$idproveedor = $_POST['idproveedor'];
		// elimina de la tabla mas no de la base de datos.
		$query_delete = mysqli_query($obj_conexion,"UPDATE proveedor SET estatus = 0 WHERE codproveedor = $idproveedor");
		mysqli_close($obj_conexion);

		if ($query_delete == true) {

			header('location: lista_proveedores.php');

		}else{
			$alert='Error al eliminar';
		}
	}

//recogida de datos.
	if (empty($_REQUEST['id'])) {

		header('location: lista_proveedores.php');
		mysqli_close($obj_conexion);
	}else{

		$idproveedor = $_REQUEST['id'];

		$query = mysqli_query($obj_conexion,"SELECT * FROM proveedor WHERE codproveedor = $idproveedor");
		mysqli_close($obj_conexion);
		$result = mysqli_num_rows($query);

		if ($result>0) {
			while ($data=mysqli_fetch_array($query)) {
				$proveedor = $data['proveedor'];
			}
				
			}else{
				header('location: lista_proveedores.php');
		}
	}

?>

<!-- Plantilla base para todas las paginas-->
<!DOCTYPE html>
<html lang="en">
<head>
	<meta charset="UTF-8">
	<?php include "includes/scripts.php";?>
	<title>Eliminar Proveedor</title>
</head>
<body>
	<?php include "includes/header.php";?>
	<section id="container">
		<div class="data_delete">
			<div class="alert"><?php echo isset($alert) ? $alert : ''; ?></div>
			<br>
			<a href="#"><i class="fas fa-truck fa-7x" style="color: #e81818f0"></i>
			<br>	
			<br>
			<h2 style="color: #0a0909"> ¿Está seguro de eliminar el siguiente registro?</h2>
			<br>
			<p style="color: #0a0909">Nombre del proveedor: <span><?php echo $proveedor; ?></span></p>
			<form method="POST" action="">
				<input type="hidden" name="idproveedor" value="<?php echo $idproveedor; ?>">
				<a href="lista_proveedores.php" class="btn_cancel"><i class="fas fa-ban"></i> Cancelar</a>
				<button type="submit" class="btn_ok"><i class="fas fa-trash-alt"></i> Eliminar</button>
			</form>
		</div>
	</section>
	<?php include "includes/footer.php";?>
</body>
</html>