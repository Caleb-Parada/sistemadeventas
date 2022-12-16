<?php
	session_start();
	if ($_SESSION['rol'] != 3) {

		header("location: ./");
	}
	include "../conexion.php";

	if (!empty($_POST)){

		if (empty($_POST['idcliente'])) {
			header('location: lista_clientes.php');
			mysqli_close($obj_conexion);
		}

		$idcliente = $_POST['idcliente'];

		//$query_delete = mysqli_query($obj_conexion,"DELETE FROM usuario WHERE idusuario = $idusuario");
		// elimina de la tabla mas no de la base de datos.
		$query_delete = mysqli_query($obj_conexion,"UPDATE cliente SET estatus = 0 WHERE idcliente = $idcliente");
		mysqli_close($obj_conexion);

		if ($query_delete) {

			header('location: lista_clientes.php');

		}else{
			$alert='<p class="msg_error">Error al eliminar cliente.</p>';
		}
	}

//recogida de datos.
	if (empty($_REQUEST['id'])) {

		header('location: lista_clientes.php');
		mysqli_close($obj_conexion);
	}else{

		$idcliente = $_REQUEST['id'];

		$query = mysqli_query($obj_conexion,"SELECT * FROM cliente WHERE idcliente = $idcliente");
		mysqli_close($obj_conexion);
		$result = mysqli_num_rows($query);

		if ($result>0) {
			while ($data=mysqli_fetch_array($query)) {
				$dni    = $data['dni'];
				$nombre = $data['nombre'];
			}
				
			}else{
				header('location: lista_clientes.php');
		}
	}

?>

<!-- Plantilla base para todas las paginas-->
<!DOCTYPE html>
<html lang="en">
<head>
	<meta charset="UTF-8">
	<?php include "includes/scripts.php";?>
	<title>Eliminar Cliente</title>
</head>
<body>
	<?php include "includes/header.php";?>
	<section id="container">
		<div class="data_delete">
			<div class="alert"><?php echo isset($alert) ? $alert : ''; ?></div>
			<h2>¿Está seguro de eliminar el siguiente registro?</h2>
			<p>Identificación: <span><?php echo $dni; ?></span></p>
			<p>Nombre del cliente: <span><?php echo $nombre; ?></span></p>
			<form method="POST" action="">
				<input type="hidden" name="idcliente" value="<?php echo $idcliente; ?>">
				<a href="lista_clientes.php" class="btn_cancel"><i class="fas fa-ban"></i> Cancelar</a>
				<button type="submit" class="btn_ok"><i class="fas fa-trash-alt"></i> Eliminar</
			</form>
		</div>
	</section>
	<?php include "includes/footer.php";?>
</body>
</html>