<?PHP
//datos .env
include '../production.php';

//header
header('Access-Control-Allow-Origin: *');
header("Content-Type: application/json; charset=UTF-8");

//const 
$faltantes = [];

//datos Request
$datos = json_decode(file_get_contents('php://input'), true);
$idViaje = $datos["idViaje"];
$idTramo = $datos["idTramo"];
$cajas = $datos["cajas"];
$tramoFinal = $datos["tramoFinal"];

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
if ($idTramo == "") {
    array_push($faltantes, 'idTramo');
}
if ($idViaje == "") {
    array_push($faltantes, 'idViaje');
}
if (!array_key_exists('cajas', $datos)) {
    array_push($faltantes, 'cajas');
}

if (!array_key_exists('tramoFinal', $datos)) {
    array_push($faltantes, 'tramoFinal');
}


if (empty($faltantes)) {
    //Conexion a base de datos
    $mysqli = mysqli_init();
    $conexion = mysqli_connect($_SESSION['HOST'], $_SESSION['USER'], $_SESSION['PASS'], $_SESSION['DBNAME']);
    //Validacion conexion con bd
    if (!$conexion) {
        respuesta(500, 500, "Hay un error con el servidor. Llama a central Error-FTBD1", []);
    } else {
        //configuracon db
        mysqli_query($conexion, "SET CHARACTER SET 'utf8'");
        mysqli_query($conexion, "SET SESSION collation_connection ='utf8_unicode_ci'");

        //Analisis de la informacion
        $consulta =  "SELECT id, cajas, estatus  FROM tramos where id = $idTramo";
        $tramo =  mysqli_query($conexion, $consulta);
        $row = mysqli_fetch_array($tramo, MYSQLI_ASSOC);

        if (empty($row)) {
            respuesta(200, 404, "Hay un error con el servidor. Llama a central. Error-FTTRI" . $idTramo, []);
        } else {
            if ($row["cajas"] == $cajas || $tramoFinal) {
                //Update
                $updateEstatus =  "UPDATE tramos SET estatus='Finalizado' WHERE id = $idTramo;";
                if ($conexion->query($updateEstatus) === TRUE) {
                    $tramoActualizado =  mysqli_query($conexion, $consulta);
                    $rowActualizado = mysqli_fetch_array($tramoActualizado, MYSQLI_ASSOC);


                    if ($tramoFinal) {
                        //Update
                        $updateViajeEstatus =  "UPDATE viaje SET estatus='Finalizado' WHERE id = $idViaje";
                        if ($conexion->query($updateViajeEstatus) === TRUE) {
                            $payload = ["sql" => "Exito Update record successfully", "idTramo" => $rowActualizado["id"], "estatus" => $rowActualizado["estatus"], "cajas" => $rowActualizado["cajas"], "viajeEstatus" => "Finalizado"];

                            respuesta(200, 200,  "Respuesta exitosa", $payload);
                        } else {
                            $payload = ["sql" => "Error: " . $updateViajeEstatus . "<br>" . $conexion->error];
                            respuesta(500, 500,  "Hay un error con el servidor. Llama a central Error-FTUPD", $payload);
                        }
                    } else {
                        $payload = ["sql" => "Exito Update record successfully", "idTramo" => $rowActualizado["id"], "estatus" => $rowActualizado["estatus"], "cajas" => $rowActualizado["cajas"]];
                        respuesta(200, 200,  "Respuesta exitosa", $payload);
                    }
                } else {
                    $payload = ["sql" => "Error: " . $updateEstatus . "<br>" . $conexion->error];
                    respuesta(500, 500,  "Hay un error con el servidor. Llama a central Error-FTUPD", $payload);
                }
            } else {
                //Update
                $updateEstatus =  "UPDATE tramos SET estatus='Bloqueado' WHERE id = $idTramo;";
                if ($conexion->query($updateEstatus) === TRUE) {
                    $tramoActualizado =  mysqli_query($conexion, $consulta);
                    $rowActualizado = mysqli_fetch_array($tramoActualizado, MYSQLI_ASSOC);

                    $payload = ["sql" => "Exito Update record successfully", "idTramo" => $rowActualizado["id"], "estatus" => $rowActualizado["estatus"], "cajas" => $rowActualizado["cajas"]];

                    respuesta(400, 404, "Las cajas no coinciden. Llama a central para poder continuar con tu viaje.", $payload);
                } else {
                    $payload = ["sql" => "Error: " . $updateEstatus . "<br>" . $conexion->error];
                    respuesta(500, 500,  "Hay un error con el servidor. Llama a central Error-FTUPD", $payload);
                }
            }
        }
    }
} else {
    $payload = ["Faltantes" => $faltantes];
    respuesta(400, 400, "Hay un error con el servidor. Llama a central Error-FTRE1", $payload);
}
