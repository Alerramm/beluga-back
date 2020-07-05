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

	PRIMARY KEY (idTIpoADecuacion)
)
CREATE TABLE dbs304381.GruposRendimiento (
	Grupo int (11) ,
	rendimiento varchar (20)
)


INSERT INTO dbs304381.categoriaproductos (nombreConstante,descripcion,contennidoConstante) VALUES ('costoxdia','El costo por dia ', '400');
INSERT INTO dbs304381.categoriaproductos (nombreConstante,descripcion,contennidoConstante) VALUES ('costoxdia','El costo por dia ', '400');



INSERT INTO dbs304381.GruposRendimiento ('A','4.875') VALUES ();
INSERT INTO dbs304381.GruposRendimiento ('B','7.5') VALUES ();
INSERT INTO dbs304381.GruposRendimiento ('C','7.5') VALUES ();
INSERT INTO dbs304381.GruposRendimiento ('D','') VALUES ();
INSERT INTO dbs304381.GruposRendimiento ('E','') VALUES ();
INSERT INTO dbs304381.GruposRendimiento ('F','') VALUES ();
INSERT INTO dbs304381.GruposRendimiento ('G','')  VALUES ();






