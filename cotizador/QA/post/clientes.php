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

$image  = $datos["image"];
$razonSocial  = $datos["razonSocial"];
$rfc  = $datos["rfc"];
$nombre  = $datos["nombre"];
$email  = $datos["email"];
$idCliente = $datos["idCliente"];
$cfdi  = $datos["cfdi"];
$tipo = $datos["tipo"];






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
//if ($idViaje == "") {
//    array_push($faltantes, 'idViaje');
//}




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





        switch ($tipo) {
            case 'razonSocial':
                $updateEstatus =  "UPDATE clientes SET razonSocial='$razonSocial' WHERE id = '$idCliente'";
                break;
            case 'rfc':
                $updateEstatus =  "UPDATE clientes SET rfc='$rfc' WHERE id = '$idCliente'";
                break;
            case 'nombre':
                $updateEstatus =  "UPDATE clientes SET nombre='$nombre' WHERE id = '$idCliente'";
                break;
            case 'email':
                $updateEstatus =  "UPDATE clientes SET email='$email' WHERE id = '$idCliente'";
                break;
            case 'image':
                $updateEstatus =  "UPDATE clientesImagen SET imagen='$image' WHERE idCliente = '$idCliente'";
                break;
            default:
                $updateEstatus =  "UPDATE clientesImagen SET 
                                razonSocial='$razonSocial', 
                                rfc='$rfc', 
                                nombre='$nombre',
                                email='$email', 
                                imagen='$image'  
                                WHERE idCliente = '$idCliente'";
                break;
        }





        if ($conexion->query($updateEstatus) === TRUE) {
            $viajesActualizados =  mysqli_query($conexion, $updateEstatus);
            $rowActualizado = mysqli_fetch_array($viajesActualizados, MYSQLI_ASSOC);

            $payload = ["updateEstatus" => "Cliente Actualizado", "id" => $idCliente];
            //     $payload = ["Mensaje" => "Cliente Actualizado", "id" => $idCliente];






            respuesta(200, 200,  "Respuesta exitosa", $payload);
        } else {
            $payload = ["sql" => "Error: " . $payload . "<br>" . $conexion->error];
            respuesta(500, 500,  "Hay un error con el servidor. Llama a central Error-TAUPD", $payload);
        }
    }
} else {
    $payload = ["Faltantes" => $faltantes];
    respuesta(400, 400, "Hay un error con el servidor. Llama a central Error-TARE1", $payload);
}
