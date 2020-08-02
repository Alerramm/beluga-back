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
	$total_distancia = $row3['distancia'] / 1000;
	$total_casetas = $row3['casetas'];
	$total_gasolina = 0;
	$total_gastos = 0;
	$total_tiempo = $row3['tiempo'];
	$tonelaje = $row3['unidad_tipo'];
	$unidMod = $row3['unidad_modelo'];
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
	$cons2_mysql = "SELECT idMetricasPrecio  FROM  precio_viaje where idViaje ='$idV'";
	//echo $cons2_mysql;
	$resCon2 = mysqli_query($enlace, $cons2_mysql);
	while ($edo2 = mysqli_fetch_assoc($resCon2)) {
		$idMet = $edo2['idMetricasPrecio'];
	}
	//echo $desti;

	$cons_mysql = "SELECT idTipo,tipo, descripcion, viaticos,porcGto, numDias,kmsIni,kmsFin FROM  tipoViajeAct where '$total_distancia' between kmsIni and kmsFin";
	//echo $cons_mysql;
	$resCon = mysqli_query($enlace, $cons_mysql);
	while ($edo = mysqli_fetch_assoc($resCon)) {
		$idTipo = $edo['idTipo'];
		$tipo = $edo['tipo'];
		$descrip = $edo['descripcion'];
		$alimentos = $edo['viaticos'];
		$numDia = $edo['numDias'];
		$porcGto = $edo['porcGto'];
		$kmsIni = $edo['kmsIni'];
		$kmsFin = $edo['kmsFin'];
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


	$cons_mysql = "SELECT * FROM  tipoViajeComple where idTipo='$idTipo' and adecuacion ='$tonelaje' and modelo='$unidMod'";
	//echo $cons_mysql;
	$resCon = mysqli_query($enlace, $cons_mysql);
	while ($edo = mysqli_fetch_assoc($resCon)) {
		$comision = $edo['comision'];
		$rendiL = $edo['RendLoc'];
		$rendiF = $edo['RendFor'];
	}

	$preDie =  str_replace(",", ".", $preDie);

	if ($total_distancia > 150) {
		//echo "kilom-->".$total_distancia;
		//echo "rend-->".$rendiF;
		$lto = $total_distancia / $rendiF;
		//echo "Ltos-->".$lto;	
		$total_gasolina = $lto * $preDie;
		$rendimiento = $rendiF;
	} else {

		$total_gasolina = ($total_distancia / $rendiL) * $preDie;
		$rendimiento = $rendiL;
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

	$sqlU = "UPDATE metricas_precio SET grupo='$descrip',rendimiento='$rendimiento',num_dias='$numDia',comision='$comision',viaticos='$alimentos',
	utilidad_premium='',gasto_premium='',km_inicial='$kmsIni' ,km_final='$kmsFin' WHERE id='$idMet'";
	mysqli_query($enlace, $sqlU);

	$sqlU = "UPDATE viajes  SET  estatus='Confirmado' WHERE id='$idV'";
	mysqli_query($enlace, $sqlU);

	$Datos = array(
		"id" => $idV,
	);
	$url = 'http://www.misistema.mx/beluga/Finanzas/endpoints/confirmacion/post/inicioConfirmacionExtras.php';
	$ch = curl_init($url);
	$jsonDataEncoded = json_encode($Datos);
	curl_setopt($ch, CURLOPT_POST, 1);
	curl_setopt($ch, CURLOPT_POSTFIELDS, $jsonDataEncoded);
	curl_setopt($ch, CURLOPT_HTTPHEADER, array('Content-Type: application/json'));
	$result = curl_exec($ch);
	echo $resullt;
}
