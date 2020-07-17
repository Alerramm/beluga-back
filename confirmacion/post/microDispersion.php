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
$idGasto = $datos["idGasto"];
$referencia = $datos["referencia"];
$emisor = $datos["emisor"];
$receptor = $datos["receptor"];
$estatus = $datos["estatus"];
$realiza = $datos["realiza"];
$metodoPago = $datos["metodoPago"];
$cobroCliente = $datos["cobroCliente"];
$iva = $datos["iva"];
$subtotal = $datos["subtotal"];
$total = $datos["total"];




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
if ($idGasto == "") {
    array_push($faltantes, 'idGasto');
}
if ($referencia == "") {
    array_push($faltantes, 'referencia');
}
if ($emisor == "") {
    array_push($faltantes, 'emisor');
}
if ($receptor == "") {
    array_push($faltantes, 'receptor');
}
if ($estatus == "") {
    array_push($faltantes, 'estatus');
}
if ($realiza == "") {
    array_push($faltantes, 'realiza');
}
if ($metodoPago == "") {
    array_push($faltantes, 'metodoPago');
}
if ($iva == "") {
    array_push($faltantes, 'iva');
}
if ($subtotal == "") {
    array_push($faltantes, 'subtotal');
}
if ($total == "") {
    array_push($faltantes, 'total');
}


if (empty($faltantes)) {



    $mysqli = mysqli_init();
    $conexion = mysqli_connect($_SESSION['HOST'], $_SESSION['USER'], $_SESSION['PASS'], $_SESSION['DBNAME']);
    //Validacion conexion con bd
        if (!$conexion) {
            respuesta(500, 500, "Hay un error con el servidor. Llama a central Error-CLBD1", []);
        } else {

            mysqli_query($conexion, "SET CHARACTER SET 'utf8'");
            mysqli_query($conexion, "SET SESSION collation_connection ='utf8_unicode_ci'");
    
            //Analisis de la informacion
            $consulta =  "SELECT * FROM gastos where id = '$idGasto'" ;
            $viajes =  mysqli_query($conexion, $consulta);
            $row = mysqli_fetch_array($viajes, MYSQLI_ASSOC);
    
            if (empty($row)) {
                respuesta(200, 404, "No hay registros con este ID de gastos" . $idGasto, []);
            } else {

                $montoAprobadoGastos = $row["montoAprobado"];
                $subtotalGastos = $row["subtotal"];
                $ivaGastos = $row["iva"];
                $totalGastos = $row["total"];
                $updateSubTotalGastos = $subtotalGastos + $subtotal;
                $updateivaGastos = $ivaGastos + $iva;
                $updateTotalGastos = $totalGastos + $total;
               $payloadGastosInsert [] = ["GastosInsert" => " Exito New Travel record created successfully " . $insert_id];
                $payloadGastosInsert [] = ["Operacion SubTotales" => " Total de la suma de Subtotales " . $updateSubTotalGastos];
               $payloadGastosInsert [] = ["Operacion Iva" => " Total de la suma de Iva " . $updateivaGastos];
                $payloadGastosInsert [] = ["Operacion Total" => " Total de la suma de Total " . $updateTotalGastos];

                if($updateTotalGastos > $montoAprobadoGastos){
                    respuesta(200, 404, " Esta dispersion supera el monto maximo aprobado, monto Aprobado: " . $montoAprobadoGastos, $payloadGastosInsert);
                }else{
                    $insertDesgloseAuth2 =  "INSERT INTO dispersiones(idGasto,referencia,emisor,receptor,estatus,realiza,metodoPago,cobroCliente,iva,subtotal,total	) VALUES ('$idGasto','$referencia','$emisor','$receptor','$estatus','$realiza','$metodoPago','$cobroCliente','$iva','$subtotal','$total' )";

                    
                    if ($conexion->query($insertDesgloseAuth2) === TRUE) { 

                        $last_id = $conexion->insert_id;   

                        $payloadGastosInsert [] = ["SQL" => " Dispersion Insertada con Exito" . $last_id];
    
                            if($updateTotalGastos == $montoAprobadoGastos){
                                $estatus2 = "Completo" ;
    
    
                            }elseif ($updateTotalGastos < $montoAprobadoGastos){
                                $estatus2 = "Parcial" ;
                            }
                           
                            
                            $updateEstatus =  "UPDATE gastos SET subtotal='$updateSubTotalGastos', iva='$updateivaGastos', total= '$updateTotalGastos', estatus = '$estatus2' WHERE id = '$idGasto'" ;

                                            



                            if ($conexion->query($updateEstatus) === TRUE) {
                                $viajesActualizados =  mysqli_query($conexion, $consulta);
                                $rowActualizado = mysqli_fetch_array($viajesActualizados, MYSQLI_ASSOC);   
                                $payloadGastosInsert = ["sql" => "Exito Update record successfully", "id" => $rowActualizado["subtotal,iva,total,estatus"]];
                                respuesta(200, 200, "Success", $payloadGastosInsert);
                            }else{
                                respuesta(400, 400, "Error al Actualizar", $payloadGastosInsert);
                            }       

                           
                    }else{
                        respuesta(400, 400, "Error al Insertar Registro", []);             
                    }

                    


                 }  


            }


 } 

}else{
    $payload = ["Faltantes" => $faltantes];
    respuesta(400, 400, "Hay un error con el servidor. Llama a central Error-TARE1", $payload);
}