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
$devoluciones = $datos["devoluciones"];
$idTramo = $datos["idTramo"];
$fecha = $datos["fecha"];

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
if ($fecha == "") {
    array_push($faltantes, 'fecha');
}

if (!array_key_exists('devoluciones', $datos)) {
    array_push($faltantes, 'devoluciones');
} else {
    $cont = 1;
    foreach ($devoluciones as &$devolucion) {
        if ($devolucion["embarque"] == "") {
            array_push($faltantes, 'embarque de la devolucion' + $cont);
        }
        if ($devolucion["cajas"] == "") {
            array_push($faltantes, 'cajas de la devolucion' + $cont);
        }
        if ($devolucion["motivo"] == "") {
            array_push($faltantes, 'motivo de la devolucion' + $cont);
        }
        $cont = $cont + 1;
    }
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
        //Update
        $estatusResponse = 200;
        foreach ($devoluciones as &$devolucion) {
            if ($estatusResponse === 200) {
                $cajas = $devolucion["cajas"];
                $embarque  = $devolucion["embarque"];
                $motivo  = $devolucion["motivo"];
                $updateEstatus =  "UPDATE embarques SET cajas_rechazadas='$cajas', estatus = 'Devolucion', idTramoDevolucion = '$idTramo' WHERE numero = $embarque;";
                if ($conexion->query($updateEstatus) === TRUE) {
                    $updateEstatusBitacora =  "INSERT INTO bitacora(idEmbarque, tipo, motivo, fecha) VALUES ('$embarque','Devolucion','$motivo','$fecha')";
                    if ($conexion->query($updateEstatusBitacora) === TRUE) {
                        $payload[] = ["sql" => "Exito Update record successfully", "sqlBitcora" => "Exito Insert record successfully"];
                        $estatusResponse = 200;
                        $mensaje = "Respuesta exitosa";
                    } else {
                        $payload[] = ["sql" => "Error: " . $updateEstatusBitacora . "<br>" . $conexion->error];
                        $estatusResponse = 500;
                        $mensaje = "Hay un error con el servidor. Llama a central Error-TAUIN";
                    }
                } else {
                    $payload[] = ["sql" => "Error: " . $updateEstatus . "<br>" . $conexion->error];
                    $estatusResponse = 500;
                    $mensaje = "Hay un error con el servidor. Llama a central Error-TAUPD";
                }
            }
        }
        respuesta($estatusResponse, $estatusResponse, $mensaje, $payload);
    }
} else {
    $payload = ["Faltantes" => $faltantes];
    respuesta(400, 400, "Hay un error con el servidor. Llama a central Error-TARE1", $payload);
}
