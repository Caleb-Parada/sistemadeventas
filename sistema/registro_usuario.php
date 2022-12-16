<!-- Funcionalidad nuevo usuario. -->
<?php 
	session_start();
	if ($_SESSION['rol'] != 1) {

		header("location: ./");
	}

	include "../conexion.php";

	if (!empty($_POST)){
		
		$alert='';

		if (empty($_POST['nombre']) || empty($_POST['correo']) || empty($_POST['usuario']) || empty($_POST['clave']) || empty($_POST['rol'])){

			$alert = '<p class="msg_error">Todos los campos son obligatorios.</p>';

		}else{

			

			$nombre = $_POST['nombre'];
			$email  = $_POST['correo'];
			$user   = $_POST['usuario'];
			$clave  = md5($_POST['clave']);
			$rol    = $_POST['rol'];
			//consulta a la tabla usuario.
			$query = mysqli_query($obj_conexion,"SELECT * FROM usuario WHERE usuario = '$user' OR correo = '$email' ");
			
			$result = mysqli_fetch_array($query);

			if ($result > 0) {
				//VERIFICA DUPLICIDAD.
				$alert = '<p class="msg_error">El correo o el usuario ya existe.</p>';
			}else{
				//REGISTRAR LOS DATOS DEL USUARIO EN LA TABLA USUARIO DEL DB.

				$query_insert = mysqli_query($obj_conexion,"INSERT INTO usuario (nombre, correo, usuario, clave, rol) VALUES ('$nombre', '$email', '$user', '$clave', '$rol')");
				//verifica si se guardo o no los datos del usuario.
			}
				if($query_insert) {
					$alert = '<p class="msg_save">Usuario creado correctamente.</p>';
				}else{
					$alert = '<p class="msg_error">Error al crear el usuario.</p>';
				}
			
		}
		mysqli_close($obj_conexion);
	}


?>
<!-- Formulario nuevo usuario. -->
<!DOCTYPE html>
<html lang="en">
<head>
	<meta charset="UTF-8">
	<?php include "includes/scripts.php";?>
	<title>Registro Usuario</title>
</head>
<body>
	<?php include "includes/header.php";?>
	<!-- Plantilla de registro de nuevo usuario -->
	<section id="container">
		
		<div class="form_register">
			<h1><i class="fas fa-user-plus fa-2x"></i> Registro usuario</h1>
			<hr>
			<div class="alert"><?php echo isset($alert)? $alert: '';?></div>

				<form action="" method="post">
					<!-- label de nombre -->
					<label for="nombre">Nombre(*)</label>
					<input type="text" name="nombre" id="nombre" placeholder="Nombre completo">
					<!-- label de correo -->
					<label for="correo">Correo electrónico(*)</label>
					<input type="email" name="correo" id="correo" placeholder="Correo electrónico">
					<!-- label de usuario-->
					<label for="usuario">Usuario(*)</label>
					<input type="text" name="usuario" id="usuario" placeholder="Usuario">
					<!-- label de clave-->
					<label for="clave">Contraseña(*)</label>
					<input type="password" name="clave" id="clave" placeholder="Contraseña">
					<!-- label de rol -->
					<label for="rol">Tipo de usuario(*)</label>
					<?php
					//extraer roles.
					include "../conexion.php";
					$query_rol  = mysqli_query($obj_conexion,"SELECT * FROM rol");
					mysqli_close($obj_conexion);
					$result_rol = mysqli_num_rows($query_rol);
					//validación en la base de datos.
					?>
					<select name="rol" id="rol">
					<?php
						if ($result_rol>0) {
							while ($rol = mysqli_fetch_array($query_rol)) {
					?>
					<option value="<?php echo $rol["idrol"]?>"><?php echo $rol["rol"]?></option>
					<?php

							}
						}
					?>
						
					</select>
					<button type="submit" class="btn_save"><i class="fas fa-save"></i> Crear usuario</button>
				<!--	<input type="submit" value="Crear usuario" class="btn_save">-->
				</form>

			</div>

	</section>
	<?php include "includes/footer.php";?>
</body>
</html>