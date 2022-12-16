//modal form add_product
$(document).ready(function() {
	//modal form delete product
	$('.add_product').click(function(e) {
		e.preventDefault();

		var producto = $(this).attr('product');
		var action = 'infoProducto';

		//inicio del ajax
		$.ajax({
			url: 'ajax.php',
			type: 'POST',
			async: true,
			data: {action:action,producto:producto},

			//correcto
			success: function(response){

				if (response != 'error') {


					var	info = JSON.parse(response);
					
					$('.bodyModal').html('<form action="" method="post" name="form_add_product" id="form_add_product" onsubmit="event.preventDefault(); sendDataProduct();">'+
									'<h1><i class="fas fa-cubes" style="font-size: 45pt;"></i> <br> Agregar producto</h1>'+
									'<h2 class="nameProducto">'+info.descripcion+'</h2><br>'+
									'<input type="number" name="cantidad" id="txtCantidad" placeholder="Cantidad del producto" required><br>'+
									'<input type="text" name="precio" id="txtPrecio" placeholder="Precio del producto" required>'+
									'<input type="hidden" name="producto_id" id="producto_id" value = "'+info.codproducto+'"required>'+
									'<input type="hidden" name="action" value="addProduct" required>'+
									'<div class="alert alertAddProduct"></div>'+
									'<button type="submit" class="btn_new"><i class="fas fa-plus"></i> Agregar</button>'+
									'<a href="#" class="btn_ok closeModal" onclick="closeModal();"><i class="fas fa-ban"></i> Cerrar</a>'+
									'</form>');
				}

			},
			// error
			error: function(error){
				console.log(error);
			},

		});// fin del ajax
		
		$('.modal').fadeIn();

	});//fin del add product

	//modal form delete product
	$('.del_product').click(function(e) {
		e.preventDefault();

		var producto = $(this).attr('product');
		var action = 'infoProducto';

		//inicio del ajax
		$.ajax({
			url: 'ajax.php',
			type: 'POST',
			async: true,
			data: {action:action,producto:producto},

			//correcto
			success: function(response){

				if (response != 'error') {


					var	info = JSON.parse(response);
					
					$('.bodyModal').html('<form action="" method="post" name="form_del_product" id="form_del_product" onsubmit="event.preventDefault(); delProduct();">'+
									'<h1><i class="fas fa-cubes" style="font-size: 45pt;"></i> <br> Eliminar producto</h1>'+
									'<br>'+
									'<p style="color: #0a0909"> ¿Está seguro de eliminar el siguiente registro?</p>'+
									'<br>'+
									'<p style="color: #0a0909">Nombre del Producto: <span></span></p>'+
									'<h2 class="nameProducto">'+info.descripcion+'</h2><br>'+
									'<input type="hidden" name="producto_id" id="producto_id" value = "'+info.codproducto+'"required>'+
									'<input type="hidden" name="action" value="delProduct" required>'+
									'<div class="alert alertAddProduct"></div>'+
									'<a href="#" class="btn_cancel" onclick="closeModal();"><i class="fas fa-ban"></i> Cancelar</a>'+
									'<button type="submit" class="btn_ok"><i class="fas fa-trash-alt"></i> Eliminar</button>'+
									'</form>');
				}

			},
			// error
			error: function(error){
				console.log(error);
			},

		});// fin del ajax
		
		$('.modal').fadeIn();

	});//fin del delete product
	//evento buscar
	$('#search_proveedor').change(function(e) {
		e.preventDefault();
			var sistema = getUrl();
			location.href = sistema+'buscar_productos.php?proveedor='+$(this).val();

	});//fin evento buscar
	//Activa campo para registrar clientes
	$('.btn_new_cliente').click(function(e) {
		e.preventDefault();
		$('#nom_cliente').removeAttr('disabled');
		$('#tel_cliente').removeAttr('disabled');
		$('#dir_cliente').removeAttr('disabled');

		$('#div_registro_cliente').slideDown();
	});//fin new cliente.

	//buscar cliente.
	$('#dni_cliente').keyup(function(e) {
		e.preventDefault();
		var cl = $(this).val();
		var action = 'searchCliente';

		$.ajax({
			url: 'ajax.php',
			type: 'POST',
			async: true,
			data: {action:action,cliente:cl},

			success: function(response){
				if (response == 0) {
					$('#idcliente').val('');
					$('#nom_cliente').val('');
					$('#tel_cliente').val('');
					$('#dir_cliente').val('');
					//mostrar boton crear
					$('.btn_new_cliente').slideDown();
				}else{
					var data = $.parseJSON(response);
					$('#idcliente').val(data.idcliente);
					$('#nom_cliente').val(data.nombre);
					$('#tel_cliente').val(data.telefono);
					$('#dir_cliente').val(data.direccion);
					//ocultar boton crear
					$('.btn_new_cliente').slideUp();

					//bloque campos
					$('#nom_cliente').attr('disabled','disabled');
					$('#tel_cliente').attr('disabled','disabled');
					$('#dir_cliente').attr('disabled','disabled');

					//ocultar boton guardar
					$('#div_registro_cliente').slideUp();
				}
			},

			error: function(error){
		
			},

		});//fin ajax
		
	});//fin buscar cliente.

	//crear cliente - venta
	$('#form_new_cliente_venta').submit(function(e) {
		e.preventDefault();

			$.ajax({
			url: 'ajax.php',
			type: 'POST',
			async: true,
			data: $('#form_new_cliente_venta').serialize(),

			success: function(response){
				
				if (response != 'error') {

					$('#idcliente').val(response);

					$('#nom_cliente').attr('disabled','disabled');
					$('#tel_cliente').attr('disabled','disabled');
					$('#dir_cliente').attr('disabled','disabled');
					//btn_guardar
					$('.btn_new_cliente').slideUp();
					//btn_new_cliente
					$('#div_registro_cliente').slideUp();

				}

			},

			error: function(error){
		
			},

		});//fin ajax

	});//fin crear cliente - venta

	// buscar producto
	$('#txt_cod_producto').keyup(function(e) {
		e.preventDefault();

		var producto = $(this).val();
		var action = 'infoProducto';

		if (producto != '') {

			$.ajax({
			url: 'ajax.php',
			type: 'POST',
			async: true,
			data: {action:action,producto:producto},

				success: function(response){
					
					if(response != 'error'){

						var info = JSON.parse(response);

						$('#txt_descripcion').html(info.descripcion);
						$('#txt_existencia').html(info.existencia);
						$('#txt_cant_producto').val('1');
						$('#txt_precio').html(info.precio);
						$('#txt_precio_total').html(info.precio);

						$('#txt_cant_producto').removeAttr('disabled');

						$('#add_product_venta').slideDown();

					}else{
						$('#txt_descripcion').html('-');
						$('#txt_existencia').html('-');
						$('#txt_cant_producto').val('0');
						$('#txt_precio').html('0.00');
						$('#txt_precio_total').html('0.00');

						$('#txt_cant_producto').attr('disabled','disabled');

						$('#add_product_venta').slideUp();
					}
					if($('#txt_existencia').html() == '0'){

						$('#txt_cant_producto').attr('disabled','disabled');
						$('#txt_existencia').html('Agotado!');
						$('#txt_cant_producto').val('0');
						$('#txt_precio').html('0.00');
						$('#txt_precio_total').html('0.00');

						$('#add_product_venta').slideUp();
					}
				},

				error: function(error){
			
				},

			});//fin ajax
		}

	});//fin buscar producto.

	//validar cantidad del producto antes de agregar
	$('#txt_cant_producto').keyup(function(e) {
		e.preventDefault();

		var precio_total = ($(this).val() * $('#txt_precio').html()).toFixed(2);
		var existencia = parseInt($('#txt_existencia').html());
		$('#txt_precio_total').html(precio_total);

		if ( ($(this).val() < 1 || isNaN($(this).val())) || ($(this).val() > existencia) ) {
			$('#txt_precio_total').html('0.00');
			$('#add_product_venta').slideUp();
		}else{
			$('#add_product_venta').slideDown();
		}

	});

	//Agregar producto al detalle
	$('#add_product_venta').click(function(e) {
		
		e.preventDefault();

		if ($('#txt_cant_producto').val() > 0) {

			var codproducto = $('#txt_cod_producto').val();
			var cantidad    = $('#txt_cant_producto').val();
			var action      =  'addProductoDetalle';

			$.ajax({
			url: 'ajax.php',
			type: 'POST',
			async: true,
			data: {action:action,producto:codproducto,cantidad:cantidad},

				success: function(response){
					
					if(response != 'error'){

						var info = JSON.parse(response);

						$('#detalle_venta').html(info.detalle);
						$('#detalle_totales').html(info.totales);

						$('#txt_cod_producto').val('');
						$('#txt_descripcion').html('-');
						$('#txt_existencia').html('-');
						$('#txt_cant_producto').val('0');
						$('#txt_precio').html('0.00');
						$('#txt_precio_total').html('0.00');
						//bloquear cantidad
						$('#txt_cant_producto').attr('disabled','disabled');
						//ocultar agregar
						$('#add_product_venta').slideUp();					

					}else{
						console.log('no data');
					}

					viewProcesar();
				},

				error: function(error){
				console.log('no data');
				},

			});
		}

	});//fin Agregar producto al detalle

	//anular venta
	$('#btn_anular_venta').click(function(e) {
		e.preventDefault();

		var rows = $('#detalle_venta tr').length;

		if (rows > 0) {
			var action = 'anularVenta';
			$.ajax({
			url: 'ajax.php',
			type: 'POST',
			async: true,
			data: {action:action},

				success: function(response){

					if (response != 'error') {
						location.reload();
					}

				},

				error: function(error){
				},

			});
		}

	});//fin anular venta

	//Procesar venta
	$('#btn_facturar_venta').click(function(e) {
		e.preventDefault();

		var rows = $('#detalle_venta tr').length;

		if (rows > 0) {
			var action = 'procesarVenta';
			var codcliente = $('#idcliente').val();

			$.ajax({
			url: 'ajax.php',
			type: 'POST',
			async: true,
			data: {action:action,codcliente:codcliente},

				success: function(response){

					if (response != 'error') {
						var info = JSON.parse(response);
						
						generarPDF(info.codcliente,info.nofactura);
						location.reload();

					}else{
						console.log('no data');
					}
				},

				error: function(error){
				},

			});
		}		

	});//fin Procesar venta

	//anular venta.
	$('.anular_factura').click(function(e) {
		e.preventDefault();

		var nofactura = $(this).attr('fac');
		var action = 'infoFactura';

		//inicio del ajax
		$.ajax({
			url: 'ajax.php',
			type: 'POST',
			async: true,
			data: {action:action,nofactura:nofactura},

			//correcto
			success: function(response){

				if (response != 'error') {

					var	info = JSON.parse(response);

					
					$('.bodyModal').html('<form action="" method="post" name="from_anular_factura" id="from_anular_factura" onsubmit="event.preventDefault(); anularFactura();">'+
									'<h1><i class="fas fa-cubes" style="font-size: 45pt;"></i> <br> Anular Factura</h1>'+
									'<br>'+
									'<p style="color: #0a0909"> ¿Realmente desea anular la factura?</p>'+
									'<br>'+
									'<p><strong>No. '+info.nofactura+'</strong></p>'+
									'<p><strong>Monto. $'+info.totalfactura+'</strong></p>'+
									'<p><strong>Fecha. '+info.fecha+'</strong></p>'+
									'<input type="hidden" name="action" value="anularFactura">'+
									'<input type="hidden" name="no_factura" id="no_factura" value="'+info.nofactura+'" required>'+
									'<div class="alert alertAddProduct"></div>'+
									'<button type="submit" class="btn_ok"><i class="fas fa-ban"></i> Anular</button>'+
									'<a href="#" class="btn_cancel" onclick="closeModal();"><i class="far fa-window-close"></i> Cancelar</a>'+
									'</form>');
				}

			},
			error: function(error){
				console.log(error);
			},

		});// fin del ajax
		
		$('.modal').fadeIn();

	});//fin del anular venta

	//btn_ver_factura
	$('.view_factura').click(function(e) {
		e.preventDefault();
		var codCliente = $(this).attr('cl');
		var noFactura = $(this).attr('f');

		generarPDF(codCliente,noFactura);

	});//fin btn_ver_factura

});//fin del document.

function anularFactura(){
	var nofactura = $('#no_factura').val();
	var action = 'anularFactura';

			$.ajax({
			url: 'ajax.php',
			type: 'POST',
			async: true,
			data: {action:action,nofactura:nofactura},

				success: function(response){
					if (response == 'error') {
						$('.alertAddProduct').html('<p style="color:red;">Error al anular la factura</p>');
					}else{
						$('#rows_'+nofactura+' .estado').html('<span class="anulada">Anulada</<span>');
						$('#from_anular_factura .btn_ok').remove();
						$('#rows_'+nofactura+' .div_factura').html('<button type="button" class="btn_anular anactive"><i class="fas fa-ban"></i></button>');
						$('.alertAddProduct').html('<p>Factura anulada</p>');
					}
				},

				error: function(error){
				},

			});
}

function generarPDF(cliente,factura){
	var ancho = 1000;
	var alto = 600;

	var x = parseInt((window.screen.width/2) - (ancho / 2));
	var y = parseInt((window.screen.height/2) - (alto / 2));

	$url = 'factura/generaFactura.php?cl='+cliente+'&f='+factura;
	window.open($url,"Factura","left="+x+",top="+y+",height="+alto+",width="+ancho+",scrollbar=si,location=no,resizable=si,menubar=no");

}

function del_product_detalle(correlativo){

	var action = 'del_product_detalle';
	var id_detalle = correlativo;

		    $.ajax({
			url: 'ajax.php',
			type: 'POST',
			async: true,
			data: {action:action,id_detalle:id_detalle},

				success: function(response){

					if (response != 'error') {

						var info = JSON.parse(response);

					    $('#detalle_venta').html(info.detalle);
						$('#detalle_totales').html(info.totales);

						$('#txt_cod_producto').val('');
						$('#txt_descripcion').html('-');
						$('#txt_existencia').html('-');
						$('#txt_cant_producto').val('0');
						$('#txt_precio').html('0.00');
						$('#txt_precio_total').html('0.00');
						//bloquear cantidad
						$('#txt_cant_producto').attr('disabled','disabled');
						//ocultar agregar
						$('#add_product_venta').slideUp();					



					}else{
						$('#detalle_venta').html('');
						$('#detalle_totales').html('');						
					}
					viewProcesar();
				},

				error: function(error){
				console.log('no data');
				},

			});
}
//mostar/ocultar boton procesar
function viewProcesar(){
	if ($('#detalle_venta tr').length > 0) {
        $('#btn_facturar_venta').show();
	}else{
		$('#btn_facturar_venta').hide();
	}
}

function serchForDetalle(id){

	var action = 'serchForDetalle';
	var user = id;

		    $.ajax({
			url: 'ajax.php',
			type: 'POST',
			async: true,
			data: {action:action,user:user},

				success: function(response){

					if(response != 'error'){

						var info = JSON.parse(response);

						$('#detalle_venta').html(info.detalle);
						$('#detalle_totales').html(info.totales);

					}else{
						console.log('no data');
					}
					viewProcesar();
				},

				error: function(error){
				console.log('no data');
				},

			});
}

function getUrl(){

	var loc = window.location;
	var pathName = loc.pathname.substring(0, loc.pathname.lastIndexOf('/') + 1);
	return loc.href.substring(0, loc.href.length - ((loc.pathname + loc.search + loc.hash).length - pathName.length));

}


//funcion enviar datos
function sendDataProduct(){
	$('.alertAddProduct').html('');
		//inicio del ajax agregar
		$.ajax({
			url: 'ajax.php',
			type: 'POST',
			async: true,
			data: $('#form_add_product').serialize(),

			//correcto
			success: function(response){
				if (response == 'error') {
					$('.alertAddProduct').html('<p style = "color: red;">Error al agregar el producto.</p>');
				}else{

					var	info = JSON.parse(response);

					$('.row'+info.producto_id+' .celPrecio').html(info.nuevo_precio);
					$('.row'+info.producto_id+' .celExistencia').html(info.nueva_existencia);
					$('#txtCantidad').val('');
					$('#txtPrecio').val('');
					$('.alertAddProduct').html("<p> Producto almacenado correctamente. </p>");
				}
			},
			// error
			error: function(error){
				console.log(error);
			},

		});// fin del ajax agregar

}


//funcion eliminar datos
function delProduct(){

	var pr = $('#producto_id').val();

	$('.alertAddProduct').html('');

		//inicio del ajax agregar
		$.ajax({
			url: 'ajax.php',
			type: 'POST',
			async: true,
			data: $('#form_del_product').serialize(),

			//correcto
			success: function(response){

				if (response == 'error') {
					$('.alertAddProduct').html('<p style = "color: red;">Error al eliminar el producto.</p>');
				}else{

					$('.row'+pr).remove();
					$('#form_del_product .btn_ok').remove();
					$('.alertAddProduct').html("<p> Producto eliminado correctamente. </p>");
				}
			},
			// error
			error: function(error){
				console.log(error);
			},

		});// fin del ajax eliminar

}

//funcion cerrar
function closeModal(){
	$('.alertAddProduct').html('');
	$('#txtCantidad').val('');
	$('#txtPrecio').val('');
	$('.modal').fadeOut();	
}
