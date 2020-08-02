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
        respuesta(500, 500, "Hay un error con el servidor. Llama a central Error-LOBD1", []);
    } else {
        //configuracon db
        mysqli_query($conexion, "SET CHARACTER SET 'utf8'");
        mysqli_query($conexion, "SET SESSION collation_connection ='utf8_unicode_ci'");


 

       
        $consulta =  "SELECT * FROM viajes WHERE id='$idViaje'";
        $usuarios =  mysqli_query($conexion, $consulta);
        $row = mysqli_fetch_array($usuarios, MYSQLI_ASSOC);


       


        $consulta =  "SELECT * FROM tramos WHERE idviaje='$idViaje'";
        $selectTramos =  mysqli_query($conexion, $consulta);
        while ($row2 = $selectTramos->fetch_array(MYSQLI_ASSOC)) {
            $tramos[] = $row2;
        }
        $row["emisor_cliente"]='true';
        $row["tramos"]=$tramos;

        //Response
        if (empty($row)) {
            respuesta(200, 404, "Usuario o contraseña incorrecto", []);
        } else {
            respuesta(200, 200, "Respuesta exitosa", $row);
        }


    }
} else {
    $payload = ["Faltantes" => $faltantes];
    respuesta(400, 400, "Hay un error con el servidor. Llama a central Error-LORE1", $row);
}
