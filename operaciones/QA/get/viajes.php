<?PHP
//datos .env
include '../production.php';

//header
header('Access-Control-Allow-Origin: *');
header("Content-Type: application/json; charset=UTF-8");

//const 
$faltantes = [];
$fin = false;

//datos Request
$datos = json_decode(file_get_contents('php://input'), true);
$estatus = $datos["estatus"];

//funciones
function respuesta($codehttp, $code, $mensaje, $factload)
{
    http_response_code($codehttp);
    $dataFinal = [
        "headerResponse" => [
            "code" => $code,
            "mensaje" => $mensaje
        ],
        "payload" => $factload
    ];
    echo json_encode($dataFinal);
}

//Validacion de Datos
if ($estatus  == "") {
    array_push($faltantes, 'estatus');
}

if (empty($faltantes)) {
    //Conexion a base de datos
    $mysqli = mysqli_init();
    $conexion = mysqli_connect($_SESSION['HOST'], $_SESSION['USER'], $_SESSION['PASS'], $_SESSION['DBNAME']);
    //Validacion conexion con bd
    if (!$conexion) {
        respuesta(500, 500, "Hay un error con el servidor. Llama a central Error-CONECTION-DATA", []);
    } else {
        //configuracon db
        mysqli_query($conexion, "SET CHARACTER SET 'utf8'");
        mysqli_query($conexion, "SET SESSION collation_connection ='utf8_unicode_ci'");


        //Consulta viajes
        switch ($estatus) {
            case "Proceso":
                $consulta = "SELECT  id as idViaje, base, cliente, destino as destinoViaje, ruta, fecha_salida, fecha_carga, fecha_entrega, fecha_disponibilidad, unidad, operador,  distancia as distanciaViaje, tiempo_formato, redondo, valida_fechas, estatus as estatusViaje  FROM viajes where estatus in ('Gastos', 'En proceso cliente', 'En proceso', 'En trayecto' )";
                break;
            case "Gastos":
                $consulta = "SELECT  id as idViaje, base, cliente, destino as destinoViaje, ruta, fecha_salida, fecha_carga, fecha_entrega, fecha_disponibilidad, unidad, operador,  distancia as distanciaViaje, tiempo_formato, redondo, valida_fechas, estatus as estatusViaje  FROM viajes where estatus = 'Gastos'";
                break;
            case "En proceso cliente":
                $consulta = "SELECT  id as idViaje, base, cliente, destino as destinoViaje, ruta, fecha_salida, fecha_carga, fecha_entrega, fecha_disponibilidad, unidad, operador,  distancia as distanciaViaje, tiempo_formato, redondo, valida_fechas, estatus as estatusViaje  FROM viajes where estatus = 'En proceso cliente'";
                break;
            case "En proceso":
                $consulta = "SELECT  id as idViaje, base, cliente, destino as destinoViaje, ruta, fecha_salida, fecha_carga, fecha_entrega, fecha_disponibilidad, unidad, operador,  distancia as distanciaViaje, tiempo_formato, redondo, valida_fechas, estatus as estatusViaje  FROM viajes where estatus = 'En proceso'";
                break;
            case "En trayecto":
                $consulta = "SELECT  id as idViaje, base, cliente, destino as destinoViaje, ruta, fecha_salida, fecha_carga, fecha_entrega, fecha_disponibilidad, unidad, operador,  distancia as distanciaViaje, tiempo_formato, redondo, valida_fechas, estatus as estatusViaje  FROM viajes where estatus = 'En trayecto'";
                break;
            case "Entrega":
                $consulta = "SELECT  id as idViaje, base, cliente, destino as destinoViaje, ruta, fecha_salida, fecha_carga, fecha_entrega, fecha_disponibilidad, unidad, operador,  distancia as distanciaViaje, tiempo_formato, redondo, valida_fechas, estatus as estatusViaje  FROM viajes where estatus in ( 'Facturacion', 'En regreso')";
                break;
            case "Historial":
                $consulta = "SELECT  id as idViaje, base, cliente, destino as destinoViaje, ruta, fecha_salida, fecha_carga, fecha_entrega, fecha_disponibilidad, unidad, operador,  distancia as distanciaViaje, tiempo_formato, redondo, valida_fechas, estatus as estatusViaje FROM viajes where estatus in ( 'Cancelado', 'Finalizado') ORDER BY id DESC;";
                break;
            case "Todos":
                $consulta = "SELECT  id as idViaje, base, cliente, destino as destinoViaje, ruta, fecha_salida, fecha_carga, fecha_entrega, fecha_disponibilidad, unidad, operador,  distancia as distanciaViaje, tiempo_formato, redondo, valida_fechas, estatus as estatusViaje  FROM viajes where estatus in ( 'Gastos', 'En proceso cliente', 'En proceso', 'En trayecto', 'Entregado' , 'Cancelado',  'Finalizado' ) ORDER BY id DESC;";
                break;
        }

        $viajes = mysqli_query($conexion, $consulta) or die(respuesta(500, 500, "Hay un error con el servidor. Llama a central Error-Estatus no existe", []));
        while ($row = $viajes->fetch_array(MYSQLI_ASSOC)) {
            $tramosData = [];
            $costosVData = [];
            $baseDeOperaciones = $row["base"];
            $consultaBase = mysqli_query($conexion, "SELECT nombre FROM baseDeOperaciones where direccion = '$baseDeOperaciones'");
            $base = mysqli_fetch_array($consultaBase, MYSQLI_ASSOC);
            $row["base"] = $base["nombre"];
            $row["direccion_base"] = $baseDeOperaciones;
            $dateC = new DateTime($row["fecha_carga"]);
            $row["fecha_carga"] = $dateC->format('Y-m-d H:i');
            $dateE = new DateTime($row["fecha_entrega"]);
            $row["fecha_entrega"] = $dateE->format('Y-m-d H:i');
            $idViaje = $row["idViaje"];
            $row["precio"] = "9000";
            $row["gasto"] = "3000";
            $consultaTempOperaciones = mysqli_query($conexion, "SELECT confirmaViaje from tempOperacion WHERE idViaje = $idViaje");
            $tempOperacion = mysqli_fetch_array($consultaTempOperaciones, MYSQLI_ASSOC);
            $row["estatusAppOperador"] =  'Pendiente';
            $tramos = mysqli_query($conexion, "SELECT id as idTramo, tramo, fecha, destino, origen, entrega, tiempo_carga, tiempo, distancia as distanciaTramo, waypoints, observaciones, estatus as estatusTramo FROM tramos where idViaje = $idViaje");
            while ($row2 = $tramos->fetch_array(MYSQLI_ASSOC)) {
                $embarquesData = [];
                $costosTData = [];
                $idTramo = $row2["idTramo"];
                $embarques = mysqli_query($conexion, "SELECT id, numero, cajas, cajas_entregadas, cajas_rechazadas, estatus as estatusEmbarque  FROM embarques where idTramo =$idTramo");
                while ($row3 = $embarques->fetch_array(MYSQLI_ASSOC)) {
                    $embarquesData[] = $row3;
                }
                $row2["embarques"] = $embarquesData;
                $costosT = mysqli_query($conexion, "SELECT tipo, presupuesto, total, observacion, estatus, autoriza  FROM costos where idTramo =$idTramo");
                while ($row4 = $costosT->fetch_array(MYSQLI_ASSOC)) {
                    $costosTData[] = $row4;
                }
                $row2["costos"] = $costosTData;
                $tramosData[] = $row2;
                $row2["casetas"] = [];
            }
            $row["tramos"] = $tramosData;
            $costosV = mysqli_query($conexion, "SELECT tipo, presupuesto, total, observacion, estatus, autoriza  FROM costos where idViaje = $idViaje");
            while ($row5 = $costosV->fetch_array(MYSQLI_ASSOC)) {
                $costosVData[] = $row5;
            }
            $row["costos"] = $costosVData;
            $dataViajes[] = $row;
        }
        //Response
        if (empty($dataViajes)) {
            respuesta(200, 404, "No existen viajes ", []);
        } else {
            $payload = $dataViajes;
            respuesta(200, 200, "Respuesta exitosa", $payload);
        }
    }
} else {
    $payload = ["Faltantes" => $faltantes];
    respuesta(400, 400, "Hay un error con el servidor. Llama a central Error-VI-BAD-REQUEST", $payload);
}
