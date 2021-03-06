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
if ($idViaje  == "") {
    array_push($faltantes, 'idViaje');
}

if (empty($faltantes)) {
    //Conexion a base de datos
    $mysqli = mysqli_init();
    $conexion = mysqli_connect($_SESSION['HOST'], $_SESSION['USER'], $_SESSION['PASS'], $_SESSION['DBNAME']);
    //Validacion conexion con bd
    if (!$conexion) {
        respuesta(500, 500, "Hay un error con el servidor. Llama a central Error-TRBD1", []);
    } else {
        //configuracon db
        mysqli_query($conexion, "SET CHARACTER SET 'utf8'");
        mysqli_query($conexion, "SET SESSION collation_connection ='utf8_unicode_ci'");

        //Consulta viajes
        $consulta = "SELECT *  FROM viajes where id = '$idViaje'";
        $viaje = mysqli_query($conexion, $consulta);
        while ($row = $viaje->fetch_array(MYSQLI_ASSOC)) {
            $dataViaje[] = $row;
        }

        //Response
        if (empty($dataViaje)) {
            respuesta(200, 404, "No tienes viajes asignados", []);
        } else {
            if (count($dataViaje) > 1) {
                respuesta(200, 404, "Hay un error con el servidor. Llama a central. Error-TREVI", $dataViaje);
            } else {
                $dataViaje = $dataViaje[0];
                switch ($dataViaje["estatus"]) {
                    case "Pendiente":
                        respuesta(200, 404, "El viaje esta en espera de confirmacion por parte de central", []);
                        break;
                        break;
                    case "Confirmado":
                        //Consulta tempOperacion
                        $consulta = "SELECT confirmaViaje  FROM tempOperacion where idViaje = '$idViaje'";
                        $tempViaje = mysqli_query($conexion, $consulta);
                        while ($row = $tempViaje->fetch_array(MYSQLI_ASSOC)) {
                            $dataTempViaje[] = $row;
                        }
                        if (empty($dataTempViaje)) {
                            respuesta(200, 404, "Hay un error con el servidor. Llama a central. Error-TRTOD" . $dataViaje["id"], []);
                        } else {
                            if (count($dataTempViaje) > 1) {
                                respuesta(200, 404, "Hay un error con el servidor. Llama a central. Error-TRETO", $dataTempViaje);
                            } else {
                                $dataTempViaje = $dataTempViaje[0];
                                //const
                                $direccionDataViaje = $dataViaje["base"];

                                //consulta baseDeOperaciones
                                $consultaBase = mysqli_query($conexion, "SELECT nombre, direccion FROM baseDeOperaciones where direccion = '$direccionDataViaje'");
                                $base = mysqli_fetch_array($consultaBase, MYSQLI_ASSOC);
                                if (empty($base)) {
                                    respuesta(200, 404, "Hay un error con el servidor. Llama a central. Error-TRBOD" . $dataViaje["id"], []);
                                } else {
                                    //const
                                    $nombreCliente = $dataViaje["cliente"];
                                    //consulta direccionCliente
                                    $consultaDireccionCliente = mysqli_query($conexion, "SELECT domCarga FROM clientes where nombre = '$nombreCliente'");
                                    $direccionCliente = mysqli_fetch_array($consultaDireccionCliente, MYSQLI_ASSOC);
                                    if (empty($direccionCliente)) {
                                        respuesta(200, 404, "Hay un error con el servidor. Llama a central. Error-TRCLD" . $nombreCliente, []);
                                    } else {
                                        //const
                                        $idV = $dataViaje["id"];
                                        //consulta direccionCliente
                                        $consultaTramosViaje = mysqli_query($conexion, "SELECT entrega as destino FROM tramos where idViaje = $idV");
                                        while ($row = $consultaTramosViaje->fetch_array(MYSQLI_ASSOC)) {
                                            $tramosViaje[] = $row;
                                        }
                                        array_shift($tramosViaje);
                                        array_splice($tramosViaje, count($tramosViaje) - 1);
                                        if (empty($tramosViaje)) {
                                            respuesta(200, 404, "Hay un error con el servidor. Llama a central. Error-TRTRD" . $idV, []);
                                        } else {
                                            $payload = [
                                                "id" => $dataViaje["id"],
                                                "base" => $base["nombre"],
                                                "direccionBase" => $base["direccion"],
                                                "cliente" => $dataViaje["cliente"],
                                                "direccionCliente" => $direccionCliente["domCarga"],
                                                "fechaCarga" => $dataViaje["fecha_carga"],
                                                "unidad" => $dataViaje["unidad"],
                                                "ruta" => $dataViaje["ruta"],
                                                "destino" => $dataViaje["destino"],
                                                "estatus" => $dataViaje["estatus"],
                                                "confirmaViaje" => $dataTempViaje["confirmaViaje"],
                                                "destinos" => $tramosViaje,
                                            ];
                                            respuesta(200, 200, "Respuesta exitosa", $payload);
                                        }
                                    }
                                }
                            }
                        }
                        break;
                    case "Gastos":
                        $payload = [
                            "idViaje" => $dataViaje["id"],
                            "cliente" => $dataViaje["cliente"],
                            "fechaCarga" => $dataViaje["fecha_carga"],
                            "unidad" => $dataViaje["unidad"],
                            "ruta" => $dataViaje["ruta"],
                            "destino" => $dataViaje["destino"],
                            "estatus" => $dataViaje["estatus"],
                        ];
                        respuesta(200, 200, "Respuesta exitosa", $payload);
                        break;
                    case "En proceso cliente":
                        $payload = [
                            "idViaje" => $dataViaje["id"],
                            "cliente" => $dataViaje["cliente"],
                            "fechaCarga" => $dataViaje["fecha_carga"],
                            "unidad" => $dataViaje["unidad"],
                            "ruta" => $dataViaje["ruta"],
                            "destino" => $dataViaje["destino"],
                            "estatus" => $dataViaje["estatus"],
                        ];
                        respuesta(200, 200, "Respuesta exitosa", $payload);
                        break;
                    case "En proceso":
                        //const 
                        $id = $dataViaje["id"];
                        //Consulta tramos
                        $consultaTramos = "SELECT id, tramo, fecha, entrega, observaciones, destino FROM tramos where idViaje = $id";
                        $tramos = mysqli_query($conexion, $consultaTramos);
                        while ($row = $tramos->fetch_array(MYSQLI_ASSOC)) {
                            $dataTramos[] = $row;
                        }
                        if (empty($dataTramos)) {
                            respuesta(200, 404, "Hay un error con el servidor. Llama a central. Error-TRTRD" . $id, []);
                        } else {
                            foreach ($dataTramos as &$row) {
                                if ($row["tramo"] != 1 && $row["tramo"] != count($dataTramos)) {
                                    $tempTotalTramos = count($dataTramos) - 2;
                                    $tramosResponse[] = [
                                        "idTramo" => $row["id"],
                                        "numeroTramo" => $row["tramo"] - 1 . "-" . $tempTotalTramos,
                                        "entregaTramo" =>  $row["entrega"],
                                        "destinoTramo" => $row["destino"],
                                        "fechaTramo" =>  $row["fecha"],
                                        "observacionesTramo" => $row["observaciones"],
                                    ];
                                }
                            }
                            $payload = [
                                "idViaje" => $dataViaje["id"],
                                "idTramo" => $dataTramos[0]["id"],
                                "estatusViaje" => $dataViaje["estatus"],
                                "cliente" => $dataViaje["cliente"],
                                "fechaCarga" => $dataViaje["fecha_carga"],
                                "unidad" => $dataViaje["unidad"],
                                "ruta" => $dataViaje["ruta"],
                                "destino" => $dataViaje["destino"],
                                "tramos" => $tramosResponse,
                            ];
                            respuesta(200, 200, "Respuesta exitosa", $payload);
                        }
                        break;
                    case "En trayecto":
                        //const 
                        $id = $dataViaje["id"];
                        //Consulta tramos
                        $consultaTramos = "SELECT id, tramo, fecha, entrega, distancia, destino, observaciones, estatus FROM tramos where idViaje = $id and estatus in ('Pendiente','Bloqueado')";
                        $tramos = mysqli_query($conexion, $consultaTramos);
                        while ($row = $tramos->fetch_array(MYSQLI_ASSOC)) {
                            $dataTramos[] = $row;
                        }
                        if (empty($dataTramos)) {
                            respuesta(200, 404, "Hay un error con el servidor. Llama a central. Error-TRTRD" . $id, []);
                        } else {
                            if (count($dataTramos) > 1) {
                                $idTramo = $dataTramos[0]["id"];

                                $consultaEmbarques = "SELECT * FROM embarques where idTramo = $idTramo";
                                $embarqueSql = mysqli_query($conexion, $consultaEmbarques);
                                while ($row = $embarqueSql->fetch_array(MYSQLI_ASSOC)) {
                                    $embarques[] = $row;
                                }
                                $payload = [
                                    "idViaje" => $dataViaje["id"],
                                    "estatusViaje" => $dataViaje["estatus"],
                                    "idTramo" => $idTramo,
                                    "numeroTramo" => $dataTramos[0]["tramo"],
                                    "entregaTramo" => $dataTramos[0]["entrega"],
                                    "destinoTramo" => $dataTramos[0]["destino"],
                                    "kmTramo" => $dataTramos[0]["distancia"],
                                    "fechaTramo" =>  $dataTramos[0]["fecha"],
                                    "observacionesTramo" => $dataTramos[0]["observaciones"],
                                    "estatusTramo" => $dataTramos[0]["estatus"],
                                    "embarques" =>  $embarques,
                                    "tramoFinal" => false,
                                ];
                                respuesta(200, 200, "Respuesta exitosa", $payload);
                            } else {
                                $payload = [
                                    "idViaje" => $dataViaje["id"],
                                    "estatusViaje" => $dataViaje["estatus"],
                                    "idTramo" => $dataTramos[0]["id"],
                                    "numeroTramo" => $dataTramos[0]["tramo"],
                                    "entregaTramo" => $dataTramos[0]["entrega"],
                                    "destinoTramo" => $dataTramos[0]["destino"],
                                    "kmTramo" => $dataTramos[0]["distancia"],
                                    "fechaTramo" =>  $dataTramos[0]["fecha"],
                                    "observacionesTramo" => $dataTramos[0]["observaciones"],
                                    "estatusTramo" => $dataTramos[0]["estatus"],
                                    "tramoFinal" => true,
                                ];
                                respuesta(200, 200, "Respuesta exitosa", $payload);
                            }
                        }
                        break;
                    case "En regreso":
                        //const 
                        $id = $dataViaje["id"];
                        //Consulta tramos
                        $consultaTramos = "SELECT id, tramo, fecha, entrega, distancia, destino, observaciones, cajas, embarque, estatus FROM tramos where idViaje = $id and estatus in ('Pendiente','Bloqueado')";
                        $tramos = mysqli_query($conexion, $consultaTramos);
                        while ($row = $tramos->fetch_array(MYSQLI_ASSOC)) {
                            $dataTramos[] = $row;
                        }
                        if (empty($dataTramos)) {
                            respuesta(200, 404, "Hay un error con el servidor. Llama a central. Error-TRTRD" . $id, []);
                        } else {
                            if (count($dataTramos) > 1) {
                                $idTramo = $dataTramos[0]["id"];

                                $consultaEmbarques = "SELECT * FROM embarques where idTramo = $idTramo";
                                $embarqueSql = mysqli_query($conexion, $consultaEmbarques);
                                while ($row = $embarqueSql->fetch_array(MYSQLI_ASSOC)) {
                                    $embarques[] = $row;
                                }
                                $payload = [
                                    "idViaje" => $dataViaje["id"],
                                    "estatusViaje" => $dataViaje["estatus"],
                                    "idTramo" => $idTramo,
                                    "numeroTramo" => $dataTramos[0]["tramo"],
                                    "entregaTramo" => $dataTramos[0]["entrega"],
                                    "destinoTramo" => $dataTramos[0]["destino"],
                                    "kmTramo" => $dataTramos[0]["distancia"],
                                    "fechaTramo" =>  $dataTramos[0]["fecha"],
                                    "observacionesTramo" => $dataTramos[0]["observaciones"],
                                    "estatusTramo" => $dataTramos[0]["estatus"],
                                    "embarques" => $embarques,
                                    "tramoFinal" => false,
                                ];
                                respuesta(200, 200, "Respuesta exitosa", $payload);
                            } else {
                                $payload = [
                                    "idViaje" => $dataViaje["id"],
                                    "estatusViaje" => $dataViaje["estatus"],
                                    "idTramo" => $dataTramos[0]["id"],
                                    "numeroTramo" => $dataTramos[0]["tramo"],
                                    "entregaTramo" => $dataTramos[0]["entrega"],
                                    "destinoTramo" => $dataTramos[0]["destino"],
                                    "kmTramo" => $dataTramos[0]["distancia"],
                                    "fechaTramo" =>  $dataTramos[0]["fecha"],
                                    "observacionesTramo" => $dataTramos[0]["observaciones"],
                                    "estatusTramo" => $dataTramos[0]["estatus"],
                                    "tramoFinal" => true,
                                ];
                                respuesta(200, 200, "Respuesta exitosa", $payload);
                            }
                        }
                        break;
                    default:
                        respuesta(200, 404, "Hay un error con el servidor. Llama a central. Error-TRVSI" . $dataViaje["id"], []);
                }
            }
        }
    }
} else {
    $payload = ["Faltantes" => $faltantes];
    respuesta(400, 400, "Hay un error con el servidor. Llama a central Error-TRRE1", $payload);
}
