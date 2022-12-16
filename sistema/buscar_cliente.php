<?php 
	session_start();
	if ($_SESSION['rol'] != 3) {

		header("location: ./");
	}
	include "../conexion.php";
?>

<!-- Plantilla base para todas las paginas-->
<!DOCTYPE html>
<html lang="en">
<head>
	<meta charset="UTF-8">
	<?php include "includes/scripts.php";?>
	<title>Lista de clientes</title>
</head>
<body>
	<?php include "includes/header.php";?>
	<section id="container">
<!-- buscador usuario-->
		<?php  

			$busqueda = strtolower($_REQUEST['busqueda']);
			if (empty($busqueda)) {
				header("location: lista_clientes.php");
				mysqli_close($obj_conexion);
			}
		?>

		<h1>Lista de clientes</h1>
		<a href="registro_cliente.php" class="btn_new"><i class="fas fa-user-plus"></i> Registrar cliente</a>
		<form action="buscar_cliente.php" method="get" class="form_search">
			<!-- boton buscar-->
			<input type="text" name="busqueda" id="buesqueda" placeholder="Buscar" value="<?php echo $busqueda;?>">
			<button type="submit" class="btn_search"><i class="fas fa-search"></i></button>
		</form>
 <!--Tabla de clientes-->
		<table>
			<tr>
				<th>ID</th>
				<th>Identificación</th>
				<th>Nombre</th>
				<th>Teléfono</th>
				<th>Dirección</th>
				<th>Acciones</th>
			</tr>

			<?php 
				//paginador
				$sql_register = mysqli_query($obj_conexion,"SELECT COUNT(*) AS total_registro FROM cliente 
																	WHERE
																	(	idcliente LIKE '%$busqueda%' OR
																	    dni LIKE '%$busqueda%' OR
																	    nombre LIKE '%$busqueda%' OR
																	    telefono LIKE '%$busqueda%' OR
																	    direccion LIKE '%$busqueda%' 
																	)
																	AND estatus = 1");
				$result_register = mysqli_fetch_array($sql_register);
				$total_registro = $result_register['total_registro'];

				$por_pagina = 8 ;

				if (empty($_GET['pagina'])) {
					
					$pagina = 1;

				}else{

					$pagina = $_GET['pagina'];

				}

				$desde = ($pagina - 1) * $por_pagina;
				$total_paginas = ceil($total_registro/ $por_pagina);

/*--------------------------------------------------------------------------------*/
				$query= mysqli_query($obj_conexion,"SELECT * FROM cliente WHERE 
													   (idcliente LIKE '%$busqueda%' OR
														dni LIKE '%$busqueda%' OR
														nombre LIKE '%$busqueda%' OR
														telefono LIKE '%$busqueda%' OR
														direccion LIKE '%$busqueda%' )
														AND estatus = 1 ORDER BY idcliente ASC LIMIT $desde,$por_pagina");
				mysqli_close($obj_conexion);
				$result = mysqli_num_rows($query);

				if ($result > 0 ) {
					
					while ($data = mysqli_fetch_array($query)) {
				?>
					<td><?php echo $data ["idcliente"]; ?></td>
					<td><?php echo $data ["dni"]; ?></td>
					<td><?php echo $data ["nombre"]; ?></td>
					<td><?php echo $data ["telefono"]; ?></td>
					<td><?php echo $data ["direccion"]; ?></td>
					<td>	
						<a class="link_edit" href="editar_cliente.php?id=<?php echo $data ["idcliente"]; ?>">Editar</a>
						|
						<a class="link_delete" href="eliminar_comfirmar_cliente.php?id=<?php echo $data ["idcliente"]; ?>">Eliminar</a>
					</td>			
				</tr>	
				<?php
					}
				}
				?>
		</table>
		<?php 
		if ($total_registro != 0) {
		?>
		<div class="paginador">
			<ul>
				<?php 
				if ($pagina != 1) {
					
				
				?>
			<li><a href="?pagina=<?php echo 1; ?>&busqueda =<?php echo $busqueda; ?>"><i class="fas fa-step-backward"></i></a></li>
			<li><a href="?pagina=<?php echo $pagina - 1; ?>&busqueda =<?php echo $busqueda; ?>"><i class="fas fa-fast-backward"></i></a></li>

			<?php 
				}
				for ($i=1; $i <= $total_paginas; $i++) { 

				if ($i == $pagina){

						echo '<li class="pageSelected">'.$i.'</li>';

					}else{

						echo '<li><a href="?pagina='.$i.'&busqueda='.$busqueda.'">'.$i.'</a></li>';
					}
					
				}
				if ($pagina != $total_paginas ) {
				
				
			 ?>
			
			<li><a href="?pagina=<?php echo $pagina + 1; ?>&busqueda =<?php echo $busqueda; ?>"><i class="fas fa-fast-forward"></i></a></li>
			<li><a href="?pagina=<?php echo $total_paginas; ?>&busqueda =<?php echo $busqueda; ?>"><i class="fas fa-step-forward"></i></a></li>
		<?php } ?>
			</ul>
		</div>
	<?php }  ?>
	</section>
	<?php include "includes/footer.php";?>
</body>
</html>