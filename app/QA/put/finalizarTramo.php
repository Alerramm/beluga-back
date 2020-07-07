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
$idTramo = $datos["idTramo"];
$embarques = $datos["embarques"];
$tramoFinal = $datos["tramoFinal"];
$tramoInicial = $datos["tramoInicial"];
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
if ($idViaje == "") {
    array_push($faltantes, 'idViaje');
}
if (!array_key_exists('embarques', $datos)) {
    array_push($faltantes, 'embarques');
}

if (!array_key_exists('tramoFinal', $datos)) {
    array_push($faltantes, 'tramoFinal');
}
if ($fecha == "") {
    array_push($faltantes, 'fecha');
}

if (empty($faltantes)) {
    //Conexion a base de datos
    $mysqli = mysqli_init();
    $conexion = mysqli_connect($_SESSION['HOST'], $_SESSION['USER'], $_SESSION['PASS'], $_SESSION['DBNAME']);
    //Validacion conexion con bd
    if (!$conexion) {
        respuesta(500, 500, "Hay un error con el servidor. Llama a central Error-FTBD1", []);
    } else {
        //configuracon db
        mysqli_query($conexion, "SET CHARACTER SET 'utf8'");
        mysqli_query($conexion, "SET SESSION collation_connection ='utf8_unicode_ci'");

        //Analisis de la informacion

        $consulta =  "SELECT id, estatus  FROM tramos where id = $idTramo";
        $tramo =  mysqli_query($conexion, $consulta);
        $row = mysqli_fetch_array($tramo, MYSQLI_ASSOC);

        if (empty($row) && !$tramoInicial) {
            respuesta(200, 404, "Hay un error con el servidor. Llama a central. Error-FTTRI" . $idTramo, []);
        } else {
            if ($tramoInicial) {
                //Update
                $updateTramo =  "UPDATE tramos SET fecha='$fecha' WHERE idViaje = $idViaje LIMIT 1";
                if ($conexion->query($updateTramo) === TRUE) {
                    //Update
                    $updateViaje1 =  "UPDATE viajes SET fecha_carga='$fecha', estatus = 'En proceso' WHERE id = $idViaje";
                    if ($conexion->query($updateViaje1) === TRUE) {
                        $tramoActualizado =  mysqli_query($conexion, $consulta);
                        $rowActualizado = mysqli_fetch_array($tramoActualizado, MYSQLI_ASSOC);
                        $viajeActualizado =  mysqli_query($conexion, $consulta);
                        $rowActualizadoViaje = mysqli_fetch_array($viajeActualizado, MYSQLI_ASSOC);
                        $payload = ["sql" => "Exito Update record successfully", "idTramo" => $rowActualizado["id"], "estatus" => $rowActualizado["estatus"], "cajas" => $rowActualizado["cajas"], "viajeFechaCarga" => $rowActualizadoViaje["fecha_carga"]];
                        respuesta(200, 200,  "Respuesta exitosa", $payload);
                    } else {
                        $payload = ["sql" => "Error: " . $updateViaje1 . "<br>" . $conexion->error];
                        respuesta(500, 500,  "Hay un error con el servidor. Llama a central Error-FTUPD", $payload);
                    }
                } else {
                    $payload = ["sql" => "Error: " . $updateTramo . "<br>" . $conexion->error];
                    respuesta(500, 500,  "Hay un error con el servidor. Llama a central Error-FTUPD", $payload);
                }
            } else {
                if ($tramoFinal) {
                    //Update
                    $updateEstatus =  "UPDATE tramos SET estatus='Finalizado' WHERE id = $idTramo;";
                    if ($conexion->query($updateEstatus) === TRUE) {
                        $tramoActualizado =  mysqli_query($conexion, $consulta);
                        $rowActualizado = mysqli_fetch_array($tramoActualizado, MYSQLI_ASSOC);
                        //Update
                        $updateViajeEstatus =  "UPDATE viajes SET estatus='Facturacion',  fecha_disponibilidad='$fecha'  WHERE id = $idViaje";
                        if ($conexion->query($updateViajeEstatus) === TRUE) {
                            $payload = ["sql" => "Exito Update record successfully", "idTramo" => $rowActualizado["id"], "estatus" => $rowActualizado["estatus"], "cajas" => $rowActualizado["cajas"], "viajeEstatus" => "Finalizado"];

                            respuesta(200, 200,  "Respuesta exitosa", $payload);
                        } else {
                            $payload = ["sql" => "Error: " . $updateViajeEstatus . "<br>" . $conexion->error];
                            respuesta(500, 500,  "Hay un error con el servidor. Llama a central Error-FTUPD", $payload);
                        }
                    } else {
                        $payload = ["sql" => "Error: " . $updateEstatus . "<br>" . $conexion->error];
                        respuesta(500, 500,  "Hay un error con el servidor. Llama a central Error-FTUPD", $payload);
                    }
                } else {
                    $consultaTramoEntregaFinal = "SELECT COUNT(1) FROM tramos where idViaje = $idViaje and estatus = 'Pendiente' and
                    (Select COUNT(1) from viajes where id = $idViaje and redondo = true) > 0;";
                    $tramoEntregaFinal =  mysqli_query($conexion, $consultaTramoEntregaFinal);
                    $tramoEntregaFinalR = mysqli_fetch_array($tramoEntregaFinal, MYSQLI_ASSOC);
                    if ($tramoEntregaFinalR["COUNT(1)"] == 2) {
                        $updateEstatusViaje =  "UPDATE viajes SET estatus='En regreso' WHERE id = $idViaje;";
                        if ($conexion->query($updateEstatusViaje) === TRUE) {
                            $payloadUpdateViaje = ["TramoEntregaFinal" => $tramoEntregaFinalR];
                        } else {
                            $payloadUpdateViaje = ["TramoEntregaFinal" => "Error: " . $updateEstatusViaje . "<br>" . $conexion->error];
                        }
                    }

                    $tramoFin = true;
                    foreach ($embarques as &$embarque) {
                        $numEmbarque = $embarque["numEmbarque"];
                        $cajas = $embarque["cajas"];
                        $motivo = $embarque["motivo"];
                        if ($embarque["estatus"] == "Entregado") {
                            //Update
                            $consultaEmbarqueEntregado =  "SELECT * FROM embarques where numero = '$numEmbarque' and idTramo = $idTramo;";
                            $embarqueSQLEntregado =  mysqli_query($conexion, $consultaEmbarqueEntregado);
                            $rowESQL = mysqli_fetch_array($embarqueSQLEntregado, MYSQLI_ASSOC);
                            if ($cajas == $rowESQL["cajas"]) {
                                $updateEmbarque =  "UPDATE embarques SET cajas_entregadas = '$cajas', estatus='Finalizado' WHERE numero = '$numEmbarque';";
                                if ($conexion->query($updateEmbarque) === TRUE) {

                                    $tramos[] = ["sql" => "Exito Update record successfully", "cajas" => $rowESQL["cajas"], "embarque" => $rowESQL["numero"], "estatus" => "Finalizado"];
                                } else {
                                    $tramos[] = ["sql" => "Error: " . $updateEmbarque . "<br>" . $conexion->error];
                                }
                            } else {
                                $tramoFin = false;
                                $updateEmbarque =  "UPDATE embarques SET cajas_entregadas = '$cajas', estatus='Bloqueado' WHERE numero = '$numEmbarque';";
                                if ($conexion->query($updateEmbarque) === TRUE) {

                                    $tramos[] = ["sql" => "Exito Update record successfully", "cajas" => $rowESQL["cajas"], "embarque" => $rowESQL["numero"], "estatus" => "Bloqueado"];
                                } else {
                                    $tramos[] = ["sql" => "Error: " . $updateEmbarque . "<br>" . $conexion->error];
                                }
                            }
                        }
                        if ($embarque["estatus"] == "Rechazado") {
                            $consultaEmbarque =  "SELECT * FROM embarques where numero = '$numEmbarque' and idTramo = $idTramo;";
                            $embarqueSQL =  mysqli_query($conexion, $consultaEmbarque);
                            $rowESQL = mysqli_fetch_array($embarqueSQL, MYSQLI_ASSOC);
                            $cajas_entregadas = $cajas - $rowESQL["cajas"];
                            $tramoFin = false;
                            //Update
                            $updateEmbarque =  "UPDATE embarques SET estatus='Rechazado', cajas_entregadas = '$cajas_entregadas',  cajas_rechazadas='$cajas' WHERE numero = '$numEmbarque';";
                            if ($conexion->query($updateEmbarque) === TRUE) {

                                $bitacora = "INSERT INTO bitacora(idEmbarque, tipo, motivo, fecha) VALUES ('$numEmbarque','Rechazo','$motivo','$fecha')";
                                if ($conexion->query($bitacora) === TRUE) {

                                    $tramos[] = ["sql" => "Exito Update record successfully", "embarque" => $rowESQL["embarque"], "estatus" => "Rechazado"];
                                } else {
                                    $tramos[] = ["sql" => "Error: " . $updateEmbarque . "<br>" . $conexion->error];
                                }
                            } else {
                                $tramos[] = ["sql" => "Error: " . $updateEmbarque . "<br>" . $conexion->error];
                            }
                        }
                    }
                    if (!$tramoFin) {
                        $updateEstatus =  "UPDATE tramos SET estatus='Bloqueado' WHERE id = $idTramo;";
                        if ($conexion->query($updateEstatus) === TRUE) {

                            $payload = ["TramoEntregaFinal" => $tramoEntregaFinalR, "sql" => "Exito Update record successfully", "idTramo" => $rowActualizado["id"], "tramos" => $tramos];

                            respuesta(400, 404, "Hubo un rechazo. Llama a central para poder continuar con tu viaje.", $payload);
                        } else {
                            $payload = ["TramoEntregaFinal" => $tramoEntregaFinalR, "sql" => "Error: " . $updateEstatus . "<br>" . $conexion->error];
                            respuesta(500, 500,  "Hay un error con el servidor. Llama a central Error-FTUPD", $payload);
                        }
                    } else {
                        $updateEstatus =  "UPDATE tramos SET estatus='Finalizado' WHERE id = $idTramo;";
                        if ($conexion->query($updateEstatus) === TRUE) {

                            $payload = ["TramoEntregaFinal" => $tramoEntregaFinalR, "sql" => "Exito Update record successfully", "idTramo" => $rowActualizado["id"], "tramos" => $tramos];

                            respuesta(200, 200, "Respuesta exitosa", $payload);
                        } else {
                            $payload = ["TramoEntregaFinal" => $tramoEntregaFinalR, "sql" => "Error: " . $updateEstatus . "<br>" . $conexion->error];
                            respuesta(500, 500,  "Hay un error con el servidor. Llama a central Error-FTUPD", $payload);
                        }
                    }
                }
            }
        }
    }
} else {
    $payload = ["Faltantes" => $faltantes];
    respuesta(400, 400, "Hay un error con el servidor. Llama a central Error-FTRE1", $payload);
}
