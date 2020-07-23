<?PHP
//datos .env
include '../production.php';

//header
header('Access-Control-Allow-Origin: *');
header("Content-Type: application/json; charset=UTF-8");

//const 
$faltantes = [];
$disel = 0;
$casetas = 0;
$alimentos = 0;
$comision = 0;
$estadias = 0;
$maniobras = 0;
$transito = 0;
$mantenimiento = 0;

//datos Request
$datos = json_decode(file_get_contents('php://input'), true);
$id = $datos["id"];

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


function consulta($conexion, $consulta)
{
    //Consulta 
    $query = mysqli_query($conexion, $consulta);
    $row_cnt = $query->num_rows;
    if ($row_cnt > 0) {
        while ($row = $query->fetch_array(MYSQLI_ASSOC)) {
            $respuesta[] = $row;
        }
    } else {
        $respuesta = [];
    }

    return $respuesta;
}

function consultaGastos($conexion, $consulta)
{
    //Consulta 
    $query = mysqli_query($conexion, $consulta);
    $row_cnt = $query->num_rows;
    if ($row_cnt > 0) {
        while ($row = $query->fetch_array(MYSQLI_ASSOC)) {
            $id = $row["id"];
            $row["dispersiones"] = consulta($conexion, "SELECT * FROM dispersiones where idGasto = '$id'");
            $respuesta[] = $row;
        }
    } else {
        $respuesta = [];
    }

    return $respuesta;
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
        respuesta(500, 500, "Hay un error con el servidor. Llama a central Error-GABD1", []);
    } else {
        //configuracon db
        mysqli_query($conexion, "SET CHARACTER SET 'utf8'");
        mysqli_query($conexion, "SET SESSION collation_connection ='utf8_unicode_ci'");

        //Analisis de la informacion
        $gastos = consultaGastos($conexion, "SELECT * FROM gastos where idViaje = '$id'");

        //Response
        if (empty($gastos)) {
            respuesta(200, 404, "Este viaje no tiene depositos", []);
        } else {
            respuesta(200, 200, "Respuesta exitosa", $gastos);
        }
    }
} else {
    $payload = ["Faltantes" => $faltantes];
    respuesta(400, 400, "Hay un error con el servidor. Llama a central Error-GARE1", $payload);
}
