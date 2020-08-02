<?PHP
//datos .env
include '../production.php';

//header
header('Access-Control-Allow-Origin: *');
header("Content-Type: application/json; charset=UTF-8");



//funciones
function respuesta($codehttp, $code, $mensaje, $payload)
{
    http_response_code($codehttp);
    $dataFinal = [
        "headerResponse" => [
            "code" => $code,
            "mensaje" => $mensaje
        ],
        "payload" => $payload
    ];
    echo json_encode($dataFinal);
}

$datosArreglo = json_decode(file_get_contents('php://input'), true);
$exitoso = false;

foreach ($datosArreglo as &$datos) {
    //const 
    $faltantes = [];


    //datos Request

    $idViaje = $datos["idViaje"];
    $precio = $datos["precio"];
    $diesel = $datos["diesel"];
    $casetas = $datos["casetas"];
    $viaticos = $datos["viaticos"];
    $comision = $datos["comision"];
    $transito = $datos["transito"];
    $maniobras = $datos["maniobras"];
	$externo = $datos["externo"];
	$custodia = $datos["custodia"];
    $datosgastos = $datos["gastos"];



    //Validacion de Datos
    if ($idViaje == "") {
        array_push($faltantes, 'idViaje');
    }

    if (empty($faltantes)) {
        //Conexion a base de datos
        $mysqli = mysqli_init();
        $conexion = mysqli_connect($_SESSION['HOST'], $_SESSION['USER'], $_SESSION['PASS'], $_SESSION['DBNAME']);
        //Validacion conexion con bd
        if (!$conexion) {
            respuesta(500, 500, "Hay un error con el servidor. Llama a central Error-ABONAR-DB", []);
        } else {
            //configuracon db
            mysqli_query($conexion, "SET CHARACTER SET 'utf8'");
            mysqli_query($conexion, "SET SESSION collation_connection ='utf8_unicode_ci'");

            //Analisis de la informacion
            $consulta =  "SELECT *  FROM viajes where id = $idViaje";
            $viaje =  mysqli_query($conexion, $consulta);
            $rowViaje = mysqli_fetch_array($viaje, MYSQLI_ASSOC);

            $viajeOperacionesC = mysqli_query($conexion, "SELECT max(viaje)  as viaje FROM operaciones where viaje <'99000'");
            $viajeOperacionesT = mysqli_fetch_array($viajeOperacionesC, MYSQLI_ASSOC);
            $viajeOperaciones = $viajeOperacionesT["viaje"] + 1;

            if (empty($rowViaje)) {
                respuesta(200, 404, "Hay un error con el servidor. Llama a central. Error-ID VIAJE EMPTY" . $id, []);
            } else {
                //Update
                $fecha_carga = $rowViaje['fecha_carga'];
                $operador = $rowViaje['operador'];
                $destino = $rowViaje['destino'];
                $insertDesgloseAuth =  "INSERT INTO desgloseGastosAut(
				fecha,operador,destino,viaje,PREdiesel,Precasetas,PREviaticos,PREcomision,PREtransito,
				PREmaniobras,PREexterno,PREcustodia,solicita,idViaje) VALUES (
				'$fecha_carga', '$operador','$destino',$idViaje,'$diesel','$casetas','$viaticos',
				'$comision','$transito','$maniobras','$externo','$custodia','',$idViaje)";

                /* foreach ($datosgastos as &$datos2) {
                    //const 
                    $faltantes = [];
                    //datos Request
                    $tipo = $datos2["tipo"];
                    $presupuesto = $datos2["presupuesto"];
                    $insertDesgloseAuth2 =  "INSERT INTO gastos(tipo,presupuesto,idViaje,estatus) VALUES ('$tipo', '$presupuesto', '$idViaje', 'Presupuesto')";

                    if ($conexion->query($insertDesgloseAuth2) === TRUE) {

                        $last_id = $conexion->insert_id;
                        $payloadGastosInsert[] = ["GastosInsert" => " Exito New Travel record created successfully " . $last_id];
                    } else {
                        $payloadGastosInsert[] = ["sql" => "Error: " . "<br>" . $conexion->error];
                    }
                } */

                foreach ($datosgastos as &$datos2) {
                    //const 
                    $faltantes = [];
                    //datos Request
                    $tipo = $datos2["tipo"];
                    $presupuesto = $datos2["presupuesto"];
                    $insertDesgloseAuth2 =  "UPDATE gastos set presupuesto = '$presupuesto' WHERE idViaje = $idViaje and tipo = '$tipo'";

                    if ($conexion->query($insertDesgloseAuth2) === TRUE) {

                        $last_id = $conexion->insert_id;
                        $payloadGastosInsert[] = ["GastosInsert" => " Exito New Travel record created successfully " . $last_id];
                    } else {
                        $payloadGastosInsert[] = ["sql" => "Error: " . "<br>" . $conexion->error];
                    }
                }

                if ($conexion->query($insertDesgloseAuth) === TRUE) {
                    $last_id = $conexion->insert_id;
                    $payloadAuth = ["sqlEstatusAuth" => " Exito New Travel record created successfully " . $last_id];


                    $cliente = $rowViaje['cliente'];
                    $fecha_entrega = $rowViaje['fecha_entrega'];
                    $distancia = $rowViaje['distancia'];
                    $hora_carga = date($fecha_carga, "H:i");
                    $hora_entrega = date($hora_entrega, "H:i");
                    $entrega = $rowViaje["ruta"];
                    $insertOperaciones =  "INSERT INTO operaciones (viaje,fecha,cliente,cargar,unidad,operador,destino,fechaEntrega,entregar,status,status_factura,kms,precio,cargar_hora,horaEntrega,diesel,casetas,comision,viaticos,transito,revision,dias,maniobras,idViaje) VALUES 
                        ('$idViaje','$fecha_carga','$cliente','','$unidad','$operador','$destino','$fecha_entrega',
                        '$entrega','99','38','$distancia','$precio','$hora_carga','$hora_entrega','0','0','0','0','0','$fecha_disponibilidad','0','0','$idViaje')";
                    if ($conexion->query($insertOperaciones) === TRUE) {
                        $last_id = $conexion->insert_id;
                        $payloadOperaciones = ["sqlEstatusOperaciones" => " Exito New Travel record created successfully " . $last_id];
                        $updateViaje = "UPDATE viajes SET estatus = 'Gastos' where id = $idViaje";
                        if ($conexion->query($updateViaje) === TRUE) {
                            $payloadViaje = ["sqlEstatusUpdate" => $idViaje];
                            $payloadFinal[] = array_merge($payloadGastosInsert, $payloadAuth, $payloadOperaciones, $payloadViaje, ["viaje operaciones" => $viajeOperaciones]);
                            $exitoso = true;
                        } else {
                            $payload = ["sql" => "Error: " . $updateEstatus . "<br>" . $conexion->error];
                            respuesta(500, 500,  "Hay un error con el servidor. Llama a central Error-UPDATE VIAJE", $payload);
                        }
                    } else {
                        $payload = ["sql" => "Error: " . $updateEstatus . "<br>" . $conexion->error];
                        respuesta(500, 500,  "Hay un error con el servidor. Llama a central Error-INSERT OPERACIONES", $payload);
                    }
                } else {
                    $payload = ["sql" => "Error: " . $updateEstatus . "<br>" . $conexion->error];
                    respuesta(500, 500,  "Hay un error con el servidor. Llama a central Error-INSERT DESGLOSE AUTH", $payload);
                }
            }
        }
    } else {
        $payload = ["Faltantes" => $faltantes];
        respuesta(400, 400, "Hay un error con el servidor. Llama a central Error-ABONAR BAD REQUEST", $payload);
    }
}
if ($exitoso) {
    respuesta(200, 200,  "Respuesta exitosa", $payloadFinal);
}
