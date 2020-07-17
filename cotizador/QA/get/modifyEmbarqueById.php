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
$idTramo = $datos["idTramo"];

$numero =$datos["numero"];
$cajas = $datos["cajas"];
$cajas_entregadas = $datos["cajas_entregadas"];
$cajas_rechazadas = $datos["cajas_rechazadas"];
$idTramoDevolucion = $datos["idTramoDevolucion"];
$estatus = $datos["estatus"];




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

            $updateEstatus =  "UPDATE embarques SET 
                                                numero = '$numero';
                                                cajas='$cajas ',
                                                cajas_entregadas='$cajas_entregadas',
                                                cajas_rechazadas='$cajas_rechazadas',
                                                estatus='$estatus',
                                                idTramoDevolucion= '$idTramoDevolucion'
                                                     WHERE id = '$idTramo' ";


            if ($conexion->query($updateEstatus) === TRUE) {
                $viajesActualizados =  mysqli_query($conexion, $consulta);
                $rowActualizado = mysqli_fetch_array($viajesActualizados, MYSQLI_ASSOC);


                $payload = ["sql" => "Viaje Actualizado", "id" => $idTramo];

                respuesta(200, 200,  "Respuesta exitosa", $payload);
            } else {
                $payload = ["sql" => "Error: " . $updateEstatus . "<br>" . $conexion->error];
                respuesta(500, 500,  "Hay un error con el servidor. Llama a central Error-TAUPD", $payload);
            }
       
    }
} else {
    $payload = ["Faltantes" => $faltantes];
    respuesta(400, 400, "Hay un error con el servidor. Llama a central Error-TARE1", $payload);
}
