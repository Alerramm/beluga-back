<?PHP
//datos .env
include '../production.php';

//header
header('Access-Control-Allow-Origin: *');
header("Content-Type: application/json; charset=UTF-8");

//const 
$faltantes = [];

//datos Request
$datosArreglo = json_decode(file_get_contents('php://input'), true);
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


foreach ($datosArreglo as &$datos) {

    $idViaje = $datos["idViaje"];
    $estatus_app = $datos["estatus_app"];
    $estatus = $datos["estatus"];
    $unidad = $datos["unidad"];
    $operador = $datos["operador"];

    if ($idViaje == "") {
        array_push($faltantes, 'idViaje');
    }

    if (empty($faltantes)) {
        //Conexion a base de datos
        $mysqli = mysqli_init();
        $conexion = mysqli_connect($_SESSION['HOST'], $_SESSION['USER'], $_SESSION['PASS'], $_SESSION['DBNAME']);
        //Validacion conexion con bd
        if (!$conexion) {
            respuesta(500, 500, "Hay un error con el servidor. Llama a central Error-TABD1", []);
        } else {
            //configuracon db
            mysqli_query($conexion, "SET CHARACTER SET 'utf8'");
            mysqli_query($conexion, "SET SESSION collation_connection ='utf8_unicode_ci'");

            $updateEstatus =  "UPDATE viajes SET 
                                                estatus_app='$estatus_app ',
                                                estatus='$estatus',
                                                unidad='$unidad',
                                                operador='$operador'
                                                
                                                     WHERE id = '$idViaje' ";



            if ($conexion->query($updateEstatus) === TRUE) {
                $viajesActualizados =  mysqli_query($conexion, $consulta);
                $rowActualizado = mysqli_fetch_array($viajesActualizados, MYSQLI_ASSOC);

                $exitoso = true;
                $payload[] = ["sql" => "Viaje Actualizado", "sqlEstatusUpdate" => $idViaje];
            } else {
                $payload[] = ["sql" => "Error: " . $updateEstatus . "<br>" . $conexion->error];
                respuesta(500, 500,  "Hay un error con el servidor. Llama a central Error-TAUPD", $payload);
            }
        }
    } else {
        $payload = ["Faltantes" => $faltantes];
        respuesta(400, 400, "Hay un error con el servidor. Llama a central Error-TARE1", $payload);
    }
}

if ($exitoso) {
    respuesta(200, 200,  "Respuesta exitosa", $payload);
}
