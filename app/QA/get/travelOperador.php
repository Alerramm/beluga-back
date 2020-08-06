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
$operador = $datos["nombre"];

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
if ($operador  == "") {
    array_push($faltantes, 'idViaje');
}

if (empty($faltantes)) {
    //Conexion a base de datos
    $mysqli = mysqli_init();
    $conexion = mysqli_connect($_SESSION['HOST'], $_SESSION['USER'], $_SESSION['PASS'], $_SESSION['DBNAME']);
    //Validacion conexion con bd
    if (!$conexion) {
        respuesta(500, 500, "Hay un error con el servidor. Llama a central Error-TRBD1", []);
    } else {
        //configuracon db
        mysqli_query($conexion, "SET CHARACTER SET 'utf8'");
        mysqli_query($conexion, "SET SESSION collation_connection ='utf8_unicode_ci'");

        //Consulta viajes
        $consulta = "SELECT *  FROM viajes where operador = '$operador' and estatus in ('Confirmado', 'Gastos', 'En proceso', 'En proceso cliente', 'En trayecto','En regreso') and estatus_app not in ('Evidencia')";
        $viaje = mysqli_query($conexion, $consulta);
        while ($row = $viaje->fetch_array(MYSQLI_ASSOC)) {
            $payload = [
                "idViaje" => $row["id"],
                "nombre" => $operador
            ];
        }

        //Response
        if (empty($payload)) {
            respuesta(200, 404, "No tienes viajes asignados", []);
        } else {

            respuesta(200, 200, "Respuesta exitosa", $payload);
        }
    }
} else {
    $payload = ["Faltantes" => $faltantes];
    respuesta(400, 400, "Hay un error con el servidor. Llama a central Error-TRRE1", $payload);
}
