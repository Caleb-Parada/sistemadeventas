<?php 
	
	session_start();
	if($_SESSION['rol'] != 2)
	{
		header("location: ./");
	}

	include "../conexion.php";

	if(!empty($_POST))
	{
		$alert='';
		if(empty($_POST['proveedor']) ||empty($_POST['contacto']) || empty($_POST['telefono']) || empty($_POST['direccion']))
		{
			$alert='<p class="msg_error">Llena los campos obligatorios.</p>';
		}else{

			$idproveedor = $_POST['id'];
			$proveedor   = $_POST['proveedor'];
			$contacto    = $_POST['contacto'];
			$telefono    = $_POST['telefono'];
			$direccion   = $_POST['direccion'];


			$sql_update = mysqli_query($obj_conexion,"UPDATE proveedor
													  SET proveedor = '$proveedor', contacto ='$contacto',telefono='$telefono',direccion='$direccion'
													  WHERE codproveedor = $idproveedor");

				if($sql_update){
					$alert='<p class="msg_save">Proveedor actualizado correctamente.</p>';
				}else{
					$alert='<p class="msg_error">Error al actualizar el proveedor.</p>';
			}
		}
	}

	//Mostrar Datos
	if(empty($_REQUEST['id']))
	{
		header('Location: lista_proveedores.php');
		mysqli_close($obj_conexion);
	}
	$idproveedor = $_REQUEST['id'];

	$sql= mysqli_query($obj_conexion,"SELECT * FROM proveedor WHERE codproveedor = $idproveedor AND estatus = 1 ");
	mysqli_close($obj_conexion);
	$result_sql = mysqli_num_rows($sql);

	if($result_sql == 0){
		header('Location: lista_proveedores.php');
	}else{

		while ($data = mysqli_fetch_array($sql)) {

			$idproveedor  = $data['codproveedor'];
			$proveedor    = $data['proveedor'];
			$contacto     = $data['contacto'];
			$telefono     = $data['telefono'];
			$direccion    = $data['direccion'];
		}
	}

 ?>

<!DOCTYPE html>
<html lang="en">
<head>
	<meta charset="UTF-8">
	<?php include "includes/scripts.php"; ?>
	<title>Actualizar Proveedor</title>
</head>
<body>
	<?php include "includes/header.php"; ?>
	<section id="container">
		
		<div class="form_register">
			<h1><i class="fas fa-truck fa-1x"></i> Actualizar proveedor</h1>
			<hr>
			<div class="alert"><?php echo isset($alert) ? $alert : ''; ?></div>

				<form action="" method="post">
					<input type="hidden" name="id" value="<?php echo $idproveedor;  ?>">
					<!-- label de nombre -->
					<label for="proveedor">Proveedor(*)</label>
					<input type="text" name="proveedor" id="proveedor" placeholder="Nombre del proveedor" value="<?php echo $proveedor;  ?>">
					<!-- label de correo -->
					<label for="contacto">Contacto(*)</label>
					<input type="text" name="contacto" id="contacto" placeholder="Nombre del contacto" value="<?php echo $contacto;  ?>">
					<!-- label de usuario-->
					<label for="telefono">Teléfono(*)</label>
					<input type="number" name="telefono" id="telefono" placeholder="Teléfono" value="<?php echo $telefono;  ?>">
					<!-- label de clave-->
					<label for="direccion">Dirección(*)</label>
					<input type="text" name="direccion" id="direccion" placeholder="Dirección completa" value="<?php echo $direccion;  ?>">
					<button type="submit" class="btn_save"><i class="fas fa-edit"></i> Actualizar Proveedor</button>
				</form>
		</div>


	</section>
	<?php include "includes/footer.php"; ?>
</body>
</html>