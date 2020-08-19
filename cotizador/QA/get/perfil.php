<?PHP
include '../production.php';

header('Access-Control-Allow-Origin: *');
header('Content-Type: application/json; charset=UTF-8');

//Datos de entrada
$datos = json_decode(file_get_contents('php://input'), true);
$idUsuario = $datos["idUsuario"];

function respuesta($codehttp, $code, $mensaje, $payload)
{
    http_response_code($codehttp);
    $dataFinal = ["headerResponse" => ["code" => $code, "mensaje" => $mensaje], "payload" => $payload];
    echo json_encode($dataFinal);
}

function consulta($conexion, $consulta)
{
    //Consulta 
    $query = mysqli_query($conexion, $consulta);
    $row_cnt = $query->num_rows;
    if ($row_cnt > 0) {
        while ($row = $query->fetch_array(MYSQLI_ASSOC)) {
            $row["key"] = $row["idUsuario"];
            $respuesta[] = $row;
        }
    } else {
        $respuesta = [];
    }

    return  $respuesta;
}

if (empty($faltantes)) {

    $mysqli = mysqli_init();
    $conexion = mysqli_connect($_SESSION['HOST'], $_SESSION['USER'], $_SESSION['PASS'], $_SESSION['DBNAME']);
    //Validacion conexion con bd
    if (!$conexion) {
        respuesta(500, 500, "Hay un error con el servidor. Llama a central Error-CLBD1", []);
    } else {
        $consulta =  consulta($conexion, "SELECT U.id as idUsuario, U.nombre as nombreUsuario, U.mail as correoUsuario, 
        C.telefono as telefonoUsuario, C.tipoCliente as perfilFacturacion, C.razonSocial as rezonSocialFacturacion,
        C.rfc as rfcFacturacion, C.nombre as nombreFacturacion, C.email as correoFacturacion FROM usuarios U INNER JOIN 
        clientes C ON U.id = C.idUsuario WHERE U.id = $idUsuario");
        $registroOk = true;
        if (empty($consulta)) {
            $registroOk = false;
        } else {
            $registroOk = true;
        }

        if ($registroOk) {
            respuesta(200, 200, "Respuesta exitosa", $consulta[0]);
        } else {
            respuesta(200, 204, "El usuario no se encuentra registrado.", $consulta);
        }
    }
} else {
    $payload = ["Faltantes" => $faltantes];
    respuesta(400, 400, "Hay un error con el servidor. Llama a central Error-TARE1", $payload);
}
