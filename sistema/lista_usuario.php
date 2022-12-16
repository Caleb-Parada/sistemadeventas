<?php 
	session_start();
	if ($_SESSION['rol'] != 1) {

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
	<title>Lista de usuarios</title>
</head>
<body>
	<?php include "includes/header.php";?>
	<section id="container">
		<h1><i class="fas fa-users fa-1x"></i> Lista de usuarios</h1>
		<a href="registro_usuario.php " class="btn_new"><i class="fas fa-user-plus"></i> Crear usuario</a>
		<form action="buscar_usuario.php" method="get" class="form_search">
			<input type="text" name="busqueda" id="buesqueda" placeholder="Buscar">
			<button type="submit" class="btn_search"><i class="fas fa-search"></i></button>
		</form>

		<table>
			<tr>
				<th>ID</th>
				<th>Nombre</th>
				<th>Correo</th>
				<th>Usuario</th>
				<th>Rol</th>
				<th>Acciones</th>
			</tr>
			<?php 
				//paginador
				$sql_register = mysqli_query($obj_conexion,"SELECT COUNT(*) AS total_registro FROM usuario WHERE estatus = 1");
				$result_register = mysqli_fetch_array($sql_register);
				
				$total_registro = $result_register['total_registro'];

				$por_pagina = 5 ;

				if (empty($_GET['pagina'])) {
					
					$pagina = 1;

				}else{

					$pagina = $_GET['pagina'];

				}

				$desde = ($pagina - 1) * $por_pagina;
				$total_paginas = ceil($total_registro/ $por_pagina);

/*--------------------------------------------------------------------------------*/
				$query= mysqli_query($obj_conexion,"SELECT u.idusuario, u.nombre, u.correo, u.usuario, r.rol FROM usuario u INNER JOIN rol r on u.rol = r.idrol WHERE estatus = 1 
					ORDER BY idusuario ASC LIMIT $desde,$por_pagina
					");
				mysqli_close($obj_conexion);
				$result = mysqli_num_rows($query);

				if ($result > 0 ) {
					
					while ($data = mysqli_fetch_array($query)) {
				?>
					<td><?php echo $data ["idusuario"]; ?></td>
					<td><?php echo $data ["nombre"]; ?></td>
					<td><?php echo $data ["correo"]; ?></td>
					<td><?php echo $data ["usuario"]; ?></td>
					<td><?php echo $data ["rol"]; ?></td>
					<td>	
						<a class="link_edit" href="editar_usuario.php?id=<?php echo $data ["idusuario"]; ?>"><i class="fas fa-edit"></i> Editar</a>
						
						<?php if ($data ["idusuario"] != 1) { ?>
						|
						<a class="link_delete" href="eliminar_comfirmar_usuario.php?id=<?php echo $data ["idusuario"]; ?>"><i class="fas fa-trash-alt"></i> Eliminar</a>
					<?php } ?>
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
			
			<li><a href="?pagina=<?php echo $pagina + 1; ?>"><i class="fas fa-fast-forward"></i></a></li>
			<li><a href="?pagina=<?php echo $total_paginas; ?>"><i class="fas fa-step-forward"></i></a></li>
		<?php } ?>
			</ul>
		</div>
	</section>
	<?php include "includes/footer.php";?>
</body>
</html>