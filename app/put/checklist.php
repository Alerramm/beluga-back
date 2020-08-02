<?PHP
//datos .env
include '../production.php';

//const 
$siguiente = true;
$faltantes = [];
$mensaje = '';

//datos Request
$datos = json_decode(file_get_contents('php://input'), true);
$idViaje = $datos["idViaje"];
$checklist = $datos["checklist"];
$tramos = $datos["tramos"];


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

//Validacion de Datos
if ($idViaje == "") {
    array_push($faltantes, 'idViaje');
    $mensaje = "Hay un error con el servidor. Llama a central Error-CLRE1(idViaje)";
} else {
    if (is_bool($checklist) === false) {
        array_push($faltantes, 'checklist');
        $mensaje = "Hay un error con el servidor. Llama a central Error-CLRE1(checklist)";
    } else {
        if (empty($tramos)) {
            array_push($faltantes, 'tramos');
            $mensaje = "Hay un error con el servidor. Llama a central Error-CLRE1(tramos)";
        } else {
            $count = 1;
            foreach ($tramos as &$tramo) {

                if ($tramo["idTramo"] == 0) {
                    array_push($faltantes, 'Id Tramo ' . $count);
                    $mensaje = "Necesitamos que complete todos los campos obligatorios antes de poder continuar";
                }
                foreach ($tramo["embarques"] as &$embarque) {
                    if ($embarque["numEmbarque"] == 0) {
                        array_push($faltantes, 'Numero Embarque Tramo ' . $count);
                        $mensaje = "Necesitamos que complete todos los campos obligatorios antes de poder continuar";
                    }
                    if ($embarque["cajas"] == 0) {
                        array_push($faltantes, 'Cajas Tramo ' . $count);
                        $mensaje = "Necesitamos que complete todos los campos obligatorios antes de poder continuar";
                    }
                }
                $count = $count + 1;
            }
        }
    }
}



if (empty($faltantes)) {
    $numEmbarque = '';
    $cajasO = "";
    //Conexion a base de datos
    $mysqli = mysqli_init();
    $conexion = mysqli_connect($_SESSION['HOST'], $_SESSION['USER'], $_SESSION['PASS'], $_SESSION['DBNAME']);
    //Validacion conexion con bd
    if (!$conexion) {
        respuesta(500, 500, "Hay un error con el servidor. Llama a central Error-CLBD1", []);
    } else {
        //configuracon db
        mysqli_query($conexion, "SET CHARACTER SET 'utf8'");
        mysqli_query($conexion, "SET SESSION collation_connection ='utf8_unicode_ci'");

        //Analisis de la informacion 
        $consulta =  "SELECT *  FROM operaciones where idViaje = $idViaje";
        $viaje =  mysqli_query($conexion, $consulta);
        $row = mysqli_fetch_array($viaje, MYSQLI_ASSOC);

        if (empty($row)) {
            respuesta(200, 404, "Hay un error con el servidor. Llama a central. Error-CLOPD" . $idViaje, []);
        } else {
            //Update Cajas Tramo
            $cont = 0;
            foreach ($tramos as &$tramo) {
                //const
                $tramoId = $tramo["idTramo"];

                if ($cont == 0) {
                    $tramoPrimero = $tramo["idTramo"]  - 1;
                    $cont = 1;
                }
                //Update Cajas Operaciones
                foreach ($tramo["embarques"] as &$embarque) {
                    $cajas = $embarque["cajas"];
                    $numero = $embarque["numEmbarque"];
                    $numEmbarque = $embarque["numEmbarque"] . "," . $numEmbarque;
                    $cajasO =  $embarque["cajas"] . "," . $cajasO;
                    $updateCajasTramos =  "INSERT INTO embarques( numero, cajas, idTramo, estatus) VALUES ('$numero','$cajas','$tramoId','Pendiente')";
                    if ($conexion->query($updateCajasTramos) === TRUE) {
                        $payloadCajasTramos[] = ["sqlCajasTramos" => "Exito Insert record successfully", "idTramo" => $tramoId];
                    } else {
                        $payloadCajasTramos[] = ["sqlCajasTramos" => "Error: " . $updateCajasTramos . "<br>" . $conexion->error];
                        $siguiente = false;
                    }
                }
            }

            //Update Estatus
            $updateEstatus =  "UPDATE viajes SET estatus='En trayecto', checklist = '$checklist'  WHERE id = $idViaje;";
            if ($conexion->query($updateEstatus) === TRUE) {
                $payloadEstatus = ["sqlEstatus" => " Exito Update record successfully"];
            } else {
                $payloadEstatus = ["sqlEstatus" => "Error: " . $updateEstatus . "<br>" . $conexion->error];
                $siguiente = false;
            }

            //Update Estatus
            $updateTramos =  "UPDATE tramos SET estatus='Finalizado'  WHERE id = $tramoPrimero;";
            if ($conexion->query($updateTramos) === TRUE) {
                $payloadEstatusTramos = ["sqlEstatusTramos" => " Exito Update record successfully"];
            } else {
                $payloadEstatusTramos = ["sqlEstatusTramos" => "Error: " . $updateTramos . "<br>" . $conexion->error];
                $siguiente = false;
            }

            if ($siguiente) {
                $payload = array_merge($payloadEstatus, $payloadCajasTramos, $payloadEstatusTramos);
                respuesta(200, 200, "Respuesta exitosa", $payload);
            } else {
                $payload = array_merge($payloadEstatus, $payloadCajasTramos, $payloadEstatusTramos);
                respuesta(500, 500, "Hay un error con el servidor. Llama a central Error-CLUPD", $payload);
            }
        }
    }
} else {
    $payload = ["Faltantes" => $faltantes];
    respuesta(400, 400, $mensaje, $payload);
}
