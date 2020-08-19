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

function consulta($conexion, $consulta)
{
    //Consulta 
    $query = mysqli_query($conexion, $consulta);
    $row_cnt = $query->num_rows;
    if ($row_cnt > 0) {
        while ($row = $query->fetch_array(MYSQLI_ASSOC)) {
            $row["key"] = $row["idViaje"] . "P";
            $respuesta[] = $row;
        }
    } else {
        $respuesta = [];
    }

    return $respuesta;
}
$cont1 = 0;
$cont2 = 0;
function consultaViajes($conexion, $consulta, $grupo)
{
    global $cont1, $cont2;
    $sinAtender = 0;
    $sinOperador = 0;
    $sinEstatus = 0;
    //Consulta 
    $query = mysqli_query($conexion, $consulta);
    $row_cnt = $query->num_rows;
    if ($row_cnt > 0) {
        while ($row = $query->fetch_array(MYSQLI_ASSOC)) {
            $row["key"] = $row["id"];
            //sin atender
            $start_date = new DateTime();
            $since_start =  $start_date->diff(new DateTime($row["fecha_registro"]));
            if ($since_start->h > 30) {
                $sinAtender = $sinAtender + 1;
            }

            //sin operador
            if ($row["operador"] == "") {
                $sinOperador = $sinOperador + 1;
            }

            //sin estatus
            if ($row["estatus_app"] == "Pendiente") {
                $sinEstatus = $sinEstatus + 1;
            }

            //adicinales
            $row["adicionales"] = consulta($conexion, "SELECT ma.precio as seguro, mn.precio as maniobra, sa.precio as seguridad, cu.precio as custodia, sas.idViaje from serviciosAdicionales sas 
            INNER JOIN mercanciaAsegurada ma on sas.idServicioAdicional = ma.idServicioAdicional 
            INNER JOIN maniobras mn on sas.idServicioAdicional = mn.idServicioAdicional 
            INNER JOIN seguridadAdicional sa on sas.idServicioAdicional = sa.idServicioAdicional 
            INNER JOIN custodia cu on sas.idServicioAdicional = cu.idServicioAdicional 
            where idViaje = " . $row["id"]);

            if (
                $row["adicionales"] == null
            ) {

                $row["adicionales"] = [
                    "seguro" => "0",
                    "maniobra" => "0",
                    "seguridad" => "0",
                    "custodia" => "0"
                ];
            }
            $respuestaTemp[] = $row;
        }
    } else {
        $row_cnt = 0;
        $respuestaTemp = [];
    }

    $respuesta["sin_atender"] = $sinAtender;
    $respuesta["sin_operador"] = $sinOperador;
    $respuesta["sin_estatus"] = $sinEstatus;
    $respuesta["total"] = $row_cnt;
    $respuesta["viajes"] = $respuestaTemp;

    if ($grupo == 1) {
        $cont1 = $cont1 + $row_cnt;
    } else {
        $cont2 = $cont2 + $row_cnt;
    }


    return $respuesta;
}




//Validacion de Datos

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

        //Consulta viajes Grupo 1
        $consultaGrupo1PremiumPrepago = "SELECT v.id, v.cliente, t.entrega as direccion_carga, v.fecha_carga, v.unidad_tipo as tipo_adecuacion, v.unidad_modelo as tipo_unidad, v.ruta as entrega, v.destino, v.fecha_entrega, s.nombre as servicio, p.precio, p.fecha as fecha_registro, v.operador, v.estatus_app 
        from viajes v 
        INNER JOIN precio_viaje p on p.idViaje = v.id 
        INNER JOIN tramos t on t.idViaje = v.id 
        INNER JOIN tipo_precio s on p.idTipoPrecio = s.id  
        where t.tramo = 1 and v.estatus = 'Solicitud' 
        and p.horario_laboral = true 
        and (SELECT TIMESTAMPDIFF(HOUR,(p.fecha),(v.fecha_carga))) > 12";

        /* $consultaGrupo1PremiumPospago = "SELECT v.id, v.cliente, t.entrega as direccion_carga, v.fecha_carga, v.unidad_tipo as tipo_adecuacion, v.unidad_modelo as tipo_unidad, v.ruta as entrega, v.destino, v.fecha_entrega, s.nombre as servicio, p.precio, p.fecha as fecha_registro, v.operador, v.estatus_app from viajes v INNER JOIN precio_viaje p on p.idViaje = v.id INNER JOIN tramos t on t.idViaje = v.id INNER JOIN tipo_precio s on p.idTipoPrecio = s.id  where t.tramo = 1 and v.estatus = 'Solicitud' and p.horario_laboral = true and (SELECT TIMESTAMPDIFF(HOUR,(p.fecha),(v.fecha_carga))) > 12";

        $consultaGrupo1BasicoPospago = "SELECT v.id, v.cliente, t.entrega as direccion_carga, v.fecha_carga, v.unidad_tipo as tipo_adecuacion, v.unidad_modelo as tipo_unidad, v.ruta as entrega, v.destino, v.fecha_entrega, s.nombre as servicio, p.precio, p.fecha as fecha_registro, v.operador, v.estatus_app from viajes v INNER JOIN precio_viaje p on p.idViaje = v.id INNER JOIN tramos t on t.idViaje = v.id INNER JOIN tipo_precio s on p.idTipoPrecio = s.id  where t.tramo = 1 and v.estatus = 'Solicitud' and p.horario_laboral = true and (SELECT TIMESTAMPDIFF(HOUR,(p.fecha),(v.fecha_carga))) > 12";

        $consultaGrupo1Contrato = "SELECT v.id, v.cliente, t.entrega as direccion_carga, v.fecha_carga, v.unidad_tipo as tipo_adecuacion, v.unidad_modelo as tipo_unidad, v.ruta as entrega, v.destino, v.fecha_entrega, s.nombre as servicio, p.precio, p.fecha as fecha_registro, v.operador, v.estatus_app from viajes v INNER JOIN precio_viaje p on p.idViaje = v.id INNER JOIN tramos t on t.idViaje = v.id INNER JOIN tipo_precio s on p.idTipoPrecio = s.id  where t.tramo = 1 and v.estatus = 'Solicitud' and p.horario_laboral = true and (SELECT TIMESTAMPDIFF(HOUR,(p.fecha),(v.fecha_carga))) > 12";
 */
        //Consulta viajes Grupo 2
        $consultaGrupo2PremiumPrepago = "SELECT v.id, v.cliente, t.entrega as direccion_carga, v.fecha_carga, v.unidad_tipo as tipo_adecuacion, v.unidad_modelo as tipo_unidad, v.ruta as entrega, v.destino, v.fecha_entrega, s.nombre as servicio, p.precio, p.fecha as fecha_registro, v.operador, v.estatus_app 
        from viajes v 
        INNER JOIN precio_viaje p on p.idViaje = v.id 
        INNER JOIN tramos t on t.idViaje = v.id 
        INNER JOIN tipo_precio s on p.idTipoPrecio = s.id  
        where t.tramo = 1 
        and v.estatus = 'Solicitud' 
        and (p.horario_laboral = false 
        or (SELECT TIMESTAMPDIFF(HOUR,(p.fecha),(v.fecha_carga))) < 12) ";
        /* 
        $consultaGrupo2PremiumPospago = "SELECT v.id, v.cliente, t.entrega as direccion_carga, v.fecha_carga, v.unidad_tipo as tipo_adecuacion, v.unidad_modelo as tipo_unidad, v.ruta as entrega, v.destino, v.fecha_entrega, s.nombre as servicio, p.precio, p.fecha as fecha_registro, v.operador, v.estatus_app from viajes v INNER JOIN precio_viaje p on p.idViaje = v.id INNER JOIN tramos t on t.idViaje = v.id INNER JOIN tipo_precio s on p.idTipoPrecio = s.id  where t.tramo = 1 and v.estatus = 'Solicitud' and (p.horario_laboral = false or (SELECT TIMESTAMPDIFF(HOUR,(p.fecha),(v.fecha_carga))) < 12) ";

        $consultaGrupo2BasicoPospago = "SELECT v.id, v.cliente, t.entrega as direccion_carga, v.fecha_carga, v.unidad_tipo as tipo_adecuacion, v.unidad_modelo as tipo_unidad, v.ruta as entrega, v.destino, v.fecha_entrega, s.nombre as servicio, p.precio, p.fecha as fecha_registro, v.operador, v.estatus_app from viajes v INNER JOIN precio_viaje p on p.idViaje = v.id INNER JOIN tramos t on t.idViaje = v.id INNER JOIN tipo_precio s on p.idTipoPrecio = s.id  where t.tramo = 1 and v.estatus = 'Solicitud' and (p.horario_laboral = false or (SELECT TIMESTAMPDIFF(HOUR,(p.fecha),(v.fecha_carga))) < 12) ";

        $consultaGrupo2Contrato = "SELECT v.id, v.cliente, t.entrega as direccion_carga, v.fecha_carga, v.unidad_tipo as tipo_adecuacion, v.unidad_modelo as tipo_unidad, v.ruta as entrega, v.destino, v.fecha_entrega, s.nombre as servicio, p.precio, p.fecha as fecha_registro, v.operador, v.estatus_app from viajes v INNER JOIN precio_viaje p on p.idViaje = v.id INNER JOIN tramos t on t.idViaje = v.id INNER JOIN tipo_precio s on p.idTipoPrecio = s.id  where t.tramo = 1 and v.estatus = 'Solicitud' and (p.horario_laboral = false or (SELECT TIMESTAMPDIFF(HOUR,(p.fecha),(v.fecha_carga))) < 12) ";
 */

        $payload = [
            "G1" => consultaViajes($conexion, $consultaGrupo1PremiumPrepago, 1),
            "G2" => consultaViajes($conexion, $consultaGrupo2PremiumPrepago, 2),
        ];


        //Response
        if (empty($payload)) {
            respuesta(200, 404, "No tienes viajes asignados", []);
        } else {

            respuesta(200, 200, "Respuesta exitosa", $payload);
        }
    }
} else {
    $payload = ["Faltantes" => $faltantes];
    respuesta(400, 400, "Hay un error con el servidor. Llama a central Error-TRRE1", $payload);
}
