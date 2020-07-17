<?PHP
//datos .env
include '../production.php';

//header
header('Access-Control-Allow-Origin: *');

//const 
$faltantes = [];

//datos Request
$datos = json_decode(file_get_contents('php://input'), true);
$idTramo = $datos["idTramo"];

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

if (empty($faltantes)) {
    //Conexion a base de datos
    $mysqli = mysqli_init();
    $conexion = mysqli_connect($_SESSION['HOST'], $_SESSION['USER'], $_SESSION['PASS'], $_SESSION['DBNAME']);
    //Validacion conexion con bd
    if (!$conexion) {
        respuesta(500, 500, "Hay un error con el servidor. Llama a central Error-CABD1", []);
    } else {
        //configuracon db
        mysqli_query($conexion, "SET CHARACTER SET 'utf8'");
        mysqli_query($conexion, "SET SESSION collation_connection ='utf8_unicode_ci'");

        //Analisis de la informacion
        $consulta =  "SELECT nombre,precio as costo FROM casetas where id_tramo = '$idTramo' ";
        $selectCasetas =  mysqli_query($conexion, $consulta);
        while ($row = $selectCasetas->fetch_array(MYSQLI_ASSOC)) {
            $casetas[] = $row;
        }

        //Response
        if (empty($casetas)) {
            respuesta(200, 404, "Este tramo no contiene casetas", []);
        } else {
            respuesta(200, 200, "Respuesta exitosa", $casetas);
        }
    }
} else {
    $payload = ["Faltantes" => $faltantes];
    respuesta(400, 400, "Hay un error con el servidor. Llama a central Error-CARE1", $payload);
}
