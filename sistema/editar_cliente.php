<?php 
	
	session_start();
	if($_SESSION['rol'] != 3)
	{
		header("location: ./");
	}

	include "../conexion.php";

	if(!empty($_POST))
	{
		$alert='';
		if(empty($_POST['nombre']) || empty($_POST['telefono']) || empty($_POST['direccion']))
		{
			$alert='<p class="msg_error">Llena los campos obligatorios.</p>';
		}else{

			$idCliente = $_POST['id'];
			$dni       = $_POST['dni'];
			$nombre    = $_POST['nombre'];
			$telefono  = $_POST['telefono'];
			$direccion = $_POST['direccion'];

			$result = 0;

			if (is_numeric($dni) and $dni != 0) {

			$query = mysqli_query($obj_conexion,"SELECT * FROM cliente
											     WHERE (dni = '$dni' and idcliente != $idCliente)");

				$result = mysqli_fetch_array($query);
				$result = count($result);

			}

			if($result > 0){
				$alert='<p class="msg_error">El N° Identificación ya existe, ingrese otro.</p>';
			}else{
				if ($dni == '') {
					$dni = 0;
				}

					$sql_update = mysqli_query($obj_conexion,"UPDATE cliente
															SET dni = $dni, nombre='$nombre',telefono='$telefono',direccion='$direccion'
															WHERE idcliente = $idCliente ");

				if($sql_update){
					$alert='<p class="msg_save">cliente actualizado correctamente.</p>';
				}else{
					$alert='<p class="msg_error">Error al actualizar el cliente.</p>';
				}
			}
		}
	}

	//Mostrar Datos
	if(empty($_REQUEST['id']))
	{
		header('Location: lista_clientes.php');
		mysqli_close($obj_conexion);
	}
	$idcliente = $_REQUEST['id'];

	$sql= mysqli_query($obj_conexion,"SELECT * FROM cliente WHERE idcliente = $idcliente AND estatus = 1 ");
	mysqli_close($obj_conexion);
	$result_sql = mysqli_num_rows($sql);

	if($result_sql == 0){
		header('Location: lista_clientes.php');
	}else{

		while ($data = mysqli_fetch_array($sql)) {

			$idcliente  = $data['idcliente'];
			$dni        = $data['dni'];
			$nombre     = $data['nombre'];
			$telefono   = $data['telefono'];
			$direccion  = $data['direccion'];
		}
	}

 ?>

<!DOCTYPE html>
<html lang="en">
<head>
	<meta charset="UTF-8">
	<?php include "includes/scripts.php"; ?>
	<title>Actualizar Cliente</title>
</head>
<body>
	<?php include "includes/header.php"; ?>
	<section id="container">
		
		<div class="form_register">
			<h1><i class="fas fa-edit fa-2x"></i> Actualizar cliente</h1>
			<hr>
			<div class="alert"><?php echo isset($alert) ? $alert : ''; ?></div>

				<form action="" method="post">
					<input type="hidden" name="id" value="<?php echo $idcliente; ?>">
					<!-- label de identificacion -->
					<label for="dni">Identificación</label>
					<input type="number" name="dni" id="dni" placeholder="N° Identificación" value="<?php echo $dni; ?>">
					<!-- label de nombre -->
					<label for="nombre">Nombre(*)</label>
					<input type="text" name="nombre" id="nombre" placeholder="Nombre completo" value="<?php echo $nombre; ?>">
					<!-- label de telefono-->
					<label for="telefono">Teléfono(*)</label>
					<input type="number" name="telefono" id="telefono" placeholder="Teléfono" value="<?php echo $telefono ; ?>">
					<!-- label de direccion-->
					<label for="direccion">Dirección(*)</label>
					<input type="text" name="direccion" id="direccion" placeholder="Dirección completa" value="<?php echo $direccion; ?>">
					<button type="submit" class="btn_save"><i class="fas fa-edit"></i> Actualizar cliente</button>
				</form>
		</div>


	</section>
	<?php include "includes/footer.php"; ?>
</body>
</html>