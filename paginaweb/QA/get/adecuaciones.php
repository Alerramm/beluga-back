<?PHP
//datos .env
include '../production.php';

//header
header('Access-Control-Allow-Origin: *');



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
        $consulta =  "SELECT idTIpoADecuacion, nombreAdecuacion FROM adecuacion";
        
        $selectAdecuaciones =  mysqli_query($conexion, $consulta);
        while ($row = $selectAdecuaciones->fetch_array(MYSQLI_ASSOC)) {
            $adecuaciones[] = $row;
        }

        //Response
        if (empty($adecuaciones)) {
            respuesta(200, 404, "No hay adecuaciones", []);
        } else {
            respuesta(200, 200, "Respuesta exitosa", $adecuaciones);
        }
    }

