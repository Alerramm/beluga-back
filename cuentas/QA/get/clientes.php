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
        $consulta = "SELECT nombre, diasCredito  FROM clientes where activo not in ('1','No') order by nombre";
        $clientes = mysqli_query($conexion, $consulta);
        while ($row = $clientes->fetch_array(MYSQLI_ASSOC)) {
            $dataClientes[] = $row;
        }

        //Response
        if (empty($dataClientes)) {
            respuesta(200, 404, "No existen clientes ", []);
        } else {

            $payload = $dataClientes;
            respuesta(200, 200, "Respuesta exitosa", $payload);
        }
    }
} else {
    $payload = ["Faltantes" => $faltantes];
    respuesta(400, 400, "Hay un error con el servidor. Llama a central Error-CU-BAD-REQUEST", $payload);
}
