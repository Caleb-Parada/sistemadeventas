<!-- Funcionalidad nuevo usuario. -->
<?php 
	session_start();
	if ($_SESSION['rol'] != 1) {

		header("location: ./");
	}

	include "../conexion.php";

	if (!empty($_POST)){
		
		$alert='';

		if (empty($_POST['proveedor']) || empty($_POST['contacto']) || empty($_POST['telefono']) || empty($_POST['direccion'])){

			$alert = '<p class="msg_error">Llena los campos obligatorios. (*)</p>';

		}else{

			$proveedor    = $_POST['proveedor'];
			$contacto     = $_POST['contacto'];
			$telefono     = $_POST['telefono'];
			$direccion    = $_POST['direccion'];
			$usuario_id   = $_SESSION['idUser'];

			
			$query_insert = mysqli_query($obj_conexion,"INSERT INTO proveedor(proveedor, contacto, telefono, direccion, usuario_id) 
													     VALUES ('$proveedor', '$contacto', '$telefono', '$direccion','$usuario_id')");

				if($query_insert) {
					$alert = '<p class="msg_save">Proveedor guardado correctamente.</p>';
				}else{
					$alert = '<p class="msg_error">Error al guardar el proveedor.</p>';
				}
		}
		/*mysqli_close($obj_conexion);*/
	}


?>
<!-- Formulario nuevo proveedor. -->
<!DOCTYPE html>
<html lang="en">
<head>
	<meta charset="UTF-8">
	<?php include "includes/scripts.php";?>
	<title>Registro Proveedor</title>
</head>
<body>
	<?php include "includes/header.php";?>
	<!-- Plantilla de guardar cliente-->
	<section id="container">
		
		<div class="form_register">
			<h1><i class="fas fa-truck fa-2x"></i> Registro proveedor</h1>
			<hr>
			<div class="alert"><?php echo isset($alert)? $alert: '';?></div>

				<form action="" method="post">
					<!-- label de nombre -->
					<label for="proveedor">Proveedor(*)</label>
					<input type="text" name="proveedor" id="proveedor" placeholder="Nombre del proveedor">
					<!-- label de correo -->
					<label for="contacto">Contacto(*)</label>
					<input type="text" name="contacto" id="contacto" placeholder="Nombre del contacto">
					<!-- label de usuario-->
					<label for="telefono">Teléfono(*)</label>
					<input type="number" name="telefono" id="telefono" placeholder="Teléfono">
					<!-- label de clave-->
					<label for="direccion">Dirección(*)</label>
					<input type="text" name="direccion" id="direccion" placeholder="Dirección completa">
					<button type="submit" class="btn_save"><i class="fas fa-save"></i> Guardar Proveedor</button>
				</form>

			</div>

	</section>
	<?php include "includes/footer.php";?>
</body>
</html>