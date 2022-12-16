<!-- Validacion de datos en la base de datos PHP -->
<?php

$alert = '';
session_start();
	if (!empty($_SESSION['active'])) {
		header('location: sistema/');
	}else
	{

		if (!empty($_POST)) {
			
			if (empty($_POST['Usuario']) || empty($_POST['clave']) ) {
				
				$alert = 'Ingrese su usuario y contraseña';
				
			}else{

				require_once "conexion.php";
				//Encriptar una contraseña
				$user = mysqli_real_escape_string($obj_conexion,$_POST['Usuario']);
				//$user = $_POST['Usuario'];
				$pass = md5(mysqli_real_escape_string($obj_conexion,$_POST['clave']));
				//$pass = md5($_POST['clave']);
				echo "user : $user, pass : $pass";
				//validar dato en la tabla usuario
				$query = mysqli_query($obj_conexion,"SELECT * FROM usuario WHERE usuario = '$user' AND clave = '$pass'");
				mysqli_close($obj_conexion);
				$result = mysqli_num_rows($query);

				if ($result > 0) {
					//iniciar sesion
					$data = mysqli_fetch_array($query);
					$_SESSION['active']=true;
					$_SESSION['idUser']= $data ['idusuario'];
					$_SESSION['nombre']= $data ['nombre'];
					$_SESSION['email']= $data ['correo'];
					$_SESSION['user']= $data ['usuario'];
					$_SESSION['rol']= $data ['rol'];

					header('location: sistema/'); //Esto hace que a index.html en la carpeta sistema
				}else{

					$alert = 'El usuario o la clave son incorrectos';
					session_destroy();
				}
			}
		}
}

?>
<!-- Interfaz index HTML -->
<!DOCTYPE html>
<html lang="en">
<head>
	<meta charset="utf-8">
	<title>Login | Sistema De Ventas</title>
	<link rel="stylesheet" type="text/css" href="css/style.css">
</head>
<body>

<section id="container">
	
	<form action="" method="post">
		
		<h3>Iniciar Sesión</h3>
		<img src="img/login.png" alt="login">

		<input type="text" name="Usuario" placeholder="Usuario">
		<input type="password" name="clave" placeholder="Contraseña">
		<div class="alert"><?php echo isset($alert)? $alert: '';?></div>
		<input type="submit" value="INGRESAR">

	</form>


</section>
<div class="BarraInferior">Copyright &copy; 2023 - Software creado por Caleb Parada Vaca - Todos los derechos reservados.</div>
</body>
</html>
