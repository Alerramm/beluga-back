<?PHP
//datos .env
include '../production.php';

//header
header('Access-Control-Allow-Origin: *');
header("Content-Type: application/json; charset=UTF-8");

//const 
$faltantes = [];
$fe1=date("Y-m-d");
//datos Request
$datos = json_decode(file_get_contents('php://input'), true);
$tipo = $datos["tipo"];
$subtotal = $datos["subtotal"];
$iva = $datos["iva"];
$total = $datos["total"];
$idViaje = $datos["idViaje"];

$idTramo = $datos["idTramo"];
$observacion = $datos["observacion"];
$estatus = $datos["estatus"];
$comprobado = $datos["comprobado"];
$cobroCliente = $datos["cobroCliente"];
$autoriza = $datos["autoriza"];
$fechaAutorizacion = $datos["fechaAutorizacion"];
$montoAprobado = $datos["montoAprobado"];
$montoSolicitado = $datos["montoSolicitado"];




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
if ($tipo == "") {
    array_push($faltantes, 'tipo');
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
        $consulta =  "SELECT * FROM gastos where idViaje = '$idViaje' AND tipo ='$tipo'";
        $viajes =  mysqli_query($conexion, $consulta);
        $row = mysqli_fetch_array($viajes, MYSQLI_ASSOC);

        if (empty($row)) {
            respuesta(200, 404, "No hay registros con este ID de viaje O tipo de gasto" . $id, []);
        } else {
            //Update
            $updateEstatus =  "UPDATE gastos SET 
                                                observacion = '$observacion',
                                                estatus = '$estatus',
                                                cobroCliente = '$cobroCliente',
                                                montoSolicitado = '$montoSolicitado'
                                                
                                                WHERE idViaje = $idViaje AND tipo ='$tipo'";



            if ($conexion->query($updateEstatus) === TRUE) {
                $viajesActualizados =  mysqli_query($conexion, $consulta);
                $rowActualizado = mysqli_fetch_array($viajesActualizados, MYSQLI_ASSOC);
            
                $consulta =  "SELECT destino,operador FROM viajes where id = '$idViaje' " ;
                $consultaresponse =  mysqli_query($conexion, $consulta);
                $row2 = mysqli_fetch_array($consultaresponse, MYSQLI_ASSOC);
                $destin = $row2["destino"];
                $oper = $row2["operador"];
                $secobra='';
                if($cobroCliente > 0){$secobra ='Si';  }
          
                $preAux='PRE'.$tipo;
                
             $insertDesgloseAuth ="INSERT INTO desgloseGastosAut(fecha,operador,destino,viaje,idViaje,".$tipo.",observacion,status,solicita,".$preAux.",seCobra)
                 VALUES ('$fe1', '$oper','$destin','$idViaje','$idViaje','$montoSolicitado','$observacion','Adicional','$autoriza','$cobroCliente','$secobra')";
             
               $conexion->query($insertDesgloseAuth); 
          

                $payload = ["sql" => "Exito Update record successfully", "id" => $row["id"]];

                respuesta(200, 200,  "Respuesta exitosa", $payload);
            } else {
                $payload = ["sql" => "Error: " . $updateEstatus . "<br>" . $conexion->error];
                respuesta(500, 500,  "Hay un error con el servidor. Llama a central Error-TAUPD", $payload);
            }
        }
    }
} else {
    $payload = ["Faltantes" => $faltantes];
    respuesta(400, 400, "Hay un error con el servidor. Llama a central Error-TARE1", $payload);
}
