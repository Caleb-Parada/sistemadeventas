<!-- Funcionalidad nuevo usuario. -->
<?php 
	session_start();
	include "../conexion.php";

	if (!empty($_POST)){
		
		$alert='';

		if (empty($_POST['proveedor']) || empty($_POST['producto']) || empty($_POST['precio']) || empty($_POST['cantidad'])){

			$alert = '<p class="msg_error">Llena los campos obligatorios. (*)</p>';

		}if ($_POST['precio']<0 || $_POST['cantidad'] < 0 ) {
			$alert = '<p class="msg_error"> Valores numericos incorrectos.</p>';
		}else{

			$proveedor    = $_POST['proveedor'];
			$producto     = $_POST['producto'];
			$precio       = $_POST['precio'];
			$cantidad     = $_POST['cantidad'];
			$usuario_id   = $_SESSION['idUser'];

			
			$query_insert = mysqli_query($obj_conexion,"INSERT INTO producto(proveedor, descripcion, precio, existencia, usuario_id) 
				VALUES ('$proveedor', '$producto', '$precio', '$cantidad','$usuario_id')");

				if($query_insert) {
					$alert = '<p class="msg_save">Producto guardado correctamente.</p>';
				}else{
					$alert = '<p class="msg_error">Error al guardar el producto.</p>';
				}
		}
		
	}


?>
<!-- Formulario nuevo proveedor. -->
<!DOCTYPE html>
<html lang="en">
<head>
	<meta charset="UTF-8">
	<?php include "includes/scripts.php";?>
	<title>Registro Productos</title>
</head>
<body>
	<?php include "includes/header.php";?>
	<!-- Plantilla de guardar productos-->
	<section id="container">
		
		<div class="form_register">
			<h1><i class="fas fa-box"></i> Registro producto</h1>
			<hr>
			<div class="alert"><?php echo isset($alert)? $alert: '';?></div>

				<form action="" method="post">

					<!-- label de proveedor -->
					<label for="proveedor">Proveedor(*)</label>

					<?php  

					$query_proveedor = mysqli_query($obj_conexion,"SELECT codproveedor, proveedor FROM proveedor WHERE estatus = 1 ORDER BY proveedor ASC");
					$result_proveedor = mysqli_num_rows($query_proveedor);
					mysqli_close($obj_conexion);

					?>

					<select name="proveedor" id="proveedor">
					<?php  
					if ($result_proveedor > 0) {
						while ($proveedor = mysqli_fetch_array($query_proveedor)) {
					
					?>
					<option value="<?php echo $proveedor['codproveedor']; ?>"><?php echo $proveedor['proveedor']; ?></option>
					<?php
						}
					}
					?>
						
					</select>

					<!-- label de producto -->
					<label for="producto">Producto(*)</label>
					<input type="text" name="producto" id="producto" placeholder="Nombre del producto">

					<!-- label de precio-->
					<label for="precio">Precio(*)</label>
					<input type="number" name="precio" id="precio" placeholder="Precio del producto">

					<!-- label de cantidad-->
					<label for="cantidad">Cantidad(*)</label>
					<input type="number" name="cantidad" id="cantidad" placeholder="Cantidad del producto">

					<button type="submit" class="btn_save"><i class="fas fa-save"></i> Guardar Producto</button>
				</form>

			</div>

	</section>
	<?php include "includes/footer.php";?>
</body>
</html>