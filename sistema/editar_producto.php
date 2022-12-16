<!-- Funcionalidad nuevo usuario. -->
<?php 
	session_start();
	include "../conexion.php";

	if (!empty($_POST)){
		
		$alert='';

		if (empty($_POST['proveedor']) || empty($_POST['producto']) || empty($_POST['precio']) || empty($_POST['id'])){

			$alert = '<p class="msg_error">Llena los campos obligatorios. (*)</p>';

		}if ($_POST['precio'] < 0) {
			$alert = '<p class="msg_error"> Valores numericos incorrectos.</p>';
		}else{

			$codproducto  = $_POST['id'];
			$proveedor    = $_POST['proveedor'];
			$producto     = $_POST['producto'];
			$precio       = $_POST['precio'];

			
			$query_update = mysqli_query($obj_conexion,"UPDATE producto
													    SET descripcion = '$producto',
													        proveedor = $proveedor, 
													        precio = $precio
													        WHERE codproducto = '$codproducto' ");

				if($query_update) {
					$alert = '<p class="msg_save">Producto actualizado correctamente.</p>';
				}else{
					$alert = '<p class="msg_error">Error al actualizar el producto.</p>';
				}
		}
		
	}
	//VALIDAR PRODUCTO.

	if(empty($_REQUEST['id'])){

		header("location: lista_productos.php");
	}else{
		$id_producto = $_REQUEST['id'];
		if (!is_numeric($id_producto)) {
			header("location: lista_productos.php");
		}

		$query_producto = mysqli_query($obj_conexion,"SELECT p.codproducto,p.descripcion,p.precio,pr.codproveedor,pr.proveedor 								   			FROM producto p
													  INNER JOIN proveedor pr
													  ON p.proveedor = pr.codproveedor
													  WHERE p.codproducto = $id_producto AND p.estatus = 1");

		$result_producto = mysqli_num_rows($query_producto);

		if ($result_producto > 0) {
			
			$data_producto = mysqli_fetch_assoc($query_producto);
			
		}else{
			header("location: lista_productos.php");
		}

	}


?>
<!-- Formulario nuevo proveedor. -->
<!DOCTYPE html>
<html lang="en">
<head>
	<meta charset="UTF-8">
	<?php include "includes/scripts.php";?>
	<title>Actualizar Producto</title>
</head>
<body>
	<?php include "includes/header.php";?>
	<!-- Plantilla de guardar productos-->
	<section id="container">
		
		<div class="form_register">
			<h1><i class="fas fa-box"></i> Actualizar producto</h1>
			<hr>
			<div class="alert"><?php echo isset($alert)? $alert: '';?></div>

				<form action="" method="post">

					<input type="hidden" name="id" value="<?php echo $data_producto['codproducto']; ?>">

					<!-- label de proveedor -->
					<label for="proveedor">Proveedor(*)</label>

					<?php  

					$query_proveedor = mysqli_query($obj_conexion,"SELECT codproveedor, proveedor FROM proveedor WHERE estatus = 1 ORDER BY proveedor ASC");
					$result_proveedor = mysqli_num_rows($query_proveedor);
					mysqli_close($obj_conexion);

					?>

					<select name="proveedor" id="proveedor" class="notItemOne">
						<option value="<?php echo $data_producto['codproveedor']; ?>" selected><?php echo $data_producto['proveedor']; ?></option>
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
					<input type="text" name="producto" id="producto" placeholder="Nombre del producto" value="<?php echo $data_producto['descripcion']; ?>">

					<!-- label de precio-->
					<label for="precio">Precio(*)</label>
					<input type="number" name="precio" id="precio" placeholder="Precio del producto" value="<?php echo $data_producto['precio']; ?>">

					<button type="submit" class="btn_save"><i class="fas fa-edit"></i> Actualizar Producto</button>
				</form>

			</div>

	</section>
	<?php include "includes/footer.php";?>
</body>
</html>