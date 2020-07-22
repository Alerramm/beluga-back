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

//variables tabla empresa_viaje

$idViaje = $datos["idViaje"];
$TipoGasto = $datos["TipoGasto"];
$presupuesto = $datos["presupuesto"];




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
}
if ($TipoGasto == "") {
    array_push($faltantes, 'TipoGasto');
}
if ($presupuesto == "") {
    array_push($faltantes, 'presupuesto');
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


        $presupuesto = str_replace ( ",", '', $presupuesto);

        $UPDATE = "UPDATE gastos SET `presupuesto` = '$presupuesto' WHERE idViaje = '$idViaje' AND tipo = '$TipoGasto'";





        if ($conexion->query($UPDATE) === TRUE) {

            $viajesActualizados =  mysqli_query($conexion, $consulta);
            $rowActualizado = mysqli_fetch_array($viajesActualizados, MYSQLI_ASSOC);


            $payloadGastosInsert = ["Presupuesto Actualizado" => $presupuesto, "id" => $idViaje];

            respuesta(200, 200,  "Respuesta exitosa", $payloadGastosInsert);
        } else {
            $payloadGastosInsert = ["sql" => "Error: " . $UPDATE . "<br>" . $conexion->error];
            respuesta(500, 500,  "Hay un error con el servidor. Llama a central Error-TAUPD", $payloadGastosInsert);
        }
    }
} else {
    $payload = ["Faltantes" => $faltantes];
    respuesta(400, 400, "Hay un error con el servidor. Llama a central Error-TARE1", $payload);
}
