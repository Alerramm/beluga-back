<?PHP
//datos .env
include '../production.php';

//header
header('Access-Control-Allow-Origin: cliente');
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
            $row["adicionales"] = consulta($conexion, "SELECT ma.precio as seguro, mn.precio as maniobra, sa.precio as seguridad, cu.precio as custodia from serviciosAdicionales sas 
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
            $respuesta[] = $row;
        }
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
        $consultaGrupo1PremiumPrepago = "SELECT v.id, v.cliente, t.entrega as direccion_carga, v.fecha_carga, v.unidad_tipo as tipo_adecuacion, v.unidad_modelo as tipo_unidad, v.ruta, v.destino, v.fecha_entrega, s.nombre, p.precio from viajes v INNER JOIN precio_viaje p on p.idViaje = v.id INNER JOIN tramos t on t.idViaje = v.id INNER JOIN tipo_precio s on p.idTipoPrecio = s.id  where t.tramo = 1 and v.estatus = 'Solicitud' and p.idTipoPrecio = 1 and p.horario_laboral = true and (SELECT TIMESTAMPDIFF(HOUR,(p.fecha),(v.fecha_carga))) > 12";

        $consultaGrupo1PremiumPospago = "SELECT v.id, v.cliente, t.entrega as direccion_carga, v.fecha_carga, v.unidad_tipo as tipo_adecuacion, v.unidad_modelo as tipo_unidad, v.ruta, v.destino, v.fecha_entrega, s.nombre, p.precio from viajes v INNER JOIN precio_viaje p on p.idViaje = v.id INNER JOIN tramos t on t.idViaje = v.id INNER JOIN tipo_precio s on p.idTipoPrecio = s.id  where t.tramo = 1 and v.estatus = 'Solicitud' and p.idTipoPrecio = 2 and p.horario_laboral = true and (SELECT TIMESTAMPDIFF(HOUR,(p.fecha),(v.fecha_carga))) > 12";

        $consultaGrupo1BasicoPospago = "SELECT v.id, v.cliente, t.entrega as direccion_carga, v.fecha_carga, v.unidad_tipo as tipo_adecuacion, v.unidad_modelo as tipo_unidad, v.ruta, v.destino, v.fecha_entrega, s.nombre, p.precio from viajes v INNER JOIN precio_viaje p on p.idViaje = v.id INNER JOIN tramos t on t.idViaje = v.id INNER JOIN tipo_precio s on p.idTipoPrecio = s.id  where t.tramo = 1 and v.estatus = 'Solicitud' and p.idTipoPrecio = 3 and p.horario_laboral = true and (SELECT TIMESTAMPDIFF(HOUR,(p.fecha),(v.fecha_carga))) > 12";

        $consultaGrupo1Contrato = "SELECT v.id, v.cliente, t.entrega as direccion_carga, v.fecha_carga, v.unidad_tipo as tipo_adecuacion, v.unidad_modelo as tipo_unidad, v.ruta, v.destino, v.fecha_entrega, s.nombre, p.precio from viajes v INNER JOIN precio_viaje p on p.idViaje = v.id INNER JOIN tramos t on t.idViaje = v.id INNER JOIN tipo_precio s on p.idTipoPrecio = s.id  where t.tramo = 1 and v.estatus = 'Solicitud' and p.idTipoPrecio = 4 and p.horario_laboral = true and (SELECT TIMESTAMPDIFF(HOUR,(p.fecha),(v.fecha_carga))) > 12";

        //Consulta viajes Grupo 2
        $consultaGrupo2PremiumPrepago = "SELECT v.id, v.cliente, t.entrega as direccion_carga, v.fecha_carga, v.unidad_tipo as tipo_adecuacion, v.unidad_modelo as tipo_unidad, v.ruta, v.destino, v.fecha_entrega, s.nombre, p.precio from viajes v INNER JOIN precio_viaje p on p.idViaje = v.id INNER JOIN tramos t on t.idViaje = v.id INNER JOIN tipo_precio s on p.idTipoPrecio = s.id  where t.tramo = 1 and v.estatus = 'Solicitud' and p.idTipoPrecio = 1 and (p.horario_laboral = false or (SELECT TIMESTAMPDIFF(HOUR,(p.fecha),(v.fecha_carga))) < 12) ";

        $consultaGrupo2PremiumPospago = "SELECT v.id, v.cliente, t.entrega as direccion_carga, v.fecha_carga, v.unidad_tipo as tipo_adecuacion, v.unidad_modelo as tipo_unidad, v.ruta, v.destino, v.fecha_entrega, s.nombre, p.precio from viajes v INNER JOIN precio_viaje p on p.idViaje = v.id INNER JOIN tramos t on t.idViaje = v.id INNER JOIN tipo_precio s on p.idTipoPrecio = s.id  where t.tramo = 1 and v.estatus = 'Solicitud' and p.idTipoPrecio = 2 and (p.horario_laboral = false or (SELECT TIMESTAMPDIFF(HOUR,(p.fecha),(v.fecha_carga))) < 12) ";

        $consultaGrupo2BasicoPospago = "SELECT v.id, v.cliente, t.entrega as direccion_carga, v.fecha_carga, v.unidad_tipo as tipo_adecuacion, v.unidad_modelo as tipo_unidad, v.ruta, v.destino, v.fecha_entrega, s.nombre, p.precio from viajes v INNER JOIN precio_viaje p on p.idViaje = v.id INNER JOIN tramos t on t.idViaje = v.id INNER JOIN tipo_precio s on p.idTipoPrecio = s.id  where t.tramo = 1 and v.estatus = 'Solicitud' and p.idTipoPrecio = 3 and (p.horario_laboral = false or (SELECT TIMESTAMPDIFF(HOUR,(p.fecha),(v.fecha_carga))) < 12) ";

        $consultaGrupo2Contrato = "SELECT v.id, v.cliente, t.entrega as direccion_carga, v.fecha_carga, v.unidad_tipo as tipo_adecuacion, v.unidad_modelo as tipo_unidad, v.ruta, v.destino, v.fecha_entrega, s.nombre, p.precio from viajes v INNER JOIN precio_viaje p on p.idViaje = v.id INNER JOIN tramos t on t.idViaje = v.id INNER JOIN tipo_precio s on p.idTipoPrecio = s.id  where t.tramo = 1 and v.estatus = 'Solicitud' and p.idTipoPrecio = 4 and (p.horario_laboral = false or (SELECT TIMESTAMPDIFF(HOUR,(p.fecha),(v.fecha_carga))) < 12) ";


        $payload = [
            "Grupo 1" => [
                "Premium Prepago" => consultaViajes($conexion, $consultaGrupo1PremiumPrepago),
                "Premium Postpago" => consultaViajes($conexion, $consultaGrupo1PremiumPospago),
                "Basico Postpago" => consultaViajes($conexion, $consultaGrupo1BasicoPospago),
                "Contrato" => consultaViajes($conexion, $consultaGrupo1Contrato)
            ],
            "Grupo 2" => [
                "Premium Prepago" => consultaViajes($conexion, $consultaGrupo2PremiumPrepago),
                "Premium Postpago" => consultaViajes($conexion, $consultaGrupo2PremiumPospago),
                "Basico Postpago" => consultaViajes($conexion, $consultaGrupo2BasicoPospago),
                "Contrato" => consultaViajes($conexion, $consultaGrupo2Contrato)
            ],
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
