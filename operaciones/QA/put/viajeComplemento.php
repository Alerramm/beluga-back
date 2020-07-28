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
$operador = $datos["operador"];
$unidad = $datos["unidad"];
$idViaje = $datos["idViaje"];




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

        //Analisis de la informacion

        //Insert
        $updateEstatus =  "INSERT INTO viajes_complemento(idViaje, operador, unidad) VALUES ($idViaje,'$operador','$unidad')";



        if ($conexion->query($updateEstatus) === TRUE) {
            $last_id = $conexion->insert_id;


            $payload = ["sql" => "Exito Insert record successfully", "id" => $last_id];

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
