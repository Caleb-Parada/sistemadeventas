<?php 
	session_start();
	
	include "../conexion.php";
?>

<!-- Plantilla base para todas las paginas-->
<!DOCTYPE html>
<html lang="en">
<head>
	<meta charset="UTF-8">
	<?php include "includes/scripts.php";?>
	<title>Lista de Productos</title>
</head>
<body>
	<?php include "includes/header.php";?>
	<section id="container">
		<h1><i class="fas fa-list fa-1x"></i> Lista de productos</h1>
		<a href="registro_producto.php" class="btn_new"><i class="fas fa-box"></i> Registrar producto</a>
		<form action="buscar_productos.php" method="get" class="form_search">
			<input type="text" name="busqueda" id="buesqueda" placeholder="Buscar producto">
			<button type="submit" class="btn_search"><i class="fas fa-search"></i></button>
		</form>
 <!--Tabla de productos-->
		<table>
			<tr>
				<th>Código</th>
				<th>Descripción</th>
				<th>Precio</th>
				<th>Existencias</th>
				<th>
				<?php  

					$query_proveedor = mysqli_query($obj_conexion,"SELECT codproveedor, proveedor FROM proveedor WHERE estatus = 1 ORDER BY proveedor ASC");
					$result_proveedor = mysqli_num_rows($query_proveedor);

					?>

					<select name="proveedor" id="search_proveedor">
					<option value="" selected ><b>Proveedor</b></option>
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

				</th>
				<th>Acciones</th>
			</tr>
			<?php 
				//paginador
				$sql_register = mysqli_query($obj_conexion,"SELECT COUNT(*) AS total_registro FROM producto WHERE estatus = 1");
				$result_register = mysqli_fetch_array($sql_register);
				
				$total_registro = $result_register['total_registro'];

				$por_pagina = 8;

				if (empty($_GET['pagina'])) {
					
					$pagina = 1;

				}else{

					$pagina = $_GET['pagina'];

				}

				$desde = ($pagina - 1) * $por_pagina;
				$total_paginas = ceil($total_registro/ $por_pagina);

/*-------------------------------fin del paginador-----------------------------------------*/
				$query = mysqli_query($obj_conexion,"SELECT p.codproducto, p.descripcion, p.precio, p.existencia, pr.proveedor 
													 FROM producto p
													 INNER JOIN  proveedor pr
													 ON p.proveedor = pr.codproveedor
													 WHERE p.estatus = 1 ORDER BY p.codproducto DESC LIMIT $desde,$por_pagina
					                                 ");

				mysqli_close($obj_conexion);

				$result = mysqli_num_rows($query);

				if ($result > 0 ) {
					
					while ($data = mysqli_fetch_array($query)) {

				?>
					<tr class="row<?php echo $data ["codproducto"]; ?>">
					<td><?php echo $data ["codproducto"]; ?></td>
					<td><?php echo $data ["descripcion"]; ?></td>
					<td class="celPrecio"><?php echo $data ["precio"]; ?></td>
					<td class="celExistencia"><?php echo $data ["existencia"]; ?></td>
					<td><?php echo $data ["proveedor"]; ?></td>
					<td>
		<a class ="link_add add_product"  product = "<?php echo $data ["codproducto"]; ?>" href="#"><i class="fas fa-plus"></i> Agregar</a>
		|
		<a class="link_edit" href="editar_producto.php?id=<?php echo $data ["codproducto"]; ?>"><i class="fas fa-edit"></i> Editar</a>
		|				
		<a class="link_delete del_product" href="#" product = "<?php echo $data ["codproducto"]; ?>"><i class="fas fa-trash-alt"></i> Eliminar</a>
					</td>			
					</tr>	
				<?php
					}
				}
				?>
		</table>
		<div class="paginador">
			<ul>
				<?php 
				if ($pagina != 1) {
					
				
				?>
			<li><a href="?pagina=<?php echo 1; ?>"><i class="fas fa-step-backward"></i></a></li>
			<li><a href="?pagina=<?php echo $pagina - 1; ?>"><i class="fas fa-fast-backward"></i></a></li>

			<?php 
				}
				for ($i=1; $i <= $total_paginas; $i++) { 

				if ($i == $pagina){

						echo '<li class="pageSelected">'.$i.'</li>';

					}else{

						echo '<li><a href="?pagina='.$i.'">'.$i.'</a></li>';
					}
					
				}
				if ($pagina != $total_paginas ) {
				
				
			 ?>
			
			<li><a href="?pagina=<?php echo $pagina + 1; ?>"><i class="fas fa-fast-forward"></i></a></li></a></li>
			<li><a href="?pagina=<?php echo $total_paginas; ?>"><i class="fas fa-step-forward"></i></a></li>
		<?php } ?>
			</ul>
		</div>
	</section>
	<?php include "includes/footer.php";?>
</body>
</html>