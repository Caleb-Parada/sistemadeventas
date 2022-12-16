-- phpMyAdmin SQL Dump
-- version 5.2.0
-- https://www.phpmyadmin.net/
--
-- Servidor: 127.0.0.1
-- Tiempo de generación: 02-11-2022 a las 22:32:14
-- Versión del servidor: 10.4.24-MariaDB
-- Versión de PHP: 8.1.6

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
START TRANSACTION;
SET time_zone = "+00:00";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8mb4 */;

--
-- Base de datos: `facturacion`
--

DELIMITER $$
--
-- Procedimientos
--
CREATE DEFINER=`root`@`localhost` PROCEDURE `actualizar_precio_producto` (IN `n_cantidad` INT, IN `n_precio` DECIMAL(10,2), IN `codigo` INT)   BEGIN
    	  DECLARE nueva_existencia int;
        DECLARE nuevo_total  decimal(10,2);
        DECLARE nuevo_precio decimal(10,2);
        
        DECLARE cant_actual int;
        DECLARE pre_actual decimal(10,2);
        
        DECLARE actual_existencia int;
        DECLARE actual_precio decimal(10,2);
                
SELECT precio,existencia INTO actual_precio,actual_existencia FROM producto WHERE codproducto = codigo;
        
SET nueva_existencia = actual_existencia + n_cantidad;
SET nuevo_total = (actual_existencia * actual_precio) + (n_cantidad * n_precio);
SET nuevo_precio = nuevo_total / nueva_existencia;
        
UPDATE producto SET existencia = nueva_existencia, precio = nuevo_precio WHERE codproducto = codigo;
        
SELECT nueva_existencia,nuevo_precio;
        
END$$

CREATE DEFINER=`root`@`localhost` PROCEDURE `add_detalle_temp` (`codigo` INT, `cantidad` INT, `token_user` VARCHAR(50))   BEGIN

		DECLARE precio_actual decimal(10,2);
		SELECT precio INTO precio_actual FROM producto WHERE codproducto = codigo;

		INSERT INTO detalle_temp(token_user,codproducto,cantidad,precio_venta) VALUES(token_user,codigo,cantidad,precio_actual);

		SELECT tmp.correlativo, tmp.codproducto, p.descripcion, tmp.cantidad, tmp.precio_venta FROM detalle_temp tmp
		INNER JOIN producto p
		ON tmp.codproducto = p.codproducto
		WHERE tmp.token_user = token_user;

	END$$

CREATE DEFINER=`root`@`localhost` PROCEDURE `anular_factura` (`no_factura` INT)   BEGIN
    	 DECLARE existe_factura int;
        DECLARE registros int;
        DECLARE a int;
        
        DECLARE cod_producto int;
        DECLARE cant_producto int;
        DECLARE existencia_actual int;
        DECLARE nueva_existencia int;
        
        SET existe_factura = (SELECT COUNT(*) FROM factura WHERE nofactura = no_factura AND estatus = 1);
        
        IF existe_factura > 0 THEN
        	CREATE TEMPORARY TABLE tbl_tmp (
                id BIGINT NOT NULL AUTO_INCREMENT PRIMARY KEY,
                cod_prod BIGINT,
                cant_prod int);
                SET a = 1;
                SET registros = (SELECT COUNT(*) FROM detallefactura WHERE nofactura = no_factura);
                
                IF registros > 0 THEN
                	INSERT INTO tbl_tmp(cod_prod,cant_prod) SELECT codproducto,cantidad FROM detallefactura WHERE nofactura = no_factura;
                    
                    WHILE a <= registros DO
                   		SELECT cod_prod,cant_prod INTO cod_producto,cant_producto FROM tbl_tmp WHERE id = a;
                        SELECT existencia INTO existencia_actual FROM producto WHERE codproducto = cod_producto;
                        SET nueva_existencia = existencia_actual + cant_producto;
                        UPDATE producto SET existencia = nueva_existencia WHERE codproducto = cod_producto;
                        SET a = a+1;
                    END WHILE;
                    
                     	UPDATE factura SET estatus = 2 WHERE nofactura = no_factura;
                        DROP TABLE tbl_tmp;
                        SELECT* FROM factura WHERE nofactura = no_factura;
                
                END IF;
        ELSE
        	SELECT 0 factura;
        END IF;
    
    END$$

CREATE DEFINER=`root`@`localhost` PROCEDURE `del_detalle_temp` (`id_detalle` INT, `token` VARCHAR(50))   BEGIN 
    	DELETE FROM detalle_temp WHERE correlativo = id_detalle;
        
        SELECT tmp.correlativo, tmp.codproducto, p.descripcion, tmp.cantidad, tmp.precio_venta FROM detalle_temp tmp
        INNER JOIN producto p
        ON tmp.codproducto = p.codproducto
        WHERE tmp.token_user = token;
    
    END$$

CREATE DEFINER=`root`@`localhost` PROCEDURE `procesar_venta` (IN `cod_usuario` INT, IN `cod_cliente` INT, IN `token` VARCHAR(50))   BEGIN
    	DECLARE factura INT;
        DECLARE registros INT;
        DECLARE total DECIMAL(10,2);
        
        DECLARE nueva_existencia int;
        DECLARE existencia_actual int;
        
        DECLARE tmp_cod_producto int;
        DECLARE tmp_cant_producto int;
        DECLARE a INT;
        SET a = 1;
        
        CREATE TEMPORARY TABLE tbl_tmp_tokenuser(
            id BIGINT NOT NULL AUTO_INCREMENT PRIMARY KEY,
            cod_prod BIGINT,
            cant_prod int);
            
            SET registros = (SELECT COUNT(*) FROM detalle_temp WHERE token_user = token);
            
            IF registros > 0 THEN
            	
                INSERT INTO tbl_tmp_tokenuser(cod_prod,cant_prod) SELECT codproducto,cantidad FROM detalle_temp WHERE token_user = token;
                
                INSERT INTO factura(usuario,codcliente) VALUES(cod_usuario,cod_cliente);
                SET factura = LAST_INSERT_ID();
                
            INSERT INTO detallefactura(nofactura,codproducto,cantidad,precio_venta) SELECT (factura) as    			nofactura,codproducto,cantidad,precio_venta FROM detalle_temp
            WHERE token_user = token;
            
            	WHILE a <= registros DO
                SELECT cod_prod,cant_prod INTO tmp_cod_producto,tmp_cant_producto FROM tbl_tmp_tokenuser WHERE id = a;
                SELECT existencia INTO existencia_actual FROM producto WHERE codproducto = tmp_cod_producto;
                
                SET nueva_existencia = existencia_actual - tmp_cant_producto;
                UPDATE producto SET existencia = nueva_existencia WHERE codproducto = tmp_cod_producto; 
                
                SET a = a+1;
                
                END WHILE;
                
                SET total = (SELECT SUM(cantidad * precio_venta) FROM detalle_temp WHERE token_user = token);
                UPDATE factura SET totalfactura = total WHERE nofactura = factura;
                DELETE FROM detalle_temp WHERE token_user = token;
                TRUNCATE TABLE tbl_tmp_tokenuser;
                SELECT* FROM factura WHERE nofactura = factura;
                
            ELSE
            	SELECT 0;
            
            END IF;
    
    END$$

DELIMITER ;

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `cliente`
--

CREATE TABLE `cliente` (
  `idcliente` int(11) NOT NULL,
  `dni` int(15) DEFAULT NULL,
  `nombre` varchar(80) DEFAULT NULL,
  `telefono` text DEFAULT NULL,
  `direccion` text DEFAULT NULL,
  `dateadd` datetime NOT NULL DEFAULT current_timestamp(),
  `usuario_id` int(11) NOT NULL,
  `estatus` int(11) NOT NULL DEFAULT 1
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

--
-- Volcado de datos para la tabla `cliente`
--

INSERT INTO `cliente` (`idcliente`, `dni`, `nombre`, `telefono`, `direccion`, `dateadd`, `usuario_id`, `estatus`) VALUES
(1, 0, 'CF', '000000000', 'dir_cliente', '2019-10-15 22:49:07', 21, 1),
(2, 0, 'andrea fernandez', '675432355', 'puerto ', '2019-10-16 07:13:47', 21, 1),
(3, 0, 'juanes andrade', '3126759876', 'villa rica', '2019-10-16 07:26:57', 21, 0),
(4, 0, 'juanes andrade2', '1312434', 'dorco', '2019-10-16 10:16:45', 21, 0),
(5, 25597470, 'luz aleida melendez', '3136389804', 'valle del ortigal', '2019-10-16 23:22:35', 21, 1),
(6, 1223455, 'francisco', '1234443', 'colon paraiso', '2019-11-08 13:10:50', 21, 1),
(7, 12345, 'juan carlos pineda', '3227456543', 'ciudad jardin', '2019-11-08 13:43:51', 21, 1),
(8, 23468976, 'Fernando calzada', '3126547989', 'lomas de granada', '2019-11-09 19:14:27', 26, 1),
(9, 1087108049, 'Diana elsy ordoÃ±ez', '3143841451', 'Lomas de granada', '2019-11-11 19:18:07', 26, 1),
(10, 1002836084, 'cristian bambague', '3124729230', 'valle ', '2019-11-14 18:20:22', 26, 1);

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `configuracion`
--

CREATE TABLE `configuracion` (
  `id` bigint(20) NOT NULL,
  `nit` varchar(20) NOT NULL,
  `nombre` varchar(100) NOT NULL,
  `razon_social` varchar(100) NOT NULL,
  `telefono` bigint(20) NOT NULL,
  `email` varchar(200) NOT NULL,
  `direccion` text NOT NULL,
  `iva` decimal(10,2) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

--
-- Volcado de datos para la tabla `configuracion`
--

INSERT INTO `configuracion` (`id`, `nit`, `nombre`, `razon_social`, `telefono`, `email`, `direccion`, `iva`) VALUES
(1, '40779260-8', 'EL PUNTO PERFECTO', '', 3117854536, 'sebastian24@gmail.com', 'Calle 7A # 17-60 B/La esmeralda', '19.00');

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `detallefactura`
--

CREATE TABLE `detallefactura` (
  `correlativo` bigint(11) NOT NULL,
  `nofactura` bigint(11) DEFAULT NULL,
  `codproducto` int(11) DEFAULT NULL,
  `cantidad` int(11) DEFAULT NULL,
  `precio_venta` decimal(10,2) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

--
-- Volcado de datos para la tabla `detallefactura`
--

INSERT INTO `detallefactura` (`correlativo`, `nofactura`, `codproducto`, `cantidad`, `precio_venta`) VALUES
(3, 1, 25, 5, '2000.00'),
(4, 2, 25, 5, '2000.00'),
(5, 2, 24, 200, '29583.33'),
(6, 2, 15, 22, '499.31'),
(7, 3, 25, 5, '2000.00'),
(8, 3, 12, 1, '7512.63'),
(10, 4, 25, 5, '2000.00'),
(11, 4, 15, 50, '499.31'),
(12, 4, 16, 1, '170.00'),
(13, 5, 12, 1, '7512.63'),
(14, 5, 13, 1, '24963.40'),
(16, 6, 25, 1, '2000.00'),
(17, 6, 25, 4, '2000.00'),
(19, 7, 25, 5, '2000.00'),
(20, 7, 12, 1, '7512.63'),
(21, 7, 24, 500, '29583.33'),
(22, 8, 23, 1, '5335.00'),
(23, 9, 12, 1, '7512.63'),
(24, 10, 25, 5, '2000.00'),
(25, 11, 25, 10, '2000.00'),
(26, 12, 25, 5, '2000.00'),
(27, 13, 25, 5, '2000.00'),
(28, 14, 12, 1, '7512.63'),
(29, 15, 13, 1, '24963.40'),
(30, 15, 14, 1, '23730.19'),
(32, 16, 14, 30, '23730.19'),
(33, 16, 15, 4, '499.31'),
(34, 16, 18, 5, '847.06'),
(35, 17, 12, 1, '7512.63'),
(36, 17, 13, 1, '24963.40'),
(38, 18, 25, 2, '2000.00'),
(39, 18, 20, 1, '392156.86'),
(41, 19, 23, 1, '5335.00'),
(42, 19, 24, 1, '29583.33'),
(43, 20, 25, 2, '2000.00'),
(44, 20, 24, 1, '29583.33'),
(46, 21, 25, 1, '2000.00'),
(47, 22, 24, 20, '29583.33'),
(48, 23, 25, 20, '2000.00'),
(49, 23, 23, 10, '5335.00'),
(50, 23, 24, 1, '29583.33'),
(51, 23, 20, 1, '392156.86'),
(52, 23, 19, 1, '285714.29'),
(53, 23, 18, 1, '847.06'),
(55, 24, 25, 30, '2000.00'),
(56, 25, 25, 10, '2000.00'),
(57, 26, 25, 10, '2000.00'),
(58, 27, 25, 30, '2000.00'),
(59, 28, 12, 80, '7512.63'),
(60, 28, 23, 88, '5335.00'),
(62, 29, 25, 11, '2000.00'),
(63, 30, 26, 12, '1000.00'),
(64, 30, 27, 12, '1800.00'),
(65, 30, 28, 12, '300.00'),
(66, 31, 26, 10, '1000.00'),
(67, 31, 27, 10, '1800.00'),
(68, 31, 28, 10, '300.00'),
(69, 32, 26, 2, '1000.00'),
(70, 32, 27, 2, '1800.00'),
(72, 33, 28, 1, '300.00'),
(73, 34, 12, 1, '7512.63'),
(74, 35, 15, 1, '499.31'),
(75, 36, 14, 1, '23730.19'),
(76, 37, 15, 1, '499.31'),
(77, 38, 15, 1, '499.31'),
(78, 39, 15, 1, '499.31');

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `detalle_temp`
--

CREATE TABLE `detalle_temp` (
  `correlativo` int(11) NOT NULL,
  `token_user` varchar(50) NOT NULL,
  `codproducto` int(11) NOT NULL,
  `cantidad` int(11) NOT NULL,
  `precio_venta` decimal(10,2) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `entradas`
--

CREATE TABLE `entradas` (
  `correlativo` int(11) NOT NULL,
  `codproducto` int(11) NOT NULL,
  `fecha` datetime NOT NULL DEFAULT current_timestamp(),
  `cantidad` int(11) NOT NULL,
  `precio` decimal(10,2) NOT NULL,
  `usuario_id` int(11) NOT NULL DEFAULT 1
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

--
-- Volcado de datos para la tabla `entradas`
--

INSERT INTO `entradas` (`correlativo`, `codproducto`, `fecha`, `cantidad`, `precio`, `usuario_id`) VALUES
(40, 14, '2019-11-06 07:10:42', 100, '50000.00', 22),
(41, 14, '2019-11-06 07:11:51', 100, '30000.00', 22),
(42, 20, '2019-11-06 07:24:19', 100, '1200000.00', 22),
(43, 21, '2019-11-06 08:13:57', 5, '1200000.00', 22),
(44, 21, '2019-11-06 08:14:13', 10, '1300000.00', 22),
(45, 22, '2019-11-06 08:16:24', 5, '1200000.00', 22),
(46, 22, '2019-11-06 08:17:15', 10, '1300000.00', 22),
(47, 22, '2019-11-06 08:23:03', 10, '1400000.00', 22),
(48, 23, '2019-11-06 09:44:40', 24, '5000.00', 22),
(49, 23, '2019-11-06 09:44:58', 10, '2000.00', 22),
(50, 23, '2019-11-06 09:46:21', 15, '3000.00', 22),
(51, 23, '2019-11-06 11:49:41', 11, '2000.00', 22),
(52, 24, '2019-11-06 13:05:54', 100, '25000.00', 22),
(53, 24, '2019-11-06 13:06:38', 100, '30000.00', 22),
(54, 24, '2019-11-07 14:30:38', 1000, '30000.00', 20),
(55, 23, '2019-11-07 14:30:55', 20, '3000.00', 20),
(56, 23, '2019-11-07 14:31:15', 20, '30000.00', 20),
(57, 23, '2019-11-07 19:46:45', 100, '2000.00', 20),
(58, 25, '2019-11-09 22:48:31', 50, '2000.00', 20),
(59, 25, '2019-11-10 12:09:20', 15, '2000.00', 20),
(60, 25, '2019-11-10 17:38:11', 100, '2000.00', 20),
(61, 25, '2019-11-11 09:32:46', 9, '2000.00', 20),
(62, 26, '2019-11-11 19:15:35', 24, '1000.00', 20),
(63, 27, '2019-11-11 19:16:20', 24, '1800.00', 20),
(64, 28, '2019-11-11 19:20:12', 24, '10.00', 20),
(65, 29, '2019-11-11 19:36:53', 6, '23500.00', 20),
(66, 30, '2019-11-11 19:37:17', 10, '70000.00', 20);

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `factura`
--

CREATE TABLE `factura` (
  `nofactura` bigint(11) NOT NULL,
  `fecha` datetime NOT NULL DEFAULT current_timestamp(),
  `usuario` int(11) DEFAULT NULL,
  `codcliente` int(11) DEFAULT NULL,
  `totalfactura` decimal(10,2) DEFAULT NULL,
  `estatus` int(11) NOT NULL DEFAULT 1
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

--
-- Volcado de datos para la tabla `factura`
--

INSERT INTO `factura` (`nofactura`, `fecha`, `usuario`, `codcliente`, `totalfactura`, `estatus`) VALUES
(1, '2019-11-10 11:44:11', 26, 1, '10000.00', 1),
(2, '2019-11-10 11:50:32', 26, 1, '5937650.82', 1),
(3, '2019-11-10 13:16:53', 26, 5, '17512.63', 1),
(4, '2019-11-10 13:21:17', 26, 1, '35135.50', 1),
(5, '2019-11-10 13:22:07', 26, 1, '32476.03', 1),
(6, '2019-11-10 13:52:12', 26, 1, '10000.00', 1),
(7, '2019-11-10 14:39:51', 26, 5, '14809177.63', 1),
(8, '2019-11-10 14:41:04', 26, 5, '5335.00', 1),
(9, '2019-11-10 14:41:28', 26, 5, '7512.63', 1),
(10, '2019-11-10 14:43:31', 26, 5, '10000.00', 1),
(11, '2019-11-10 14:45:25', 26, 5, '20000.00', 1),
(12, '2019-11-10 14:46:28', 26, 5, '10000.00', 1),
(13, '2019-11-10 14:48:08', 26, 1, '10000.00', 1),
(14, '2019-11-10 14:49:58', 26, 5, '7512.63', 1),
(15, '2019-11-10 14:52:53', 26, 5, '48693.59', 1),
(16, '2019-11-10 14:56:03', 26, 5, '718138.24', 1),
(17, '2019-11-10 14:59:06', 26, 1, '32476.03', 1),
(18, '2019-11-10 15:01:01', 26, 5, '396156.86', 1),
(19, '2019-11-10 15:04:21', 26, 5, '34918.33', 1),
(20, '2019-11-10 17:19:57', 26, 5, '33583.33', 1),
(21, '2019-11-10 17:21:01', 26, 5, '2000.00', 2),
(22, '2019-11-10 17:23:25', 26, 5, '591666.60', 2),
(23, '2019-11-10 17:39:43', 26, 1, '801651.54', 1),
(24, '2019-11-10 17:40:50', 26, 5, '60000.00', 1),
(25, '2019-11-10 17:45:02', 26, 5, '20000.00', 1),
(26, '2019-11-10 17:45:51', 26, 5, '20000.00', 2),
(27, '2019-11-10 17:50:05', 26, 1, '60000.00', 2),
(28, '2019-11-10 21:51:16', 26, 5, '1070490.40', 2),
(29, '2019-11-10 23:45:37', 26, 5, '22000.00', 2),
(30, '2019-11-11 19:20:42', 26, 9, '37200.00', 1),
(31, '2019-11-11 19:41:48', 26, 9, '31000.00', 1),
(32, '2019-11-14 18:21:34', 26, 10, '5600.00', 2),
(33, '2022-08-03 18:39:36', 27, 7, '300.00', 1),
(34, '2022-09-26 18:50:45', 27, 7, '7512.63', 1),
(35, '2022-09-26 18:57:29', 27, 7, '499.31', 1),
(36, '2022-09-26 18:59:04', 27, 7, '23730.19', 1),
(37, '2022-09-26 19:02:53', 27, 7, '499.31', 1),
(38, '2022-09-26 19:05:23', 27, 7, '499.31', 1),
(39, '2022-09-26 19:05:44', 27, 7, '499.31', 1);

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `producto`
--

CREATE TABLE `producto` (
  `codproducto` int(11) NOT NULL,
  `descripcion` varchar(100) DEFAULT NULL,
  `proveedor` int(11) DEFAULT NULL,
  `precio` decimal(10,2) DEFAULT NULL,
  `existencia` int(11) DEFAULT NULL,
  `date_add` datetime NOT NULL DEFAULT current_timestamp(),
  `usuario_id` int(11) NOT NULL,
  `estatus` int(11) NOT NULL DEFAULT 1
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

--
-- Volcado de datos para la tabla `producto`
--

INSERT INTO `producto` (`codproducto`, `descripcion`, `proveedor`, `precio`, `existencia`, `date_add`, `usuario_id`, `estatus`) VALUES
(12, 'pc-gamer', 11, '7512.63', 785, '2019-10-19 21:24:50', 22, 1),
(13, 'PC-NOGAMER', 5, '24963.40', 1363, '2019-10-19 21:40:10', 22, 1),
(14, 'Ensure 50 ml', 10, '23730.19', 306, '2019-10-19 21:46:05', 22, 1),
(15, 'borrador 6', 1, '499.31', 42, '2019-10-20 00:04:26', 22, 1),
(16, 'borrador acer', 11, '170.00', 399, '2019-10-20 00:04:59', 22, 1),
(17, 'borrador colon', 11, '514.28', 175, '2019-10-20 00:06:16', 22, 1),
(18, 'Lapiz', 8, '847.06', 79, '2019-10-20 18:22:06', 22, 1),
(19, 'computador AMD 64 bits ', 5, '285714.29', 349, '2019-10-24 11:14:43', 22, 1),
(20, 'Computador Asus', 11, '392156.86', 253, '2019-11-05 07:02:25', 22, 1),
(21, 'Computador Asus x64bits', 11, '1266666.67', 15, '2019-11-06 08:13:57', 22, 0),
(22, 'pc 64its', 11, '1320000.00', 25, '2019-11-06 08:16:24', 22, 0),
(23, 'PaÃ±ales x 24 ', 10, '5335.00', 188, '2019-11-06 09:44:40', 22, 1),
(24, 'Paq. PaÃ±ales X24 adult.', 12, '29583.33', 477, '2019-11-06 13:05:54', 22, 1),
(25, 'PaÃ±ales unidad', 12, '2000.00', 50, '2019-11-09 22:48:31', 20, 1),
(26, 'Gaseosa manz 100ml', 13, '1000.00', 2, '2019-11-11 19:15:35', 20, 1),
(27, 'Paq. papa 200 mg', 14, '1800.00', 2, '2019-11-11 19:16:20', 20, 1),
(28, 'Bombo', 15, '300.00', 1, '2019-11-11 19:20:12', 20, 1),
(29, 'Paq. cerveza poker X24', 16, '23500.00', 6, '2019-11-11 19:36:53', 20, 1),
(30, 'Ron viejo caldas', 17, '70000.00', 10, '2019-11-11 19:37:17', 20, 1);

--
-- Disparadores `producto`
--
DELIMITER $$
CREATE TRIGGER `entradas_A_I` AFTER INSERT ON `producto` FOR EACH ROW BEGIN
		INSERT INTO entradas(codproducto,cantidad,precio,usuario_id)
		VALUES (new.codproducto,new.existencia,new.precio,new.usuario_id); 
    END
$$
DELIMITER ;

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `proveedor`
--

CREATE TABLE `proveedor` (
  `codproveedor` int(11) NOT NULL,
  `proveedor` varchar(100) DEFAULT NULL,
  `contacto` varchar(100) DEFAULT NULL,
  `telefono` bigint(11) DEFAULT NULL,
  `direccion` text DEFAULT NULL,
  `date_add` datetime NOT NULL DEFAULT current_timestamp(),
  `usuario_id` int(11) NOT NULL,
  `estatus` int(11) NOT NULL DEFAULT 1
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

--
-- Volcado de datos para la tabla `proveedor`
--

INSERT INTO `proveedor` (`codproveedor`, `proveedor`, `contacto`, `telefono`, `direccion`, `date_add`, `usuario_id`, `estatus`) VALUES
(1, 'Claro', 'juan carlos parra', 3124567890, 'El centro', '2019-10-17 10:16:08', 22, 1),
(2, 'la estacia', 'fernanda pancha', 3128908765, 'boleros', '2019-10-17 10:37:46', 22, 1),
(3, 'movistar', 'juanpa torso', 3129809887, 'valle', '2019-10-17 10:42:30', 22, 1),
(4, 'comcel esa', 'pedro fresco', 3126567409, 'piedra alta', '2019-10-17 10:45:54', 22, 1),
(5, 'HP', 'pedro duarte', 3148760987, 'valle cauca', '2019-10-17 10:46:37', 22, 1),
(6, 'pescas fules', 'franco contes', 3174908900, 'dinamarca', '2019-10-17 10:48:32', 22, 0),
(7, 'fundacion confites', 'paola fances', 3127689009, 'valle', '2019-10-17 10:49:16', 22, 0),
(8, 'omega', 'frank duarle', 3260987654, 'valle', '2019-10-17 10:50:25', 22, 1),
(9, 'perez fran', 'pedro aspol', 3217865098, 'cauca', '2019-10-17 10:51:00', 22, 1),
(10, 'mennar', 'fernada sierra', 3125468790, 'la estancia', '2019-10-17 11:25:01', 22, 1),
(11, 'ASUS', 'JUAN CADAVID', 3124567809, 'PUERTA ELACE', '2019-10-19 20:42:38', 22, 1),
(12, 'Huggies', 'hernan franco', 3212345677, 'torres de molino', '2019-11-06 13:07:22', 22, 1),
(13, 'Big cola', 'fredy rendon', 3124568790, 'cali - valle', '2019-11-11 19:14:10', 20, 1),
(14, 'Margarita', 'carolina fernan', 3125467892, 'cali - valle', '2019-11-11 19:14:57', 20, 1),
(15, 'BOM BUM', 'carmelo frances', 3154678906, 'cali - valle', '2019-11-11 19:19:47', 20, 1),
(16, 'Poker', 'fabio colmenares', 3126475899, 'cali - valle', '2019-11-11 19:34:14', 20, 1),
(17, 'viejo caldas', 'carlos perez', 3245678901, 'cali - valle', '2019-11-11 19:35:34', 20, 1);

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `rol`
--

CREATE TABLE `rol` (
  `idrol` int(11) NOT NULL,
  `rol` varchar(20) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

--
-- Volcado de datos para la tabla `rol`
--

INSERT INTO `rol` (`idrol`, `rol`) VALUES
(1, 'Administrador'),
(2, 'Supervisor'),
(3, 'Vendedor');

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `usuario`
--

CREATE TABLE `usuario` (
  `idusuario` int(11) NOT NULL,
  `nombre` varchar(70) DEFAULT NULL,
  `correo` varchar(100) DEFAULT NULL,
  `usuario` varchar(25) DEFAULT NULL,
  `clave` varchar(100) DEFAULT NULL,
  `rol` int(11) DEFAULT NULL,
  `estatus` int(11) NOT NULL DEFAULT 1
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

--
-- Volcado de datos para la tabla `usuario`
--

INSERT INTO `usuario` (`idusuario`, `nombre`, `correo`, `usuario`, `clave`, `rol`, `estatus`) VALUES
(1, 'admin', 'admin@ventas.com', 'cristian.bambague', 'eb1da10d0a272fdf7ffb7507585959b1', 1, 1),
(2, 'dolex', 'dolex@dolex.com', 'parlon', 'e120ea280aa50693d5568d0071456460', 2, 0),
(3, 'luisa pabon', 'pabon@ventas.com', 'luisa', '81dc9bdb52d04dc20036dbd8313ed055	', 3, 0),
(4, 'eslogan', 'cola@cola.com', 'hola', '202cb962ac59075b964b07152d234b70	', 2, 0),
(5, 'amigos', 'amigos@a.com', 'amigos', '81dc9bdb52d04dc20036dbd8313ed055', 3, 0),
(6, 'colones pizza', 'pizza@amor.com', 'pizza', '827ccb0eea8a706c4c34a16891f84e7b', 2, 0),
(7, 'colon', 'colon@colon.com', 'solar', '202cb962ac59075b964b07152d234b70', 3, 0),
(8, 'mexico', 'galaxia@gal.com', 'espiral', '8f1bac3967e0ff70ebc09d8ca5e08633', 2, 0),
(9, 'samsung', 'samsung@sma.com', 'samsung', '81dc9bdb52d04dc20036dbd8313ed055', 2, 0),
(10, 'hawei', 'hawei@a.com', 'hawei', '202cb962ac59075b964b07152d234b70', 2, 0),
(11, 'los omega', 'omega@alfa.com', 'omega', '202cb962ac59075b964b07152d234b70', 2, 0),
(12, 'camello', 'camello@came', 'camello', '1552c03e78d38d5005d4ce7b8018addf', 3, 0),
(13, 'goku', 'goku@dbz.com', 'kakaroto', '202cb962ac59075b964b07152d234b70', 1, 0),
(14, 'picoro', 'picoro@dbz.com', 'picoro', '202cb962ac59075b964b07152d234b70', 2, 0),
(15, 'freezer', 'freezer@dbz.com', 'freezer', '202cb962ac59075b964b07152d234b70', 3, 0),
(16, 'johan', 'johan@dbz.com', 'johan', '202cb962ac59075b964b07152d234b70', 3, 0),
(17, 'andres', 'andres@muy.com', 'andres', '202cb962ac59075b964b07152d234b70', 2, 0),
(18, 'andres1', 'andres1@muy.com', 'andres125', '202cb962ac59075b964b07152d234b70', 3, 0),
(19, 'juanpis', 'juanp@gmail.com', 'juan', '81dc9bdb52d04dc20036dbd8313ed055', 2, 0),
(20, 'supervisor', 'supervisor@ventas.com', 'supervisor', '827ccb0eea8a706c4c34a16891f84e7b', 2, 1),
(21, 'vendedor', 'vendedor@ventas.com', 'vendedor', '81dc9bdb52d04dc20036dbd8313ed055', 3, 1),
(22, 'andres2', 'andres@gamlll.com2', 'super', '827ccb0eea8a706c4c34a16891f84e7b', 2, 1),
(23, 'juanadres2', 'cristian@uni.com2', 'parede152', '827ccb0eea8a706c4c34a16891f84e7b', 2, 1),
(24, 'fernado escandon', 'fescando@ventas.com', 'scandon', '81dc9bdb52d04dc20036dbd8313ed055', 3, 1),
(25, 'perez', 'a@peres.com', 'perez', '81dc9bdb52d04dc20036dbd8313ed055', 2, 1),
(26, 'Don pepe', 'pepe@hotmail.com', 'vendedor1', 'e10adc3949ba59abbe56e057f20f883e', 3, 1),
(27, 'Caleb Parada', 'Calebparada@gmail.com', 'caleb', 'c7bbf09d0c646508b1d42fff102e457e', 1, 1),
(28, 'josue', 'josueparada@gmail.com', 'josue', 'cfe765fbcde56acbc69ed0b317094463', 1, 1);

--
-- Índices para tablas volcadas
--

--
-- Indices de la tabla `cliente`
--
ALTER TABLE `cliente`
  ADD PRIMARY KEY (`idcliente`),
  ADD KEY `usuario_id` (`usuario_id`);

--
-- Indices de la tabla `configuracion`
--
ALTER TABLE `configuracion`
  ADD PRIMARY KEY (`id`);

--
-- Indices de la tabla `detallefactura`
--
ALTER TABLE `detallefactura`
  ADD PRIMARY KEY (`correlativo`),
  ADD KEY `codproducto` (`codproducto`),
  ADD KEY `nofactura` (`nofactura`);

--
-- Indices de la tabla `detalle_temp`
--
ALTER TABLE `detalle_temp`
  ADD PRIMARY KEY (`correlativo`),
  ADD KEY `nofactura` (`token_user`),
  ADD KEY `codproducto` (`codproducto`);

--
-- Indices de la tabla `entradas`
--
ALTER TABLE `entradas`
  ADD PRIMARY KEY (`correlativo`),
  ADD KEY `codproducto` (`codproducto`);

--
-- Indices de la tabla `factura`
--
ALTER TABLE `factura`
  ADD PRIMARY KEY (`nofactura`),
  ADD KEY `usuario` (`usuario`),
  ADD KEY `codcliente` (`codcliente`);

--
-- Indices de la tabla `producto`
--
ALTER TABLE `producto`
  ADD PRIMARY KEY (`codproducto`),
  ADD KEY `proveedor` (`proveedor`),
  ADD KEY `usuario_id` (`usuario_id`);

--
-- Indices de la tabla `proveedor`
--
ALTER TABLE `proveedor`
  ADD PRIMARY KEY (`codproveedor`),
  ADD KEY `usuario_id` (`usuario_id`);

--
-- Indices de la tabla `rol`
--
ALTER TABLE `rol`
  ADD PRIMARY KEY (`idrol`);

--
-- Indices de la tabla `usuario`
--
ALTER TABLE `usuario`
  ADD PRIMARY KEY (`idusuario`),
  ADD KEY `rol` (`rol`);

--
-- AUTO_INCREMENT de las tablas volcadas
--

--
-- AUTO_INCREMENT de la tabla `cliente`
--
ALTER TABLE `cliente`
  MODIFY `idcliente` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=11;

--
-- AUTO_INCREMENT de la tabla `configuracion`
--
ALTER TABLE `configuracion`
  MODIFY `id` bigint(20) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=2;

--
-- AUTO_INCREMENT de la tabla `detallefactura`
--
ALTER TABLE `detallefactura`
  MODIFY `correlativo` bigint(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=79;

--
-- AUTO_INCREMENT de la tabla `detalle_temp`
--
ALTER TABLE `detalle_temp`
  MODIFY `correlativo` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=115;

--
-- AUTO_INCREMENT de la tabla `entradas`
--
ALTER TABLE `entradas`
  MODIFY `correlativo` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=67;

--
-- AUTO_INCREMENT de la tabla `factura`
--
ALTER TABLE `factura`
  MODIFY `nofactura` bigint(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=40;

--
-- AUTO_INCREMENT de la tabla `producto`
--
ALTER TABLE `producto`
  MODIFY `codproducto` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=31;

--
-- AUTO_INCREMENT de la tabla `proveedor`
--
ALTER TABLE `proveedor`
  MODIFY `codproveedor` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=18;

--
-- AUTO_INCREMENT de la tabla `rol`
--
ALTER TABLE `rol`
  MODIFY `idrol` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=5;

--
-- AUTO_INCREMENT de la tabla `usuario`
--
ALTER TABLE `usuario`
  MODIFY `idusuario` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=29;

--
-- Restricciones para tablas volcadas
--

--
-- Filtros para la tabla `cliente`
--
ALTER TABLE `cliente`
  ADD CONSTRAINT `cliente_ibfk_1` FOREIGN KEY (`usuario_id`) REFERENCES `usuario` (`idusuario`);

--
-- Filtros para la tabla `detallefactura`
--
ALTER TABLE `detallefactura`
  ADD CONSTRAINT `detallefactura_ibfk_1` FOREIGN KEY (`nofactura`) REFERENCES `factura` (`nofactura`) ON DELETE CASCADE ON UPDATE CASCADE,
  ADD CONSTRAINT `detallefactura_ibfk_2` FOREIGN KEY (`codproducto`) REFERENCES `producto` (`codproducto`) ON DELETE CASCADE ON UPDATE CASCADE;

--
-- Filtros para la tabla `detalle_temp`
--
ALTER TABLE `detalle_temp`
  ADD CONSTRAINT `detalle_temp_ibfk_2` FOREIGN KEY (`codproducto`) REFERENCES `producto` (`codproducto`) ON DELETE CASCADE ON UPDATE CASCADE;

--
-- Filtros para la tabla `entradas`
--
ALTER TABLE `entradas`
  ADD CONSTRAINT `entradas_ibfk_1` FOREIGN KEY (`codproducto`) REFERENCES `producto` (`codproducto`) ON DELETE CASCADE ON UPDATE CASCADE;

--
-- Filtros para la tabla `factura`
--
ALTER TABLE `factura`
  ADD CONSTRAINT `factura_ibfk_1` FOREIGN KEY (`usuario`) REFERENCES `usuario` (`idusuario`) ON DELETE CASCADE ON UPDATE CASCADE,
  ADD CONSTRAINT `factura_ibfk_2` FOREIGN KEY (`codcliente`) REFERENCES `cliente` (`idcliente`) ON DELETE CASCADE ON UPDATE CASCADE;

--
-- Filtros para la tabla `producto`
--
ALTER TABLE `producto`
  ADD CONSTRAINT `producto_ibfk_1` FOREIGN KEY (`proveedor`) REFERENCES `proveedor` (`codproveedor`) ON DELETE CASCADE ON UPDATE CASCADE,
  ADD CONSTRAINT `producto_ibfk_2` FOREIGN KEY (`usuario_id`) REFERENCES `usuario` (`idusuario`) ON DELETE CASCADE ON UPDATE CASCADE;

--
-- Filtros para la tabla `proveedor`
--
ALTER TABLE `proveedor`
  ADD CONSTRAINT `proveedor_ibfk_1` FOREIGN KEY (`usuario_id`) REFERENCES `usuario` (`idusuario`) ON DELETE CASCADE ON UPDATE CASCADE;

--
-- Filtros para la tabla `usuario`
--
ALTER TABLE `usuario`
  ADD CONSTRAINT `usuario_ibfk_1` FOREIGN KEY (`rol`) REFERENCES `rol` (`idrol`) ON DELETE CASCADE ON UPDATE CASCADE;
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
