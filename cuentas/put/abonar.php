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
$id = $datos["id"];
$monto = $datos["monto"];

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
if ($id == "") {
    array_push($faltantes, 'id');
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
        $consulta =  "SELECT *  FROM operaciones where id = $id";
        $factura =  mysqli_query($conexion, $consulta);
        $row = mysqli_fetch_array($factura, MYSQLI_ASSOC);

        if (empty($row)) {
            respuesta(200, 404, "Hay un error con el servidor. Llama a central. Error-ID OPERACIONES EMPTY" . $id, []);
        } else {
            //Update
            $montoUpdate = $monto + $row['montoPorPagar'];
            $updateEstatus =  "UPDATE operaciones SET montoPagado='$montoUpdate' WHERE id = $id;";

            if ($conexion->query($updateEstatus) === TRUE) {
                $rowActualizado = file_get_contents("http://www.misistema.mx/beluga/Finanzas/endpoints/cuentas/get/facturas.php");
                respuesta(200, 200,  "Respuesta exitosa", json_decode($rowActualizado));
            } else {
                $payload = ["sql" => "Error: " . $updateEstatus . "<br>" . $conexion->error];
                respuesta(500, 500,  "Hay un error con el servidor. Llama a central Error-ABONAR ERROR UPDATE", $payload);
            }
        }
    }
} else {
    $payload = ["Faltantes" => $faltantes];
    respuesta(400, 400, "Hay un error con el servidor. Llama a central Error-ABONAR BAD REQUEST", $payload);
}
