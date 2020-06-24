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
                if ($tramo["numEmbarque"] == 0) {
                    array_push($faltantes, 'Numero Embarque Tramo ' . $count);
                    $mensaje = "Necesitamos que complete todos los campos obligatorios antes de poder continuar";
                }
                if ($tramo["cajas"] == 0) {
                    array_push($faltantes, 'Cajas Tramo ' . $count);
                    $mensaje = "Necesitamos que complete todos los campos obligatorios antes de poder continuar";
                }
                $count = $count + 1;
            }
        }
    }
}



if (empty($faltantes)) {
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
            foreach ($tramos as &$tramo) {
                $numEmbarque = $tramo["numEmbarque"];
                $cajas = $cajas + $tramo["cajas"];

                //const
                $tramoId = $tramo["idTramo"];
                $tramoEmbarque = $tramo["numEmbarque"];
                $tramoCajas = $tramo["cajas"];

                //Update Cajas Operaciones
                $updateCajasTramos =  "UPDATE tramos SET embarque ='$tramoEmbarque', cajas ='$tramoCajas' WHERE id = $tramoId;";
                if ($conexion->query($updateCajasTramos) === TRUE) {
                    $payloadCajasTramos[] = ["sqlCajasTramos" => "Exito Update record successfully", "idTramo" => $tramoId];
                } else {
                    $payloadCajasTramos[] = ["sqlCajasTramos" => "Error: " . $updateCajasTramos . "<br>" . $conexion->error];
                    $siguiente = false;
                }
            }
            //Update Cajas Operaciones
            $updateCajasOperaciones =  "UPDATE operaciones SET numEmbarque='$numEmbarque', Cajas ='$cajas', checkList = '$checklist'  WHERE idViaje = $idViaje;";
            if ($conexion->query($updateCajasOperaciones) === TRUE) {
                $payloadCajasOperaciones = ["sqlCajasOperaciones" => "Exito Update record successfully", " query" => $updateCajasOperaciones];
            } else {
                $payloadCajasOperaciones = ["sqlCajasOperaciones" => "Error: " . $updateCajasOperaciones . "<br>" . $conexion->error];
                $siguiente = false;
            }

            //Update Estatus
            $updateEstatus =  "UPDATE viaje SET estatus='En trayecto'  WHERE id = $idViaje;";
            if ($conexion->query($updateEstatus) === TRUE) {
                $payloadEstatus = ["sqlEstatus" => " Exito Update record successfully"];
            } else {
                $payloadEstatus = ["sqlEstatus" => "Error: " . $updateEstatus . "<br>" . $conexion->error];
                $siguiente = false;
            }


            if ($siguiente) {
                $payload = array_merge($payloadEstatus, $payloadCajasOperaciones, $payloadCajasTramos);
                respuesta(200, 200, "Respuesta exitosa", $payload);
            } else {
                $payload = array_merge($payloadEstatus, $payloadCajasOperaciones, $payloadCajasTramos);
                respuesta(500, 500, "Hay un error con el servidor. Llama a central Error-CLUPD", $payload);
            }
        }
    }
} else {
    $payload = ["Faltantes" => $faltantes];
    respuesta(400, 400, $mensaje, $payload);
}
