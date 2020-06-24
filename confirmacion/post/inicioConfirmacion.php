<?PHP

include '../production.php';

header('Access-Control-Allow-Origin: *');
header("Content-Type: application/json; charset=UTF-8");

$mysqli = mysqli_init();
$enlace = mysqli_connect($_SESSION['HOST'], $_SESSION['USER'], $_SESSION['PASS'], $_SESSION['DBNAME']);

$result3 = mysqli_query($enlace, "SELECT *  from viajes where  estatus='Pendiente' order by id asc");
while ($row3 = mysqli_fetch_assoc($result3)) {
	$idV = $row3['id'];
	$preDie = $row3['disel'];
	$baseO = $row3['base'];
	$cliente = $row3['cliente'];
	$unidad = $row3['unidad'];
	$operador = $row3['operador'];
	$fecha_carga = $row3['fecha_carga'];
	$hracar = date($row3['fecha_carga'], "H:i");
	$desti = $row3['entrega'];
	$fecha_disponibilidad = $row3['fecha_disponibilidad'];
	$fechaEntrega = $row3['fecha_entrega'];
	$total_distancia = $row3['distancia'];
	$total_casetas = $row3['casetas'];
	$total_gasolina = 0;
	$total_gastos = 0;
	$total_tiempo = $row3['tiempo'];
	$tonelaje = $row3['unidad_tipo'];
	$vuelta = $row3['redondo'];
	$ciudad = $row3['destino'];


	$cons1_mysql = "SELECT observaciones,tramo FROM  tramos where idviaje ='$idV' order by tramo asc";
	//echo $cons_mysql;
	$resCon1 = mysqli_query($enlace, $cons1_mysql);
	while ($edo1 = mysqli_fetch_assoc($resCon1)) {
		$entre = $edo1['observaciones'];
		$tramo = $edo1['tramo'] - 1;
	}

	$cons2_mysql = "SELECT entrega  FROM  tramos where idviaje ='$idV' and tramo ='$tramo'";
	//echo $cons2_mysql;
	$resCon2 = mysqli_query($enlace, $cons2_mysql);
	while ($edo2 = mysqli_fetch_assoc($resCon2)) {
		$desti = $edo2['entrega'];
	}
	//echo $desti;

	$cons_mysql = "SELECT tipo, descripcion, viaticos, numDias FROM  tipoViajeAct where '$total_distancia' between kmsIni and kmsFin";
	//echo $cons_mysql;
	$resCon = mysqli_query($enlace, $cons_mysql);
	while ($edo = mysqli_fetch_assoc($resCon)) {
		$tipo = $edo['tipo'];
		$descrip = $edo['descripcion'];
		$alimentos = $edo['viaticos'];
		$numDia = $edo['numDias'];
	}

	$cons_mysql = "SELECT tipo, descripcion, viaticos,porcGto numDias FROM  tipoViajeAct where '$total_distancia' between kmsIni and kmsFin";
	//echo $cons_mysql;
	$resCon = mysqli_query($enlace, $cons_mysql);
	while ($edo = mysqli_fetch_assoc($resCon)) {
		$tipo = $edo['tipo'];
		$descrip = $edo['descripcion'];
		$alimentos = $edo['viaticos'];
		$numDia = $edo['numDias'];
		$porcGto = $edo['porcGto'];
	}


	$pos = strpos($unidad, 'Ejes');

	$unidad = substr($unidad, 0, $pos - 1);

	//***RENDIMIENTOS COMISIONM***
	$unid_mysql = "SELECT modelo,modelo2,comisionV,rendimientoLocal,rendimientoForaneo FROM unidades 
WHERE   camion ='$unidad'";
	//echo $unid_mysql;
	$resUni = mysqli_query($enlace, $unid_mysql);
	while ($uni = mysqli_fetch_assoc($resUni)) {
		$modelo = $uni['modelo'];
		$modelo2 = $uni['modelo2'];
		$comision = $uni['comisionV'];
		$rendiL = $uni['rendimientoLocal'];
		$rendiF = $uni['rendimientoForaneo'];
	}


	$cons_mysql = "SELECT * FROM  tipoViajeAct where '$total_distancia' between kmsIni and kmsFin";
	//echo $cons_mysql;
	$resCon = mysqli_query($enlace, $cons_mysql);
	while ($edo = mysqli_fetch_assoc($resCon)) {
		$tipo = $edo['tipo'];
		$descrip = $edo['descripcion'];
		$alimentos = $edo['viaticos'];
		$numDia = $edo['numDias'];
		$porcGto = $edo['porcGto'];
		if ($modelo2 == 'CASCADIA' or $modelo == 'CASCADIA') {
			$comision = $edo['CASCADIA_comision'];
			$rendiL = $edo['CASCADIA_RendLoc'];
			$rendiF = $edo['CASCADIA_RendFor'];
		} else if ($modelo2 == 'SUNRAY' or $modelo == 'SUNRAY') {
			$comision = $edo['SUNRAY_comision'];
			$rendiL = $edo['SUNRAY_RendLoc'];
			$rendiF = $edo['SUNRAY_RendFor'];
		} else if ($modelo2 == 'UTILITARIA' or $modelo == 'GF8') {
			$comision = $edo['GF8_comision'];
			$rendiL = $edo['GF8_RendLoc'];
			$rendiF = $edo['GF8_RendFor'];
		} else if ($modelo2 == 'GF10000' or $modelo == 'GF10000') {
			$comision = $edo['GF10000_comision'];
			$rendiL = $edo['GF10000_RendLoc'];
			$rendiF = $edo['GF10000_RendFor'];
		} else if ($modelo2 == 'GF6000' or $modelo == 'GF6000') {
			$comision = $edo['GF6000_comision'];
			$rendiL = $edo['GF6000_RendLoc'];
			$rendiF = $edo['GF6000_RendFor'];
		} else if ($modelo2 == 'GF5000' or $modelo == 'GF5000') {
			$comision = $edo['GF5000_comision'];
			$rendiL = $edo['GF5000_RendLoc'];
			$rendiF = $edo['GF5000_RendFor'];
		} else if ($modelo2 == 'GF3000' or $modelo == 'GF3000') {
			$comision = $edo['GF3000_comision'];
			$rendiL = $edo['GF3000_RendLoc'];
			$rendiF = $edo['GF3000_RendFor'];
		} else if ($modelo2 == 'GF1500' or $modelo == 'GF1500') {
			$comision = $edo['GF1500_comision'];
			$rendiL = $edo['GF1500_RendLoc'];
			$rendiF = $edo['GF1500_RendFor'];
		} else if ($modelo2 == 'GF250' or $modelo == 'GF250') {
			$comision = $edo['GF250_comision'];
			$rendiL = $edo['GF250_RendLoc'];
			$rendiF = $edo['GF250_RendFor'];
		}
	}
	/* echo "Modelo-->".$modelo2;
 echo "COMISION-->".$comision;
 echo "REND LOCAL-->".$rendiL;
 echo "REND FORANE-->".$rendiF;
 echo "numdia-->".$numDia;*/

	$preDie =  str_replace(",", ".", $preDie);

	if ($total_distancia > 150) {
		//echo "kilom-->".$total_distancia;
		//echo "rend-->".$rendiF;
		$lto = $total_distancia / $rendiF;
		//echo "Ltos-->".$lto;	
		$total_gasolina = $lto * $preDie;
	} else {

		$total_gasolina = ($total_distancia / $rendiL) * $preDie;
	}

	if ($vuelta == true or $vuelta == 1) {
		$tipo = 'Ida-Vuelta';
	} else {
		$tipo = 'Ida';
	}

	//		 if ($tonelaje=='GF6000'){
	//	      $comision =$comision3;
	//        }		 


	//*************DIESEL REDONDEO ****
	$dieabs = intval($total_gasolina);
	$rem = $dieabs % 100;

	if ($rem < 30) {
		// echo "diesel Redon-->".$diesel;
		$total_gasolina = $dieabs - $rem;
	} elseif ($rem > 29 && $rem < 50) {
		$total_gasolina = $dieabs - $rem + 50;
	} elseif ($rem > 49 && $rem < 80) {
		$total_gasolina = $dieabs - $rem + 50;
	} else {
		$total_gasolina = $dieabs - $rem + 100;
	}

	//************CASETAS REDONDEO ****
	//   echo "TOTAL Casetas-->".$total_casetas;
	$casabs = intval($total_casetas);
	$remC = $casabs % 100;
	//echo "remc-->".$remC;
	if ($remC > 1 && $remC < 50) {
		$total_casetas = $casabs - $remC + 100;
	} elseif ($remC > 49 && $remC < 80) {
		$total_casetas = $casabs - $remC + 100;
	} else {
		$total_casetas = $casabs - $remC + 100;
	}


	$sqlDeleteTempOperacion = "DELETE FROM `tempOperacion` WHERE operador = '$operador'";
	mysqli_query($enlace, $sqlDeleteTempOperacion);

	//$totComision=$comision*$numDia;
	$totComision = $comision;
	$queryInsertar = "insert into tempOperacion (fecha,HraCar,cliente,unidad,cargar,destino,operador,
fechaHoraEntrega,entrega,status,diesel,casetas,alimentos,comision,kms,numDias,tiempo,
fechaDisponib,confirmacion,idViaje,confirmaViaje,ciudad) value 				 
(
   '$fecha_carga','$hracar','$cliente','$unidad','basecliente','$desti','$operador','$fechaEntrega',
   '$entre','Programado','$total_gasolina','$total_casetas','$alimentos','$totComision','$total_distancia','$numDia','$total_tiempo',
   '$fecha_disponibilidad','$tipo','$idV','Pendiente','$ciudad')";
	//echo $queryInsertar;

	mysqli_query($enlace, $queryInsertar);

	$sqlU = "UPDATE viajes  SET  estatus='Confirmado' WHERE id='$idV'";
	mysqli_query($enlace, $sqlU);
}
