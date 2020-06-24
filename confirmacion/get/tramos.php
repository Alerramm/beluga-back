<?PHP
//datos .env
include '../production.php';

//header
header('Access-Control-Allow-Origin: *');
header("Content-Type: application/json; charset=UTF-8");

//const 
$faltantes = [];
$siguiente = true;

//datos Request
$datos = json_decode(file_get_contents('php://input'), true);
$id = $datos["id"];

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
if ($id == "") {
    array_push($faltantes, 'id');
}

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
       

        //Consulta tramos
        $consulta = "SELECT *  FROM tramos where idViaje = '$id'";
        $tramos = mysqli_query($conexion, $consulta);
        //Response
        if (empty($tramos)) {
            respuesta(200, 404, "No existen tramos ", []);
        } else {
            while ($row = $tramos->fetch_array(MYSQLI_ASSOC)) {
                $date = new DateTime($row["fecha"]);
                $row["fecha"] = $date->format('Y-m-d H:i');
                $dataTramos[] = $row;
            }
            $payload = $dataTramos;
            respuesta(200, 200, "Respuesta exitosa", $payload);
        }
    }
} else {
    $payload = ["Faltantes" => $faltantes];
    respuesta(400, 400, "Hay un error con el servidor. Llama a central Error-VI-BAD-REQUEST", $payload);
}
