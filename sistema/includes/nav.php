		<nav>
			<ul>
				<li><a href="index.php"><i class="fas fa-home"></i> Inicio</a></li>
				<?php
					if ($_SESSION['rol'] == 1) {
				?>
				<li class="principal">
					<a href="#"><i class="fas fa-users"></i> Usuarios</a>
					<ul>
						<li><a href="registro_usuario.php"><i class="fas fa-user-plus"></i> Nuevo Usuario</a></li>
						<li><a href="lista_usuario.php"><i class="fas fa-list"></i> Lista de Usuarios</a></li>
					</ul>
				</li>
				<li class="principal">
					<a href="#"><i class="fas fa-truck"></i> Proveedores</a>
					<ul>
						<li><a href="registro_proveedor.php"><i class="fas fa-handshake"></i> Nuevo Proveedor</a></li>
						<li><a href="lista_proveedores.php"><i class="fas fa-list"></i> Lista de Proveedores</a></li>
					</ul>
				</li>
				<li class="principal">
					<a href="#"><i class="fas fa-dolly"></i> Productos</a>
					<ul>
						<li><a href="registro_producto.php"><i class="fas fa-box"></i> Nuevo Producto</a></li>
						<li><a href="lista_productos.php"><i class="fas fa-list"></i> Lista de Productos</a></li>
					</ul>
				</li>
		     	<li class="principal">
					<a href="#"><i class="fas fa-user"></i> Clientes</a>
					<ul>
						<li><a href="registro_cliente.php"><i class="fas fa-user-plus"></i> Nuevo Cliente</a></li>
						<li><a href="lista_clientes.php"><i class="fas fa-list"></i> Lista de Clientes</a></li>
					</ul>
				</li>
				<li class="principal">
					<a href="#"><i class="fas fa-shopping-cart"></i> Ventas</a>
					<ul>
						<li><a href="nueva_venta.php"><i class="fas fa-cart-plus"></i> Nueva Venta</a></li>
						<li><a href="ventas.php"><i class="fas fa-list"></i> Ventas</a></li>
					</ul>
				</li>
				<?php } ?>
			</ul>
		</nav>