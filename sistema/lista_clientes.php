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
	<title>Lista de Clientes</title>
</head>
<body>
	<?php include "includes/header.php";?>
	<section id="container">
		<h1><i class="fas fa-users fa-2x"></i> Lista de clientes</h1>
		<a href="registro_cliente.php" class="btn_new"><i class="fas fa-user-plus"></i> Registrar cliente</a>
		<form action="buscar_cliente.php" method="get" class="form_search">
			<input type="text" name="busqueda" id="buesqueda" placeholder="Buscar cliente">
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
				$sql_register = mysqli_query($obj_conexion,"SELECT COUNT(*) AS total_registro FROM cliente WHERE estatus = 1");
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

/*--------------------------------------------------------------------------------*/
				$query = mysqli_query($obj_conexion,"SELECT* FROM cliente 
													 WHERE estatus = 1 ORDER BY idcliente ASC LIMIT $desde,$por_pagina
					");

				mysqli_close($obj_conexion);

				$result = mysqli_num_rows($query);

				if ($result > 0 ) {
					
					while ($data = mysqli_fetch_array($query)) {

						if ($data ["dni"] == 0) {
							$dni = "C/F";
						}else{
							$dni = $data ["dni"];
						}
				?>
					<td><?php echo $data ["idcliente"]; ?></td>
					<td><?php echo $dni; ?></td>
					<td><?php echo $data ["nombre"]; ?></td>
					<td><?php echo $data ["telefono"]; ?></td>
					<td><?php echo $data ["direccion"]; ?></td>
					<td>	
		<a class="link_edit" href="editar_cliente.php?id=<?php echo $data ["idcliente"]; ?>"><i class="fas fa-edit"></i> Editar</a>
		|				
		<a class="link_delete" href="eliminar_comfirmar_cliente.php?id=<?php echo $data ["idcliente"]; ?>"><i class="fas fa-trash-alt"></i> Eliminar</a>
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