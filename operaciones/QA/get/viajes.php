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


function consulta($conexion, $consulta)
{
    //Consulta 
    $query = mysqli_query($conexion, $consulta);
    $row_cnt = $query->num_rows;
    if ($row_cnt > 0) {
        while ($row = $query->fetch_array(MYSQLI_ASSOC)) {
            $row["key"] = $row["id"];
            $respuesta[] = $row;
        }
    } else {
        $respuesta = [];
    }

    return $respuesta;
}

function consultaEmbarques($conexion, $consulta, $row)
{
    //Consulta 
    $numRechazos = 0;
    $query = mysqli_query($conexion, $consulta);
    $row_cntEmbarque = $query->num_rows;
    if ($row_cntEmbarque > 0) {
        while ($rowE = $query->fetch_array(MYSQLI_ASSOC)) {
            $rowE["key"] = $rowE["id"];
            if ($rowE["cajas_rechazadas"] > 0) {
                $numRechazos = $numRechazos + 1;
                $rechazos = consulta($conexion, "SELECT b.*, e.cajas_rechazadas, e.estatus, e.idTramo as idEmbarqueTramo FROM bitacora b INNER JOIN embarques e ON b.idEmbarque = e.id WHERE b.idEmbarque = " . $rowE["id"] . " AND b.tipo = 'Rechazo'");
            }
            $respuesta[] = $rowE;
        }
        $row["numRechazos"] = $numRechazos;
        $row["rechazos"] = $rechazos;
        $row["embarques"] = $respuesta;
    } else {
        $row["numRechazos"] = 0;
        $row["embarques"] = [];
        $row["rechazos"] = [];
    }

    return $row;
}

function consultaTramos($conexion, $consulta)
{
    //Consulta 
    $query = mysqli_query($conexion, $consulta);
    $row_cnt = $query->num_rows;
    if ($row_cnt > 0) {
        while ($row = $query->fetch_array(MYSQLI_ASSOC)) {
            $idTramo = $row["idTramo"];
            $row["key"] = $row["id"];
            $row["distanciaTramo"] =  round($row["distanciaTramo"]);
            $row["tiempo"] =  round($row["tiempo"] / 60 / 60);
            $dateT = new DateTime($row["fecha"]);
            $row["fecha"] = $dateT->format('Y-m-d H:i');
            $row["casetas"] = consulta($conexion, "SELECT * from casetas WHERE idTramo = $idTramo");
            $row = consultaEmbarques($conexion, "SELECT id, numero, cajas, cajas_entregadas, cajas_rechazadas, estatus as estatusEmbarque  FROM embarques where idTramo = $idTramo", $row);
            $row["incidencias"] = consulta($conexion, "SELECT * FROM incidencias WHERE idTramo = " . $row["idTramo"]);
            if (empty($row["incidencias"])) {
                $row["numIncidencias"] = 0;
            } else {
                $row["numIncidencias"] = count($row["incidencias"]);
            }
            $respuesta[] = $row;
        }
    } else {
        $respuesta = [];
    }

    return $respuesta;
}

$gasto = 0;
$presupuesto = 0;

function consultaGastos($conexion, $consulta)
{
    global $gasto, $presupuesto;
    $gasto = 0;
    $presupuesto = 0;
    //Consulta 
    $query = mysqli_query($conexion, $consulta);
    $row_cnt = $query->num_rows;
    if ($row_cnt > 0) {
        while ($row = $query->fetch_array(MYSQLI_ASSOC)) {
            $gasto = $gasto + $row["total"];
            $presupuesto = $presupuesto + $row["presupuesto"];
            $row["key"] = $row["id"];
            $idGasto = $row["id"];
            $row["dispersiones"] = consulta($conexion, "SELECT * FROM dispersiones WHERE idGasto = $idGasto");
            $respuesta[] = $row;
        }
    } else {
        $respuesta = [];
    }

    return $respuesta;
}

function consultaViajes($conexion, $consulta)
{

    //Consulta 
    $query = mysqli_query($conexion, $consulta);
    $row_cnt = $query->num_rows;
    if ($row_cnt > 0) {
        while ($row = $query->fetch_array(MYSQLI_ASSOC)) {
            $idViaje = $row["idViaje"];
            $row["key"] = $row["idViaje"];
            $dateC = new DateTime($row["fecha_carga"]);
            $row["fecha_carga"] = $dateC->format('Y-m-d H:i');
            $dateE = new DateTime($row["fecha_entrega"]);
            $row["fecha_entrega"] = $dateE->format('Y-m-d H:i');
            $row["distanciaViaje"] = round($row["distanciaViaje"] / 1000, -1);
            $row["tiempo"] = round($row["tiempo"]);
            $row["gastos"] = consultaGastos($conexion, "SELECT * FROM gastos where idViaje = $idViaje");
            $row["tramos"] = consultaTramos($conexion, "SELECT id as idTramo, tramo, fecha, destino, origen, entrega, tiempo_carga, tiempo, distancia as distanciaTramo, waypoints, observaciones, estatus as estatusTramo FROM tramos where idViaje = $idViaje");

            global $gasto, $presupuesto;
            $row["gasto"] = $gasto;
            $row["presupuesto"] = $presupuesto;

            $respuesta[] = $row;
        }
    } else {
        $respuesta = [];
    }

    return $respuesta;
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
                $consulta = "SELECT  v.id as idViaje, e.nombre as empresa, v.fecha_carga, v.cliente, v.unidad, v.operador, v.destino as destinoViaje, v.ruta, v.fecha_entrega, v.fecha_disponibilidad, v.tiempo, v.distancia as distanciaViaje, pv.precio, v.estatus as estatusViaje, tp.nombre as tipoPrecio  
                FROM viajes v
                INNER JOIN empresa_viaje ev on v.id = ev.idViaje
                INNER JOIN empresa e on ev.idEmpresa = e.id 
                INNER JOIN precio_viaje pv on v.id = pv.idViaje
                INNER JOIN tipo_precio tp on pv.idTipoPrecio = tp.id
                where v.estatus in ('Gastos', 'En proceso cliente', 'En proceso', 'En trayecto' ) ORDER BY v.id DESC;";
                break;
            case "Gastos":
                $consulta = "SELECT  v.id as idViaje, e.nombre as empresa, v.fecha_carga, v.cliente, v.unidad, v.operador, v.destino as destinoViaje, v.ruta, v.fecha_entrega, v.fecha_disponibilidad, v.tiempo, v.distancia as distanciaViaje, pv.precio, v.estatus as estatusViaje, tp.nombre as tipoPrecio  
                FROM viajes v
                INNER JOIN empresa_viaje ev on v.id = ev.idViaje
                INNER JOIN empresa e on ev.idEmpresa = e.id 
                INNER JOIN precio_viaje pv on v.id = pv.idViaje
                INNER JOIN tipo_precio tp on pv.idTipoPrecio = tp.id
                where v.estatus = 'Gastos' ORDER BY v.id DESC;";
                break;
            case "En carga":
                $consulta = "SELECT  v.id as idViaje, e.nombre as empresa, v.fecha_carga, v.cliente, v.unidad, v.operador, v.destino as destinoViaje, v.ruta, v.fecha_entrega, v.fecha_disponibilidad, v.tiempo, v.distancia as distanciaViaje, pv.precio, v.estatus as estatusViaje, tp.nombre as tipoPrecio  
                FROM viajes v
                INNER JOIN empresa_viaje ev on v.id = ev.idViaje
                INNER JOIN empresa e on ev.idEmpresa = e.id 
                INNER JOIN precio_viaje pv on v.id = pv.idViaje
                INNER JOIN tipo_precio tp on pv.idTipoPrecio = tp.id
                where v.estatus in ('En proceso cliente', 'En proceso') ORDER BY v.id DESC;";
                break;
            case "En trayecto":
                $consulta = "SELECT  v.id as idViaje, e.nombre as empresa, v.fecha_carga, v.cliente, v.unidad, v.operador, v.destino as destinoViaje, v.ruta, v.fecha_entrega, v.fecha_disponibilidad, v.tiempo, v.distancia as distanciaViaje, pv.precio, v.estatus as estatusViaje, tp.nombre as tipoPrecio  
                FROM viajes v
                INNER JOIN empresa_viaje ev on v.id = ev.idViaje
                INNER JOIN empresa e on ev.idEmpresa = e.id 
                INNER JOIN precio_viaje pv on v.id = pv.idViaje
                INNER JOIN tipo_precio tp on pv.idTipoPrecio = tp.id
                where v.estatus = 'En trayecto' ORDER BY v.id DESC;";
                break;
            case "Entrega":
                $consulta = "SELECT  v.id as idViaje, e.nombre as empresa, v.fecha_carga, v.cliente, v.unidad, v.operador, v.destino as destinoViaje, v.ruta, v.fecha_entrega, v.fecha_disponibilidad, v.tiempo, v.distancia as distanciaViaje, pv.precio, v.estatus as estatusViaje, tp.nombre as tipoPrecio  
                FROM viajes v
                INNER JOIN empresa_viaje ev on v.id = ev.idViaje
                INNER JOIN empresa e on ev.idEmpresa = e.id 
                INNER JOIN precio_viaje pv on v.id = pv.idViaje
                INNER JOIN tipo_precio tp on pv.idTipoPrecio = tp.id
                where v.estatus in ( 'En regreso', 'Evidencia completa', 'Evidencia incompleta') ORDER BY v.id DESC;";
                break;
            case "En regreso":
                $consulta = "SELECT  v.id as idViaje, e.nombre as empresa, v.fecha_carga, v.cliente, v.unidad, v.operador, v.destino as destinoViaje, v.ruta, v.fecha_entrega, v.fecha_disponibilidad, v.tiempo, v.distancia as distanciaViaje, pv.precio, v.estatus as estatusViaje, tp.nombre as tipoPrecio  
                    FROM viajes v
                    INNER JOIN empresa_viaje ev on v.id = ev.idViaje
                    INNER JOIN empresa e on ev.idEmpresa = e.id 
                    INNER JOIN precio_viaje pv on v.id = pv.idViaje
                    INNER JOIN tipo_precio tp on pv.idTipoPrecio = tp.id
                    where v.estatus in ( 'En regreso') ORDER BY v.id DESC;";
                break;
            case "Evidencia completa":
                $consulta = "SELECT  v.id as idViaje, e.nombre as empresa, v.fecha_carga, v.cliente, v.unidad, v.operador, v.destino as destinoViaje, v.ruta, v.fecha_entrega, v.fecha_disponibilidad, v.tiempo, v.distancia as distanciaViaje, pv.precio, v.estatus as estatusViaje, tp.nombre as tipoPrecio  
                FROM viajes v
                INNER JOIN empresa_viaje ev on v.id = ev.idViaje
                INNER JOIN empresa e on ev.idEmpresa = e.id 
                INNER JOIN precio_viaje pv on v.id = pv.idViaje
                INNER JOIN tipo_precio tp on pv.idTipoPrecio = tp.id
                where v.estatus in ( 'Evidencia completa') ORDER BY v.id DESC;";
                break;
            case "Evidencia incompleta":
                $consulta = "SELECT  v.id as idViaje, e.nombre as empresa, v.fecha_carga, v.cliente, v.unidad, v.operador, v.destino as destinoViaje, v.ruta, v.fecha_entrega, v.fecha_disponibilidad, v.tiempo, v.distancia as distanciaViaje, pv.precio, v.estatus as estatusViaje, tp.nombre as tipoPrecio  
                    FROM viajes v
                    INNER JOIN empresa_viaje ev on v.id = ev.idViaje
                    INNER JOIN empresa e on ev.idEmpresa = e.id 
                    INNER JOIN precio_viaje pv on v.id = pv.idViaje
                    INNER JOIN tipo_precio tp on pv.idTipoPrecio = tp.id
                    where v.estatus in ( 'Evidencia incompleta') ORDER BY v.id DESC;";
                break;
            case "Historial":
                $consulta = "SELECT  v.id as idViaje, e.nombre as empresa, v.fecha_carga, v.cliente, v.unidad, v.operador, v.destino as destinoViaje, v.ruta, v.fecha_entrega, v.fecha_disponibilidad, v.tiempo, v.distancia as distanciaViaje, pv.precio, v.estatus as estatusViaje, tp.nombre as tipoPrecio  
                FROM viajes v
                INNER JOIN empresa_viaje ev on v.id = ev.idViaje
                INNER JOIN empresa e on ev.idEmpresa = e.id 
                INNER JOIN precio_viaje pv on v.id = pv.idViaje
                INNER JOIN tipo_precio tp on pv.idTipoPrecio = tp.id
                where v.estatus in ('Liberado', 'Pagado', 'No pagado','Cancelado',  'Finalizado' ) ORDER BY v.id DESC;";
                break;
            case "Liberado":
                $consulta = "SELECT  v.id as idViaje, e.nombre as empresa, v.fecha_carga, v.cliente, v.unidad, v.operador, v.destino as destinoViaje, v.ruta, v.fecha_entrega, v.fecha_disponibilidad, v.tiempo, v.distancia as distanciaViaje, pv.precio, v.estatus as estatusViaje, tp.nombre as tipoPrecio  
                    FROM viajes v
                    INNER JOIN empresa_viaje ev on v.id = ev.idViaje
                    INNER JOIN empresa e on ev.idEmpresa = e.id 
                    INNER JOIN precio_viaje pv on v.id = pv.idViaje
                    INNER JOIN tipo_precio tp on pv.idTipoPrecio = tp.id
                    where v.estatus in ( 'Liberado') ORDER BY v.id DESC;";
                break;
            case "Pagado":
                $consulta = "SELECT  v.id as idViaje, e.nombre as empresa, v.fecha_carga, v.cliente, v.unidad, v.operador, v.destino as destinoViaje, v.ruta, v.fecha_entrega, v.fecha_disponibilidad, v.tiempo, v.distancia as distanciaViaje, pv.precio, v.estatus as estatusViaje, tp.nombre as tipoPrecio  
                        FROM viajes v
                        INNER JOIN empresa_viaje ev on v.id = ev.idViaje
                        INNER JOIN empresa e on ev.idEmpresa = e.id 
                        INNER JOIN precio_viaje pv on v.id = pv.idViaje
                        INNER JOIN tipo_precio tp on pv.idTipoPrecio = tp.id
                        where v.estatus in ( 'Pagado') ORDER BY v.id DESC;";
                break;
            case "No pagado":
                $consulta = "SELECT  v.id as idViaje, e.nombre as empresa, v.fecha_carga, v.cliente, v.unidad, v.operador, v.destino as destinoViaje, v.ruta, v.fecha_entrega, v.fecha_disponibilidad, v.tiempo, v.distancia as distanciaViaje, pv.precio, v.estatus as estatusViaje, tp.nombre as tipoPrecio  
                            FROM viajes v
                            INNER JOIN empresa_viaje ev on v.id = ev.idViaje
                            INNER JOIN empresa e on ev.idEmpresa = e.id 
                            INNER JOIN precio_viaje pv on v.id = pv.idViaje
                            INNER JOIN tipo_precio tp on pv.idTipoPrecio = tp.id
                            where v.estatus in ( 'No pagado') ORDER BY v.id DESC;";
                break;
            case "Todos":
                $consulta = "SELECT  v.id as idViaje, e.nombre as empresa, v.fecha_carga, v.cliente, v.unidad, v.operador, v.destino as destinoViaje, v.ruta, v.fecha_entrega, v.fecha_disponibilidad, v.tiempo, v.distancia as distanciaViaje, pv.precio, v.estatus as estatusViaje, tp.nombre as tipoPrecio  
                FROM viajes v
                INNER JOIN empresa_viaje ev on v.id = ev.idViaje
                INNER JOIN empresa e on ev.idEmpresa = e.id 
                INNER JOIN precio_viaje pv on v.id = pv.idViaje
                INNER JOIN tipo_precio tp on pv.idTipoPrecio = tp.id 
                ORDER BY v.id DESC;";
                break;
        }

        $viajes = consultaViajes($conexion, $consulta);


        //Response
        if (empty($viajes)) {
            respuesta(200, 404, "No existen viajes ", []);
        } else {
            $payload = $viajes;
            respuesta(200, 200, "Respuesta exitosa", $viajes);
        }
    }
} else {
    $payload = ["Faltantes" => $faltantes];
    respuesta(400, 400, "Hay un error con el servidor. Llama a central Error-VI-BAD-REQUEST", $payload);
}
