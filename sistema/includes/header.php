<?php
	if (empty($_SESSION['active'])) {
		header('location: ../');
	}
?>
<header>
		<div class="header">
			
			<h1><i class="fas fa-cart-arrow-down"></i> Sistema De Ventas</h1>
			<div class="optionsBar">
				<p><i class="fas fa-calendar-alt"></i> Popayán, <?php echo fechaC(); ?> </p>
				<span>|</span>
				<span class="user"><?php echo $_SESSION['user'] , "-" , $_SESSION['idUser']; ?></span>
				<img class="photouser" src="imag/user.png" alt="Usuario">
				<a href="salir.php"><img class="close" src="imag/salir.png" alt="Salir del sistema" title="Salir"></a>
			</div>
		</div>
		<?php include "nav.php";?>
	</header>
	<div class = "modal">
		<div class = "bodyModal">
		</div>
	</div>