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

//variables tabla empresa_viaje

$idViaje = $datos["idViaje"];
$diesel = $datos["diesel"];




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
if ($diesel == "") {
    array_push($faltantes, 'diesel');
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


        $consulta =  "SELECT A.id AS idViaje, A.diesel AS DieselViaje , A.distancia AS distancia, B.idMetricasPrecio AS idMetricasPrecio, C.rendimiento AS rendimiento FROM viajes AS A INNER JOIN precio_viaje AS B ON A.id=B.idViaje INNER JOIN metricas_precio AS C ON B.idMetricasPrecio = C.id WHERE A.id = '$idViaje'";

        $usuarios =  mysqli_query($conexion, $consulta);
        $row = mysqli_fetch_array($usuarios, MYSQLI_ASSOC);


        $payloadGastosInsert[] = ["Resultado Select" => " Select " . $row["idViaje"], $row["DieselViaje"], $row["idMetricasPrecio"],$row["rendimiento"],$row["distancia"]];


        $litros=$diesel/$row["DieselViaje"];

        $payloadGastosInsert[] = ["Diesel tabla Viajes" => " Select " . $row["DieselViaje"]];
        $payloadGastosInsert[] = ["Diesel Division" => " Select " . $litros];

        $payloadGastosInsert[] = ["Distancia" => " Select " . $row["distancia"]];

        $kilometros= $row["distancia"]/1000;

        $payloadGastosInsert[] = ["Distancia entre mil" => " Select " . $kilometros];



        $rendimiento = $kilometros / $litros;
        

        $payloadGastosInsert[] = ["Rendimiento Final " => " Select " . $rendimiento];

        $idMetricasPrecio = $row["idMetricasPrecio"];


        $UPDATE ="UPDATE metricas_precio SET rendimiento = '$rendimiento' WHERE metricas_precio.id = '$idMetricasPrecio'";



        
        if ($conexion->query($UPDATE) === TRUE) {

            $viajesActualizados =  mysqli_query($conexion, $consulta);
            $rowActualizado = mysqli_fetch_array($viajesActualizados, MYSQLI_ASSOC);


            $payloadGastosInsert = ["Rendimiento" => $rendimiento, "id" => $idMetricasPrecio];

            respuesta(200, 200,  "Respuesta exitosa", $payloadGastosInsert);


        } else {
            $payloadGastosInsert = ["sql" => "Error: " . $UPDATE . "<br>" . $conexion->error];
            respuesta(500, 500,  "Hay un error con el servidor. Llama a central Error-TAUPD", $payloadGastosInsert);
        }




       
    }
} else {
    $payload = ["Faltantes" => $faltantes];
    respuesta(400, 400, "Hay un error con el servidor. Llama a central Error-TARE1", $payload);
}
