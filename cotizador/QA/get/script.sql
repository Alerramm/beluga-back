CREATE TABLE dbs304381.unidadesNueva (
	 idTipoUnidad int (11) NOT NULL AUTO_INCREMENT,
	 idTIpoADecuacion int(11),
	 nombreUnidad varchar (32),
	 descripcion varchar (50),
	 PRIMARY KEY (idTipoUnidad)
)

CREATE TABLE dbs304381.adecuacion (
	idTIpoADecuacion int (11) NOT NULL AUTO_INCREMENT,
	nombreAdecuacion varchar (32),
	descripcion varchar (50),
	PRIMARY KEY (idTIpoADecuacion)
)


INSERT INTO dbs304381.adecuacion (nombreAdecuacion,descripcion) VALUES ('CAJA SECA', 'Descripcion para Caja Seca');
INSERT INTO dbs304381.adecuacion (nombreAdecuacion,descripcion) VALUES ('PLATAFORMA TIPO MADRINA', 'Descripcion para plataforma tipo madrina');
INSERT INTO dbs304381.adecuacion (nombreAdecuacion,descripcion) VALUES ('CAJA REFRIGERADA', 'Descripcion para Caja Refrigerada');
INSERT INTO dbs304381.adecuacion (nombreAdecuacion,descripcion) VALUES ('PORTACONTENEDOR ', 'Descripcion para PORTACONTENEDOR ');
INSERT INTO dbs304381.adecuacion (nombreAdecuacion,descripcion) VALUES ('PLATAFORMA ', 'Descripcion para PLATAFORMA');
INSERT INTO dbs304381.adecuacion (nombreAdecuacion,descripcion) VALUES ('GRUAS ', 'Descripcion para GRUAS');

//CAJA SECA
INSERT INTO dbs304381.unidadesNueva (idTIpoADecuacion,nombreUnidad,descripcion) VALUES (1,'1.5', '1.5 descripcion');
INSERT INTO dbs304381.unidadesNueva (idTIpoADecuacion,nombreUnidad,descripcion) VALUES (1,'3.5', '3.5 descripcion');
INSERT INTO dbs304381.unidadesNueva (idTIpoADecuacion,nombreUnidad,descripcion) VALUES (1,'5.5', '5.5 descripcion');
INSERT INTO dbs304381.unidadesNueva (idTIpoADecuacion,nombreUnidad,descripcion) VALUES (1,'10', '10 descripcion');
INSERT INTO dbs304381.unidadesNueva (idTIpoADecuacion,nombreUnidad,descripcion) VALUES (1,'MUDANCERO / TORTON', 'MUDANCERO / TORTON descripcion');
INSERT INTO dbs304381.unidadesNueva (idTIpoADecuacion,nombreUnidad,descripcion) VALUES (1,'TRAILER DE 53"', 'TRAILER DE 53 descripcion');
// PLATA FORMA TIPO MADRINA
INSERT INTO dbs304381.unidadesNueva (idTIpoADecuacion,nombreUnidad,descripcion) VALUES (2,'B4 UNIDADES', 'B4 UNIDADES descripcion');
INSERT INTO dbs304381.unidadesNueva (idTIpoADecuacion,nombreUnidad,descripcion) VALUES (2,'13 UNIDADES', '13 UNIDADES descripcion');
// CAJA REFRIGERADA
INSERT INTO dbs304381.unidadesNueva (idTIpoADecuacion,nombreUnidad,descripcion) VALUES (3,'1.5', '1.5 descripcion');
INSERT INTO dbs304381.unidadesNueva (idTIpoADecuacion,nombreUnidad,descripcion) VALUES (3,'3.5', '3.5 descripcion');
INSERT INTO dbs304381.unidadesNueva (idTIpoADecuacion,nombreUnidad,descripcion) VALUES (3,'5.5', '5.5 descripcion');
INSERT INTO dbs304381.unidadesNueva (idTIpoADecuacion,nombreUnidad,descripcion) VALUES (3,'10', '10 descripcion');
INSERT INTO dbs304381.unidadesNueva (idTIpoADecuacion,nombreUnidad,descripcion) VALUES (3,'MUDANCERO / TORTON', 'MUDANCERO / TORTON descripcion');
INSERT INTO dbs304381.unidadesNueva (idTIpoADecuacion,nombreUnidad,descripcion) VALUES (3,'TRAILER DE 53"', 'TRAILER DE 53" descripcion');
//PORTACONTENEDOR
INSERT INTO dbs304381.unidadesNueva (idTIpoADecuacion,nombreUnidad,descripcion) VALUES (4,'40" 20" FULL', '40" 20" FULL descripcion');
INSERT INTO dbs304381.unidadesNueva (idTIpoADecuacion,nombreUnidad,descripcion) VALUES (4,'40" 20" FULL', '40" 20" FULL descripcion');
//PLATAFORMA
INSERT INTO dbs304381.unidadesNueva (idTIpoADecuacion,nombreUnidad,descripcion) VALUES (5,'EQUIPO PESADO', 'EQUIPO PESADO descripcion');
INSERT INTO dbs304381.unidadesNueva (idTIpoADecuacion,nombreUnidad,descripcion) VALUES (5,'EQUIPO LIGERO', 'EQUIPO LIGERO descripcion');
INSERT INTO dbs304381.unidadesNueva (idTIpoADecuacion,nombreUnidad,descripcion) VALUES (5,'MATERIAL DE CONSTRUCCION', 'MATERIAL DE CONSTRUCCION descripcion');
//GRUAS
INSERT INTO dbs304381.unidadesNueva (idTIpoADecuacion,nombreUnidad,descripcion) VALUES (6,'VEHICULOS PESADOS', 'VEHICULOS PESADOS descripcion');
INSERT INTO dbs304381.unidadesNueva (idTIpoADecuacion,nombreUnidad,descripcion) VALUES (6,'VEHICULOS LIGEROS', 'VEHICULOS LIGEROS descripcion');


select idTIpoADecuacion, nombreAdecuacion from dbs304381.adecuacion;
select nombreUnidad from dbs304381.unidadesNueva where idTIpoADecuacion = 1;

CREATE TABLE  dbs304381.categoriaproductos (
	idcategoriaproducto int (11) NOT NULL AUTO_INCREMENT,
	nombrecategoria varchar (30),
	desccateg varchar (30),
	PRIMARY KEY (idcategoriaproducto)
)

INSERT INTO dbs304381.categoriaproductos (nombrecategoria, desccateg) VALUES ('Textil','Descripcion para textil');
INSERT INTO dbs304381.categoriaproductos (nombrecategoria, desccateg) VALUES ('Automotriz','Descripcion para Automotriz');
INSERT INTO dbs304381.categoriaproductos (nombrecategoria, desccateg) VALUES ('Cosmético','Descripcion para Cosmético');
INSERT INTO dbs304381.categoriaproductos (nombrecategoria, desccateg) VALUES ('Vinos y licores','Descripcion para Vinos y licores');
INSERT INTO dbs304381.categoriaproductos (nombrecategoria, desccateg) VALUES ('Textil','Alimentos');


CREATE TABLE dbs304381.constantes (
	idTipoConstante int (11) NOT NULL AUTO_INCREMENT,
	nombreConstante varchar (32),
	descripcion varchar (50),
	contennidoConstante varchar (30),

	PRIMARY KEY (idTipoConstante)
);

INSERT INTO dbs304381.constantes (nombreConstante,descripcion,contennidoConstante) VALUES ('costoDiesel', 'costo al dia del diesel', '21');

SELECT contennidoConstante FROM dbs304381.constantes WHERE idTipoConstante=1;





CREATE TABLE dbs304381.gruposClasificacion (
	idGrupo int(11) NOT NULL AUTO_INCREMENT,
	nombreGrupo VARCHAR (20) ,
	PRIMARY KEY (idGrupo)
)

CREATE TABLE dbs304381.Kilometros(
	idKilometros int(11) NOT NULL AUTO_INCREMENT,
	idGrupo int(11),
	rendimiento varchar (20),
	numDias varchar (20),
	comision varchar (20),
	viaticos varchar (20),
	utilidadPremium varchar (20),
	gastoPremium varchar (20),

	PRIMARY KEY (idKilometros)
)

INSERT INTO `Kilometros` (`idKilometros`, `idGrupo`, `rendimiento`, `numDias`, `comision`, `viaticos`, `utilidadPremium`, `gastoPremium`) VALUES (NULL, '1', '4.88 ', '1', '400', '0', '76', '24'), (NULL, '1', '4.88', '1', '400', '0', '76', '24'), (NULL, '1', '4.88', '1', '400', '0', '82', '18'), (NULL, '2', '7.5', '1.5', '600', '0', '82', '18'), (NULL, '2', '7.5', '1.5', '600', '0', '78', '22'), (NULL, '2', '7.5', '1.5', '600', '0', '78', '22'), (NULL, '3', '7.5', '1.5', '600', '150', '74', '26'), (NULL, '3', '7.5', '1.5', '600', '150', '76', '24'), (NULL, '3', '7.5', '1.5', '600', '150', '77', '23'), (NULL, '4', '7.5', '2.0', '800', '300', '75', '25'), (NULL, '4', '7.5', '2.0', '800', '300', '75', '25'), (NULL, '4', '7.5', '2.5', '1000', '450', '76', '24'), (NULL, '5', '7.5', '3.0', '1200', '450', '75', '25'), (NULL, '5', '7.5', '3.0', '1200', '450', '75', '25'), (NULL, '5', '7.5', '3.5', '1400', '525', '76', '24'), (NULL, '6', '7.5', '4.0', '1600', '600', '70', '30'), (NULL, '6', '7.5', '4.0', '1600', '600', '70', '30'), (NULL, '6', '7.5', '4.5', '1800', '675', '74', '26'), (NULL, '7', '7.5', '5.5', '2200', '825', '75', '25'), (NULL, '7', '7.5', '7.0', '2800', '1050', '72', '28'), (NULL, '7', '7.5', '8.5', '3400', '1275', '74', '26');












CREATE TABLE dbs304381.serviciosAdicionales(
	idServicioAdicional  int (11) NOT NULL AUTO_INCREMENT,
	descripcion varchar (30),
	idViaje varchar (20),
	PRIMARY KEY (idServicioAdicional))


CREATE TABLE dbs304381.mercanciaAsegurada(
	idMercanciaAsegurada int (11) NOT NULL AUTO_INCREMENT,
	idServicioAdicional  int (11) ,
	monto int (11),
	precio int (11),

	PRIMARY KEY (idMercanciaAsegurada))

CREATE TABLE dbs304381.maniobras(
	idManiobras int (11) NOT NULL AUTO_INCREMENT,
	idServicioAdicional  int (11) ,
	numeroCarga int (11),
	numeroEntrega int (11),
	precio int (11),
	PRIMARY KEY (idManiobras))

CREATE TABLE dbs304381.seguridadAdicional(
	idseguridadAdicional int (11) NOT NULL AUTO_INCREMENT,
	idServicioAdicional  int (11) ,
	tipo int (11),
	precio int (11),

	PRIMARY KEY (idseguridadAdicional))	

CREATE TABLE dbs304381.custodia(
	idcustodia int (11) NOT NULL AUTO_INCREMENT,
	idServicioAdicional  int (11) ,
	km int (11),
	precio int (11),

	PRIMARY KEY (idcustodia))

CREATE TABLE  dbs304381.productosByViaje (
	idProductoByViaje int (11) NOT NULL AUTO_INCREMENT,
	idViaje varchar (30),
	peso varchar (20),
	largo varchar (20),
	ancho varchar (20),
	alto varchar (20),
	descripcion varchar (30),
	PRIMARY KEY (idProductoByViaje)
)





