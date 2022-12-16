<?php

	include "../conexion.php";
	session_start();
	//print_r($_POST);exit;

	if(!empty($_POST)){

		//extraer datos del producto
		if ($_POST['action'] == 'infoProducto') {
			
			$producto_id = $_POST['producto'];

			$query = mysqli_query($obj_conexion,"SELECT codproducto, descripcion, existencia, precio FROM producto 
								 WHERE codproducto = $producto_id AND estatus = 1 ");
			mysqli_close($obj_conexion);

			$result = mysqli_num_rows($query);

			if ($result > 0) {
			 	$data = mysqli_fetch_assoc($query);
			 	echo json_encode($data,JSON_UNESCAPED_UNICODE);
			 	exit;
			 } 
			 echo'error'; //1. error
			exit;
		}//fin del info product

		//agregar producto a entrada.
		if ($_POST['action'] == 'addProduct') {
			//si esta vacio
			if (!empty($_POST['cantidad']) || !empty($_POST['precio'])|| !empty($_POST['producto_id'])){

				$Cantidad    = $_POST['cantidad'];
				$precio      = $_POST['precio'];
				$producto_id = $_POST['producto_id'];
				$usuario_id  = $_SESSION['idUser'];
				//query para insertar a entradas.
				$query_insert = mysqli_query($obj_conexion,"INSERT INTO entradas(codproducto,cantidad,precio,usuario_id) 
															VALUES($producto_id,$Cantidad,$precio,$usuario_id)");
				//Validamos si se hizo el insert
				if ($query_insert) {
					//ejecutar procedimiento almacenado.
					$query_upd = mysqli_query($obj_conexion,"CALL actualizar_precio_producto($Cantidad,$precio,$producto_id)");
					$result_pro = mysqli_num_rows($query_upd);
					//validamos si nos devuelve un valor  var $result_pro
					if ($result_pro > 0) {
						$data = mysqli_fetch_assoc($query_upd);
						$data['producto_id'] = $producto_id;
						//json agregar.
						echo json_encode($data,JSON_UNESCAPED_UNICODE);
			 			exit;
					}
				}
				//sino se hace nada
				else{
					echo'error'; //2. error
				}
				//termina agregar
				mysqli_close($obj_conexion);
			}
			else{
				echo'error'; //3. error
			}
			exit;//2.exit
		}//fin del add product

		//inicio eliminar producto.
		if ($_POST['action'] == 'delProduct') {

			if (empty($_POST['producto_id']) || !is_numeric($_POST['producto_id'])) {
				echo 'error';
			}else{

				$idproducto = $_POST['producto_id'];
				$query_delete = mysqli_query($obj_conexion,"UPDATE producto SET estatus = 0 WHERE codproducto = $idproducto");
				mysqli_close($obj_conexion);

				if ($query_delete){
					echo 'ok';
				}else{
					echo 'error';
				}
				
			}
			echo 'error';
			exit;
		}//fin del eliminar product

		//Buscar cliente
		if ($_POST['action'] == 'searchCliente'){
			
			if (!empty($_POST['cliente'])) {
				$dni = $_POST['cliente'];

				$query = mysqli_query($obj_conexion,"SELECT* FROM cliente WHERE dni LIKE '$dni' AND estatus = 1");
				mysqli_close($obj_conexion);
				$result = mysqli_num_rows($query);

				$data = '';
				if ($result > 0) {
					$data = mysqli_fetch_assoc($query);
				}else{
					$data = 0;
				}
				echo json_encode($data,JSON_UNESCAPED_UNICODE);

			}//fin del buscar cliente
			exit;
		}//fin del buscar cliente

		//crear nuevo cliente - venta
		if ($_POST['action'] == 'addCliente'){

			$dni        = $_POST['dni_cliente'];
			$nombre     = $_POST['nom_cliente'];
			$telefono   = $_POST['tel_cliente'];
			$direccion  = $_POST['dir_cliente'];
			$usuario_id = $_SESSION['idUser'];

			$query_insert = mysqli_query($obj_conexion,"INSERT INTO cliente(dni,nombre,telefono,direccion,usuario_id) 
														VALUES('$dni','$nombre','$telefono','$direccion','$usuario_id')");

			if ($query_insert) {
				$codCliente = mysqli_insert_id($obj_conexion);
				$msg = $codCliente;
			}else{
				$msg = 'error';
			}
			mysqli_close($obj_conexion);
			echo $msg;
			exit;

		}//fin del crear cliente
		//agregar producto al temp
		if ($_POST['action'] == 'addProductoDetalle'){

			if (empty($_POST['producto']) || empty($_POST['cantidad']) ) {
				echo 'error';
			}else{

				$codproducto = $_POST['producto'];
				$cantidad    = $_POST['cantidad'];
				$token       = md5($_SESSION['idUser']);

				$query_iva = mysqli_query($obj_conexion,"SELECT iva FROM configuracion");
				$result_iva = mysqli_num_rows($query_iva);

				$query_detalle_temp = mysqli_query($obj_conexion,"CALL add_detalle_temp($codproducto,$cantidad,'$token')");
				$result = mysqli_num_rows($query_detalle_temp);

				$detalleTabla = '';
				$sub_total = 0;
				$iva = 0;
				$total = 0;
				$arrayData = array();

				if ($result > 0) {

					if ($result_iva > 0) {
						$info_iva = mysqli_fetch_assoc($query_iva);
						$iva = $info_iva['iva'];
					}
					while ($data = mysqli_fetch_assoc($query_detalle_temp)) {
						$precioTotal = round($data['cantidad'] * $data['precio_venta'],2);
						$sub_total   = round($sub_total + $precioTotal,2);
						$total       = round($total + $precioTotal,2);

						$detalleTabla .= '<tr>
										 <td>'.$data['codproducto'].'</td>
										 <td colspan="2">'.$data['descripcion'].'</td>
										 <td class="textright">'.$data['cantidad'].'</td>
										 <td class="textright">'.$data['precio_venta'].'</td>
										 <td class="textright">'.$precioTotal.'</td>
										 <td class="">
										     <a class="link_delete" href="#" onclick="event.preventDefault(); del_product_detalle('.$data['correlativo'].');"><i class="far fa-trash-alt"></i></a>
										 </td>
										 </tr>
						';

					}//FIN WHILE CALCULO DEL IVA

					$impuesto = round($sub_total * ($iva / 100),2);
					$tl_sniva = round($sub_total - $impuesto,2);
					$total = round($tl_sniva + $impuesto,2);

					$detalleTotales = '<tr>
									   <td colspan="5" class="textright"> SUBTOTAL $.</td>
									   <td class="textright">'.$tl_sniva.'</td>
									   </tr>
									   <tr>
									   <td colspan="5" class="textright"> IVA ('.round($iva,0).'%)</td>
									   <td class="textright">'.$impuesto.'</td>
									   </tr>
									   <tr>
									   <td colspan="5" class="textright"> TOTAL $.</td>
									   <td class="textright">'.$total.'</td>
									   </tr>';

					$arrayData['detalle'] = $detalleTabla;
					$arrayData['totales'] = $detalleTotales;

					echo json_encode($arrayData,JSON_UNESCAPED_UNICODE);
				}else{
					echo 'error';
				}
				mysqli_close($obj_conexion);
			}
			exit;

		}//fin agregar producto al temp

		//Extrae datos del detalle temp
		if ($_POST['action'] == 'serchForDetalle'){

			if (empty($_POST['user'])) {
				echo 'error';
			}else{

				$token = md5($_SESSION['idUser']);

				$query = mysqli_query($obj_conexion,"SELECT tmp.correlativo,
															tmp.token_user,
															tmp.cantidad,
															tmp.precio_venta,
															p.codproducto,
															p.descripcion
													FROM detalle_temp tmp
													INNER JOIN producto p
													ON tmp.codproducto = p.codproducto
													WHERE token_user = '$token'");
				
				$result = mysqli_num_rows($query);

				$query_iva = mysqli_query($obj_conexion,"SELECT iva FROM configuracion");
				$result_iva = mysqli_num_rows($query_iva);

				$detalleTabla = '';
				$sub_total = 0;
				$iva = 0;
				$total = 0;
				$arrayData = array();

				if ($result > 0) {

					if ($result_iva > 0) {
						$info_iva = mysqli_fetch_assoc($query_iva);
						$iva = $info_iva['iva'];
					}
					while ($data = mysqli_fetch_assoc($query)) {
						$precioTotal = round($data['cantidad'] * $data['precio_venta'],2);
						$sub_total   = round($sub_total + $precioTotal,2);
						$total       = round($total + $precioTotal,2);

						$detalleTabla .= '<tr>
										 <td>'.$data['codproducto'].'</td>
										 <td colspan="2">'.$data['descripcion'].'</td>
										 <td class="textright">'.$data['cantidad'].'</td>
										 <td class="textright">'.$data['precio_venta'].'</td>
										 <td class="textright">'.$precioTotal.'</td>
										 <td class="">
										     <a class="link_delete" href="#" onclick="event.preventDefault(); del_product_detalle('.$data['correlativo'].');"><i class="far fa-trash-alt"></i></a>
										 </td>
										 </tr>
						';

					}//FIN WHILE CALCULO DEL IVA

					$impuesto = round($sub_total * ($iva / 100),2);
					$tl_sniva = round($sub_total - $impuesto,2);
					$total = round($tl_sniva + $impuesto,2);

					$detalleTotales = '<tr>
									   <td colspan="5" class="textright"> SUBTOTAL $.</td>
									   <td class="textright">'.$tl_sniva.'</td>
									   </tr>
									   <tr>
									   <td colspan="5" class="textright"> IVA ('.round($iva,0).'%)</td>
									   <td class="textright">'.$impuesto.'</td>
									   </tr>
									   <tr>
									   <td colspan="5" class="textright"> TOTAL $.</td>
									   <td class="textright">'.$total.'</td>
									   </tr>';

					$arrayData['detalle'] = $detalleTabla;
					$arrayData['totales'] = $detalleTotales;

					echo json_encode($arrayData,JSON_UNESCAPED_UNICODE);
				}else{
					echo 'error';
				}
				mysqli_close($obj_conexion);
			}
			exit;

		}//fin extraer datos del temp

		//eliminar productos del detalle_temp
	if ($_POST['action'] == 'del_product_detalle'){

		if (empty($_POST['id_detalle'])) {
				echo 'error';
			}else{

				$id_detalle = $_POST['id_detalle'];
				$token      = md5($_SESSION['idUser']);

				$query_iva = mysqli_query($obj_conexion,"SELECT iva FROM configuracion");
				$result_iva = mysqli_num_rows($query_iva);

				$query_detalle_temp = mysqli_query($obj_conexion,"CALL del_detalle_temp($id_detalle,'$token')");
				$result = mysqli_num_rows($query_detalle_temp);

				$detalleTabla = '';
				$sub_total = 0;
				$iva = 0;
				$total = 0;
				$arrayData = array();

				if ($result > 0) {

					if ($result_iva > 0) {
						$info_iva = mysqli_fetch_assoc($query_iva);
						$iva = $info_iva['iva'];
					}
					while ($data = mysqli_fetch_assoc($query_detalle_temp)) {
						$precioTotal = round($data['cantidad'] * $data['precio_venta'],2);
						$sub_total   = round($sub_total + $precioTotal,2);
						$total       = round($total + $precioTotal,2);

						$detalleTabla .= '<tr>
										 <td>'.$data['codproducto'].'</td>
										 <td colspan="2">'.$data['descripcion'].'</td>
										 <td class="textright">'.$data['cantidad'].'</td>
										 <td class="textright">'.$data['precio_venta'].'</td>
										 <td class="textright">'.$precioTotal.'</td>
										 <td class="">
										     <a class="link_delete" href="#" onclick="event.preventDefault(); del_product_detalle('.$data['correlativo'].');"><i class="far fa-trash-alt"></i></a>
										 </td>
										 </tr>
						';

					}//FIN WHILE CALCULO DEL IVA

					$impuesto = round($sub_total * ($iva / 100),2);
					$tl_sniva = round($sub_total - $impuesto,2);
					$total = round($tl_sniva + $impuesto,2);

					$detalleTotales = '<tr>
									   <td colspan="5" class="textright"> SUBTOTAL $.</td>
									   <td class="textright">'.$tl_sniva.'</td>
									   </tr>
									   <tr>
									   <td colspan="5" class="textright"> IVA ('.round($iva,0).'%)</td>
									   <td class="textright">'.$impuesto.'</td>
									   </tr>
									   <tr>
									   <td colspan="5" class="textright"> TOTAL $.</td>
									   <td class="textright">'.$total.'</td>
									   </tr>';

					$arrayData['detalle'] = $detalleTabla;
					$arrayData['totales'] = $detalleTotales;

					echo json_encode($arrayData,JSON_UNESCAPED_UNICODE);
				}else{
					echo 'error';
				}
				mysqli_close($obj_conexion);
			}
			exit;


	}//fin eliminar productos del detalle_temp

	//anular venta
	if ($_POST['action'] == 'anularVenta'){

		$token = md5($_SESSION['idUser']);

		$query_del = mysqli_query($obj_conexion,"DELETE FROM detalle_temp WHERE token_user = '$token'");
		mysqli_close($obj_conexion);

		if ($query_del) {
			echo 'ok';
		}else{
			echo 'error';
		}
		exit;
	}//fin anular venta

	//procesar venta
	if ($_POST['action'] == 'procesarVenta'){
	
		if (empty($_POST['codcliente'])) {
			$codcliente = 1;
		}else{
			$codcliente = $_POST['codcliente'];
		}

		$token   = md5($_SESSION['idUser']);
		$usuario = $_SESSION['idUser'];

		$query = mysqli_query($obj_conexion,"SELECT * FROM detalle_temp WHERE token_user = '$token'");
		$result = mysqli_num_rows($query);

		if ($result > 0) {
			$query_procesar = mysqli_query($obj_conexion,"CALL procesar_venta($usuario,$codcliente,'$token')");
			$result_detalle = mysqli_num_rows($query_procesar);

			if ($result_detalle > 0) {
				$data = mysqli_fetch_assoc($query_procesar);
				echo json_encode($data,JSON_UNESCAPED_UNICODE);
			}else{
				echo 'error';
			}
		}else{
			echo 'error';
		}
		mysqli_close($obj_conexion);
		exit;
	}//fin procesar venta

	//anular factura
	if ($_POST['action'] == 'infoFactura'){
		if (!empty($_POST['nofactura'])) {
			$nofactura = $_POST['nofactura'];
			$query = mysqli_query($obj_conexion,"SELECT * FROM factura WHERE nofactura = '$nofactura' AND estatus = 1");
			mysqli_close($obj_conexion);
			$result = mysqli_num_rows($query);

			if ($result > 0) {
				$data = mysqli_fetch_assoc($query);
				echo json_encode($data,JSON_UNESCAPED_UNICODE);
				exit;
			}
		}
		echo 'error';
		exit;

	}//fin anular factura

	//action btn_anular
	if ($_POST['action'] == 'anularFactura'){
		if (!empty($_POST['nofactura'])) {
			$nofactura = $_POST['nofactura'];

			$query_anular = mysqli_query($obj_conexion,"CALL anular_factura($nofactura)");
			mysqli_close($obj_conexion);
			$result = mysqli_num_rows($query_anular);

			if ($result > 0) {
				$data = mysqli_fetch_assoc($query_anular);
				echo json_encode($data,JSON_UNESCAPED_UNICODE);
				exit;	
			}
		}
		echo 'error';
		exit;

	}//fin action btn_anular

	}//fin del post
	exit;//1. exit

?>