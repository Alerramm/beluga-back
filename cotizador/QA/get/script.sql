
ALTER TABLE `baseDeOperaciones` ADD `latitud` VARCHAR(50) CHARACTER SET latin1 COLLATE latin1_general_ci NOT NULL AFTER `direccion`, ADD `longuitud` INT(50) NOT NULL AFTER `latitud`;
ALTER TABLE `viajes` ADD `checklist` BOOLEAN NOT NULL AFTER `ruta_guardada`, ADD `estatus_app` VARCHAR(50) CHARACTER SET latin1 COLLATE latin1_general_ci NOT NULL AFTER `checklist`;
ALTER TABLE `tramos`DROP `embarque`, DROP `cajas`;




ALTER TABLE `clientes` ADD `tipoCliente` VARCHAR(10) CHARACTER SET utf8 COLLATE utf8_unicode_ci NOT NULL AFTER `rutaArchivo`;

CREATE TABLE `embarques` (
  `id` int(11) NOT NULL,
  `numero` varchar(50) NOT NULL,
  `cajas` varchar(50) NOT NULL,
  `cajas_entregadas` varchar(50) NOT NULL,
  `cajas_rechazadas` varchar(50) NOT NULL,
  `idTramoDevolucion` int(11) NOT NULL,
  `idTramo` int(11) NOT NULL,
  `estatus` varchar(50) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

ALTER TABLE `embarques` ADD PRIMARY KEY (`id`);
ALTER TABLE `embarques` MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=138;

CREATE TABLE `bitacora` (
  `id` int(11) NOT NULL,
  `idEmbarque` int(11) NOT NULL,
  `tipo` varchar(50) NOT NULL,
  `motivo` varchar(200) NOT NULL,
  `fecha` varchar(100) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8;


ALTER TABLE `bitacora` ADD PRIMARY KEY (`id`);

ALTER TABLE `bitacora`MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=20;


CREATE TABLE unidadesNueva (
	 idTipoUnidad int (11) NOT NULL AUTO_INCREMENT,
	 idTIpoADecuacion int(11),
	 nombreUnidad varchar (32),
	 descripcion varchar (50),
	 PRIMARY KEY (idTipoUnidad)
);

CREATE TABLE adecuacion (
	idTIpoADecuacion int (11) NOT NULL AUTO_INCREMENT,
	nombreAdecuacion varchar (32),
	descripcion varchar (50),
	PRIMARY KEY (idTIpoADecuacion)
);


INSERT INTO adecuacion (nombreAdecuacion,descripcion) VALUES ('CAJA SECA', 'Descripcion para Caja Seca');
INSERT INTO adecuacion (nombreAdecuacion,descripcion) VALUES ('PLATAFORMA TIPO MADRINA', 'Descripcion para plataforma tipo madrina');
INSERT INTO adecuacion (nombreAdecuacion,descripcion) VALUES ('CAJA REFRIGERADA', 'Descripcion para Caja Refrigerada');
INSERT INTO adecuacion (nombreAdecuacion,descripcion) VALUES ('PORTACONTENEDOR ', 'Descripcion para PORTACONTENEDOR ');
INSERT INTO adecuacion (nombreAdecuacion,descripcion) VALUES ('PLATAFORMA ', 'Descripcion para PLATAFORMA');
INSERT INTO adecuacion (nombreAdecuacion,descripcion) VALUES ('GRUAS ', 'Descripcion para GRUAS');

//CAJA SECA
INSERT INTO unidadesNueva (idTIpoADecuacion,nombreUnidad,descripcion) VALUES (1,'1.5', '1.5 descripcion');
INSERT INTO unidadesNueva (idTIpoADecuacion,nombreUnidad,descripcion) VALUES (1,'3.5', '3.5 descripcion');
INSERT INTO unidadesNueva (idTIpoADecuacion,nombreUnidad,descripcion) VALUES (1,'5.5', '5.5 descripcion');
INSERT INTO unidadesNueva (idTIpoADecuacion,nombreUnidad,descripcion) VALUES (1,'10', '10 descripcion');
INSERT INTO unidadesNueva (idTIpoADecuacion,nombreUnidad,descripcion) VALUES (1,'MUDANCERO / TORTON', 'MUDANCERO / TORTON descripcion');
INSERT INTO unidadesNueva (idTIpoADecuacion,nombreUnidad,descripcion) VALUES (1,'TRAILER DE 53"', 'TRAILER DE 53 descripcion');
// PLATA FORMA TIPO MADRINA
INSERT INTO unidadesNueva (idTIpoADecuacion,nombreUnidad,descripcion) VALUES (2,'B4 UNIDADES', 'B4 UNIDADES descripcion');
INSERT INTO unidadesNueva (idTIpoADecuacion,nombreUnidad,descripcion) VALUES (2,'13 UNIDADES', '13 UNIDADES descripcion');
// CAJA REFRIGERADA
INSERT INTO unidadesNueva (idTIpoADecuacion,nombreUnidad,descripcion) VALUES (3,'1.5', '1.5 descripcion');
INSERT INTO unidadesNueva (idTIpoADecuacion,nombreUnidad,descripcion) VALUES (3,'3.5', '3.5 descripcion');
INSERT INTO unidadesNueva (idTIpoADecuacion,nombreUnidad,descripcion) VALUES (3,'5.5', '5.5 descripcion');
INSERT INTO unidadesNueva (idTIpoADecuacion,nombreUnidad,descripcion) VALUES (3,'10', '10 descripcion');
INSERT INTO unidadesNueva (idTIpoADecuacion,nombreUnidad,descripcion) VALUES (3,'MUDANCERO / TORTON', 'MUDANCERO / TORTON descripcion');
INSERT INTO unidadesNueva (idTIpoADecuacion,nombreUnidad,descripcion) VALUES (3,'TRAILER DE 53"', 'TRAILER DE 53" descripcion');
//PORTACONTENEDOR
INSERT INTO unidadesNueva (idTIpoADecuacion,nombreUnidad,descripcion) VALUES (4,'40" 20" FULL', '40" 20" FULL descripcion');
INSERT INTO unidadesNueva (idTIpoADecuacion,nombreUnidad,descripcion) VALUES (4,'40" 20" FULL', '40" 20" FULL descripcion');
//PLATAFORMA
INSERT INTO unidadesNueva (idTIpoADecuacion,nombreUnidad,descripcion) VALUES (5,'EQUIPO PESADO', 'EQUIPO PESADO descripcion');
INSERT INTO unidadesNueva (idTIpoADecuacion,nombreUnidad,descripcion) VALUES (5,'EQUIPO LIGERO', 'EQUIPO LIGERO descripcion');
INSERT INTO unidadesNueva (idTIpoADecuacion,nombreUnidad,descripcion) VALUES (5,'MATERIAL DE CONSTRUCCION', 'MATERIAL DE CONSTRUCCION descripcion');
//GRUAS
INSERT INTO unidadesNueva (idTIpoADecuacion,nombreUnidad,descripcion) VALUES (6,'VEHICULOS PESADOS', 'VEHICULOS PESADOS descripcion');
INSERT INTO unidadesNueva (idTIpoADecuacion,nombreUnidad,descripcion) VALUES (6,'VEHICULOS LIGEROS', 'VEHICULOS LIGEROS descripcion');


select idTIpoADecuacion, nombreAdecuacion from dbs304381.adecuacion;
select nombreUnidad from dbs304381.unidadesNueva where idTIpoADecuacion = 1;

CREATE TABLE  categoriaproductos (
	idcategoriaproducto int (11) NOT NULL AUTO_INCREMENT,
	nombrecategoria varchar (30),
	desccateg varchar (30),
	PRIMARY KEY (idcategoriaproducto)
);

INSERT INTO categoriaproductos (nombrecategoria, desccateg) VALUES ('Textil','Descripcion para textil');
INSERT INTO categoriaproductos (nombrecategoria, desccateg) VALUES ('Automotriz','Descripcion para Automotriz');
INSERT INTO categoriaproductos (nombrecategoria, desccateg) VALUES ('Cosmético','Descripcion para Cosmético');
INSERT INTO categoriaproductos (nombrecategoria, desccateg) VALUES ('Vinos y licores','Descripcion para Vinos y licores');
INSERT INTO categoriaproductos (nombrecategoria, desccateg) VALUES ('Textil','Alimentos');


CREATE TABLE constantes (
	idTipoConstante int (11) NOT NULL AUTO_INCREMENT,
	nombreConstante varchar (32),
	descripcion varchar (50),
	contennidoConstante varchar (30),

	PRIMARY KEY (idTipoConstante)
);

INSERT INTO constantes (nombreConstante,descripcion,contennidoConstante) VALUES ('costoDiesel', 'costo al dia del diesel', '21');

SELECT contennidoConstante FROM dbs304381.constantes WHERE idTipoConstante=1;





CREATE TABLE gruposClasificacion (
	idGrupo int(11) NOT NULL AUTO_INCREMENT,
	nombreGrupo VARCHAR (20) ,
	PRIMARY KEY (idGrupo)
);

CREATE TABLE Kilometros(
	idKilometros int(11) NOT NULL AUTO_INCREMENT,
	idGrupo int(11),
	rendimiento varchar (20),
	numDias varchar (20),
	comision varchar (20),
	viaticos varchar (20),
	utilidadPremium varchar (20),
	gastoPremium varchar (20),

	PRIMARY KEY (idKilometros)
);

INSERT INTO `Kilometros` (`idKilometros`, `idGrupo`, `rendimiento`, `numDias`, `comision`, `viaticos`, `utilidadPremium`, `gastoPremium`) VALUES (NULL, '1', '4.88 ', '1', '400', '0', '76', '24'), (NULL, '1', '4.88', '1', '400', '0', '76', '24'), (NULL, '1', '4.88', '1', '400', '0', '82', '18'), (NULL, '2', '7.5', '1.5', '600', '0', '82', '18'), (NULL, '2', '7.5', '1.5', '600', '0', '78', '22'), (NULL, '2', '7.5', '1.5', '600', '0', '78', '22'), (NULL, '3', '7.5', '1.5', '600', '150', '74', '26'), (NULL, '3', '7.5', '1.5', '600', '150', '76', '24'), (NULL, '3', '7.5', '1.5', '600', '150', '77', '23'), (NULL, '4', '7.5', '2.0', '800', '300', '75', '25'), (NULL, '4', '7.5', '2.0', '800', '300', '75', '25'), (NULL, '4', '7.5', '2.5', '1000', '450', '76', '24'), (NULL, '5', '7.5', '3.0', '1200', '450', '75', '25'), (NULL, '5', '7.5', '3.0', '1200', '450', '75', '25'), (NULL, '5', '7.5', '3.5', '1400', '525', '76', '24'), (NULL, '6', '7.5', '4.0', '1600', '600', '70', '30'), (NULL, '6', '7.5', '4.0', '1600', '600', '70', '30'), (NULL, '6', '7.5', '4.5', '1800', '675', '74', '26'), (NULL, '7', '7.5', '5.5', '2200', '825', '75', '25'), (NULL, '7', '7.5', '7.0', '2800', '1050', '72', '28'), (NULL, '7', '7.5', '8.5', '3400', '1275', '74', '26');












CREATE TABLE serviciosAdicionales(
	idServicioAdicional  int (11) NOT NULL AUTO_INCREMENT,
	descripcion varchar (30),
	idViaje varchar (20),
	PRIMARY KEY (idServicioAdicional));


CREATE TABLE mercanciaAsegurada(
	idMercanciaAsegurada int (11) NOT NULL AUTO_INCREMENT,
	idServicioAdicional  int (11) ,
	monto int (11),
	precio int (11),

	PRIMARY KEY (idMercanciaAsegurada));

CREATE TABLE maniobras(
	idManiobras int (11) NOT NULL AUTO_INCREMENT,
	idServicioAdicional  int (11) ,
	numeroCarga int (11),
	numeroEntrega int (11),
	precio int (11),
	PRIMARY KEY (idManiobras));

CREATE TABLE seguridadAdicional(
	idseguridadAdicional int (11) NOT NULL AUTO_INCREMENT,
	idServicioAdicional  int (11) ,
	tipo int (11),
	precio int (11),

	PRIMARY KEY (idseguridadAdicional))	;

CREATE TABLE custodia(
	idcustodia int (11) NOT NULL AUTO_INCREMENT,
	idServicioAdicional  int (11) ,
	km int (11),
	precio int (11),

	PRIMARY KEY (idcustodia));

CREATE TABLE  productosByViaje (
	idProductoByViaje int (11) NOT NULL AUTO_INCREMENT,
	idViaje varchar (30),
	peso varchar (20),
	largo varchar (20),
	ancho varchar (20),
	alto varchar (20),
	descripcion varchar (30),
	PRIMARY KEY (idProductoByViaje)
);




CREATE TABLE  contactoCliente (
	idContacto int (11) NOT NULL AUTO_INCREMENT,
	idCliente varchar (20) ,
	tipoContacto varchar (10),
	contacto varchar (32),
	telefono varchar (20),
	correo varchar (60),
	PRIMARY KEY (idContacto)
);

CREATE TABLE `costos` (
  `id` int(11) NOT NULL,
  `tipo` varchar(50) NOT NULL,
  `presupuesto` varchar(20) NOT NULL,
  `subtotal` varchar(20) NOT NULL,
  `iva` varchar(20) NOT NULL,
  `total` varchar(20) NOT NULL,
  `idTramo` int(11) NOT NULL,
  `idViaje` int(11) NOT NULL,
  `observacion` varchar(100) NOT NULL,
  `estatus` varchar(50) NOT NULL,
  `autoriza` varchar(50) NOT NULL,
  `comprobado` varchar(50) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8;


ALTER TABLE `costos` ADD PRIMARY KEY (`id`);

ALTER TABLE `costos` MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=2;





CREATE TABLE `dispersiones` (
  `id` int(11) NOT NULL,
  `idGasto` int(11) NOT NULL,
  `referencia` varchar(60) NOT NULL,
  `emisor` varchar(50) NOT NULL,
  `receptor` varchar(50) NOT NULL,
  `estatus` varchar(50) NOT NULL,
  `realiza` varchar(50) NOT NULL,
  `metodoPago` varchar(20) NOT NULL,
  `cobroCliente` varchar(10) NOT NULL,
  `iva` varchar(20) NOT NULL,
  `subtotal` varchar(20) NOT NULL,
  `total` varchar(20) CHARACTER SET utf8mb4 NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8;


ALTER TABLE `dispersiones` ADD PRIMARY KEY (`id`);

ALTER TABLE `dispersiones` MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=39;




CREATE TABLE `empresa_viaje` (
  `id` int(11) NOT NULL,
  `idViaje` int(11) NOT NULL,
  `idEmpresa` int(11) NOT NULL,
  `estatus` varchar(50) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

ALTER TABLE `empresa_viaje` ADD PRIMARY KEY (`id`);
ALTER TABLE `empresa_viaje` MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=130;



CREATE TABLE `metricas_precio` (
  `id` int(11) NOT NULL,
  `grupo` varchar(10) NOT NULL,
  `rendimiento` varchar(10) NOT NULL,
  `num_dias` varchar(10) NOT NULL,
  `comision` varchar(10) NOT NULL,
  `viaticos` varchar(10) NOT NULL,
  `utilidad_premium` varchar(10) NOT NULL,
  `gasto_premium` varchar(10) NOT NULL,
  `km_inicial` varchar(10) NOT NULL,
  `km_final` varchar(10) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

ALTER TABLE `metricas_precio`ADD PRIMARY KEY (`id`);

ALTER TABLE `metricas_precio` MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=221;


CREATE TABLE `precio_viaje` (
  `id` int(11) NOT NULL,
  `idViaje` int(11) NOT NULL,
  `idTipoPrecio` int(11) NOT NULL,
  `fecha` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `horario_laboral` tinyint(1) NOT NULL,
  `precio` varchar(20) NOT NULL,
  `idMetricasPrecio` int(11) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

ALTER TABLE `precio_viaje`
  ADD PRIMARY KEY (`id`);

  ALTER TABLE `precio_viaje`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=230;


  CREATE TABLE `productosByViaje` (
  `idProductoByViaje` int(11) NOT NULL,
  `idViaje` varchar(30) DEFAULT NULL,
  `peso` varchar(20) DEFAULT NULL,
  `largo` varchar(20) DEFAULT NULL,
  `ancho` varchar(20) DEFAULT NULL,
  `alto` varchar(20) DEFAULT NULL,
  `descripcion` varchar(30) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

ALTER TABLE `productosByViaje`
  ADD PRIMARY KEY (`idProductoByViaje`);

  ALTER TABLE `productosByViaje`
  MODIFY `idProductoByViaje` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=217;




  CREATE TABLE `tipo_precio` (
  `id` int(11) NOT NULL,
  `nombre` varchar(50) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

--
-- Volcado de datos para la tabla `tipo_precio`
--

INSERT INTO `tipo_precio` (`id`, `nombre`) VALUES
(1, 'Premium prepago'),
(2, 'Premium pospago'),
(3, 'Basico pospago'),
(4, 'Contrato');


ALTER TABLE `tipo_precio`
  ADD PRIMARY KEY (`id`);


ALTER TABLE `tipo_precio`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=5;