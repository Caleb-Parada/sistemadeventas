<?php
		session_start();
	if ($_SESSION['rol'] != 1) {

		header("location: ./");
	}
	include "../conexion.php";

	if (!empty($_POST)){
		if ($_POST['idusuario']==1) {
			header('location: lista_usuario.php');
			mysqli_close($obj_conexion);
			exit;
		}
		$idusuario = $_POST['idusuario'];

		//$query_delete = mysqli_query($obj_conexion,"DELETE FROM usuario WHERE idusuario = $idusuario");
		// elimina de la tabla mas no de la base de datos.
		$query_delete = mysqli_query($obj_conexion,"UPDATE usuario SET estatus = 0 WHERE idusuario = $idusuario");
		mysqli_close($obj_conexion);
		if ($query_delete) {
			header('location: lista_usuario.php');
		}else{
			echo "error al eliminar.";
		}
	}

//recogida de datos.
	if (empty($_REQUEST['id']) || $_REQUEST['id'] ==1 ) {

		header('location: lista_usuario.php');
		mysqli_close($obj_conexion);
	}else{

		$idusuario = $_REQUEST['id'];

		$query = mysqli_query($obj_conexion,"SELECT u.nombre, u.usuario,r.rol
												FROM usuario u
												INNER JOIN
												rol r
												ON u.rol = r.idrol
												WHERE u.idusuario = $idusuario");
		mysqli_close($obj_conexion);
		$result = mysqli_num_rows($query);

		if ($result>0) {
			while ($data=mysqli_fetch_array($query)) {
				$nombre  = $data['nombre'];
				$usuario = $data['usuario'];
				$rol     = $data['rol'];
			}
				
			}else{
				header('location: lista_usuario.php');
		}
	}

?>

<!-- Plantilla base para todas las paginas-->
<!DOCTYPE html>
<html lang="en">
<head>
	<meta charset="UTF-8">
	<?php include "includes/scripts.php";?>
	<title>Eliminar Usuario</title>
</head>
<body>
	<?php include "includes/header.php";?>
	<section id="container">
		<div class="data_delete">
			<i class="fas fa-user-times fa-7x" style="color: #e81818f0"></i>
			<br>
			<h2>¿Está seguro de eliminar el siguiente registro?</h2>
			<p>Nombre: <span><?php echo $nombre; ?></span></p>
			<p>Usuario: <span><?php echo $usuario; ?></span></p>
			<p>Tipo Usuario: <span><?php echo $rol; ?></span></p>
			<form method="POST" action="">
				<input type="hidden" name="idusuario" value="<?php echo $idusuario; ?>">
				<a href="lista_usuario.php" class="btn_cancel"><i class="fas fa-ban"></i> Cancelar</a>
				<button type="submit" class="btn_ok"><i class="fas fa-trash-alt"></i> Eliminar</button>
			</form>
		</div>
	</section>
	<?php include "includes/footer.php";?>
</body>
</html>