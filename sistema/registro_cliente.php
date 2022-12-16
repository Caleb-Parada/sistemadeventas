<!-- Funcionalidad nuevo usuario. -->
<?php 
	session_start();
	if ($_SESSION['rol'] != 3) {

		header("location: ./");
	}

	include "../conexion.php";

	if (!empty($_POST)){
		
		$alert='';

		if (empty($_POST['nombre']) || empty($_POST['telefono']) || empty($_POST['direccion'])){

			$alert = '<p class="msg_error">Llena los campos obligatorios.</p>';

		}else{

			$dni        = $_POST['dni'];
			$nombre     = $_POST['nombre'];
			$telefono   = $_POST['telefono'];
			$direccion  = $_POST['direccion'];
			$usuario_id = $_SESSION['idUser'];

			$result = 0;

			if (is_numeric($dni) and $dni != 0 ) {
				
				$query = mysqli_query($obj_conexion,"SELECT * FROM cliente WHERE dni  = '$dni' ");
				$result = mysqli_fetch_array($query);
			}

			if ($result > 0) {
				$alert = '<p class="msg_error">El N° Identificación ya existe.</p>';

			}else{

				$query_insert = mysqli_query($obj_conexion,"INSERT INTO cliente(dni, nombre, telefono, direccion, usuario_id) 
													     VALUES ('$dni', '$nombre', '$telefono', '$direccion','$usuario_id')");

				if($query_insert) {
					$alert = '<p class="msg_save">Cliente guardado correctamente.</p>';
				}else{
					$alert = '<p class="msg_error">Error al guardar el cliente.</p>';
				}
			}
		}
		/*mysqli_close($obj_conexion);*/
	}


?>
<!-- Formulario nuevo usuario. -->
<!DOCTYPE html>
<html lang="en">
<head>
	<meta charset="UTF-8">
	<?php include "includes/scripts.php";?>
	<title>Registro Cliente</title>
</head>
<body>
	<?php include "includes/header.php";?>
	<!-- Plantilla de guardar cliente-->
	<section id="container">
		
		<div class="form_register">
			<h1><i class="fas fa-user-plus fa-2x"></i> Registro cliente</h1>
			<hr>
			<div class="alert"><?php echo isset($alert)? $alert: '';?></div>

				<form action="" method="post">
					<!-- label de correo -->
					<label for="dni">Identificación</label>
					<input type="number" name="dni" id="dni" placeholder="N° Identificación">
					<!-- label de nombre -->
					<label for="nombre">Nombre(*)</label>
					<input type="text" name="nombre" id="nombre" placeholder="Nombre completo">
					<!-- label de usuario-->
					<label for="telefono">Teléfono(*)</label>
					<input type="number" name="telefono" id="telefono" placeholder="Teléfono">
					<!-- label de clave-->
					<label for="direccion">Dirección(*)</label>
					<input type="text" name="direccion" id="direccion" placeholder="Dirección completa">
					<button type="submit" class="btn_save"><i class="fas fa-save"></i> Guardar cliente</button>
				</form>

			</div>

	</section>
	<?php include "includes/footer.php";?>
</body>
</html>