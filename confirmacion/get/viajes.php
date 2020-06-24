<?PHP
//datos .env
include '../production.php';

//header
header('Access-Control-Allow-Origin: *');
header("Content-Type: application/json; charset=UTF-8");

//const 
$faltantes = [];

//datos Request
file_get_contents("http://www.misistema.mx/beluga/Finanzas/endpoints/confirmacion/post/inicioConfirmacion.php");

//funciones
function respuesta($codehttp, $code, $mensaje, $factload)
{
    http_response_code($codehttp);
    $dataFinal = [
        "headerResponse" => [
            "code" => $code,
            "mensaje" => $mensaje
        ],
        "payload" => $factload
    ];
    echo json_encode($dataFinal);
}

//Validacion de Datos

if (empty($faltantes)) {
    //Conexion a base de datos
    $mysqli = mysqli_init();
    $conexion = mysqli_connect($_SESSION['HOST'], $_SESSION['USER'], $_SESSION['PASS'], $_SESSION['DBNAME']);
    //Validacion conexion con bd
    if (!$conexion) {
        respuesta(500, 500, "Hay un error con el servidor. Llama a central Error-CONECTION-DATA", []);
    } else {
        //configuracon db
        mysqli_query($conexion, "SET CHARACTER SET 'utf8'");
        mysqli_query($conexion, "SET SESSION collation_connection ='utf8_unicode_ci'");


        //Consulta viajes
        $consulta = "SELECT *  FROM viajes where estatus = 'Confirmado'";

        $viajes = mysqli_query($conexion, $consulta);
        while ($row = $viajes->fetch_array(MYSQLI_ASSOC)) {
            $baseDeOperaciones = $row["base"];
            $consultaBase = mysqli_query($conexion, "SELECT nombre FROM baseDeOperaciones where direccion = '$baseDeOperaciones'");
            $base = mysqli_fetch_array($consultaBase, MYSQLI_ASSOC);
            $row["base_operaciones"] = $base["nombre"];
            $dateC = new DateTime($row["fecha_carga"]);
            $row["fecha_carga"] = $dateC->format('Y-m-d H:i');
            $dateE = new DateTime($row["fecha_entrega"]);
            $row["fechaEntregaTemporal"] = $dateE->format('Y-m-d H:i');
            $idViaje = $row["id"];
            $consultaTempOperaciones = mysqli_query($conexion, "SELECT confirmaViaje from tempOperacion WHERE idViaje = $idViaje");
            $tempOperacion = mysqli_fetch_array($consultaTempOperaciones, MYSQLI_ASSOC);
            $row["estatusOperador"] =  $tempOperacion["confirmaViaje"];
            $dataViajes[] = $row;
        }
        //Response
        if (empty($dataViajes)) {
            respuesta(200, 404, "No existen viajes ", []);
        } else {
            $payload = $dataViajes;
            respuesta(200, 200, "Respuesta exitosa", $payload);
        }
    }
} else {
    $payload = ["Faltantes" => $faltantes];
    respuesta(400, 400, "Hay un error con el servidor. Llama a central Error-VI-BAD-REQUEST", $payload);
}
