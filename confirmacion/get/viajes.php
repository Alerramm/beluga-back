<?PHP
//datos .env
include '../production.php';

//header
header('Access-Control-Allow-Origin: *');
header("Content-Type: application/json; charset=UTF-8");

//const 
$faltantes = [];

//datos Request
file_get_contents("http://www.misistema.mx/beluga/Finanzas/endpoints/confirmacion/post/inicioConfirmacion.php");

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

function consultaGastos($conexion, $consulta, $rowRespuesta)
{
    //Consulta 
    $query = mysqli_query($conexion, $consulta);
    $row_cnt = $query->num_rows;
    if ($row_cnt > 0) {
        while ($row = $query->fetch_array(MYSQLI_ASSOC)) {
            $tipo = $row["tipo"];
            $rowRespuesta[strtolower($tipo)] = round($row["presupuesto"], -1);
        }
    }

    return $rowRespuesta;
}

function clientRest($Datos, $url)
{
    $ch = curl_init($url);
    $jsonDataEncoded = json_encode($Datos);
    curl_setopt($ch, CURLOPT_POST, 1);
    curl_setopt($ch, CURLOPT_POSTFIELDS, $jsonDataEncoded);
    curl_setopt($ch, CURLOPT_HTTPHEADER, array('Content-Type: application/json'));
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
    $result = curl_exec($ch);
    return json_decode($result, true);
}

function consultaViajes($conexion, $consulta)
{
    //Consulta 
    $query = mysqli_query($conexion, $consulta);
    $row_cnt = $query->num_rows;
    if ($row_cnt > 0) {
        while ($row = $query->fetch_array(MYSQLI_ASSOC)) {
            $row["tramos"] = consulta($conexion, "SELECT id, tramo, fecha as fecha_tramo, destino as destino_tramo, entrega as entrega_tramo, distancia as distancia_tramo, casetas as casetas_tramo FROM tramos WHERE idViaje = " . $row["idViaje"]);
            $row["key"] = $row["idViaje"];
            $row["kilometraje"] = round($row["kilometraje"] / 1000, -1);
            $dateC = new DateTime($row["fecha_carga"]);
            $row["fecha_carga"] = $dateC->format('Y-m-d H:i');
            $dateE = new DateTime($row["fecha_entrega"]);
            $row["fecha_entrega"] = $dateE->format('Y-m-d H:i');
            $row["empresa"] = consulta($conexion, "SELECT id, nombre from empresa");
            $conductores = array(
                "fecha_inicial" => $row["fecha_salida"],
                "fecha_final" => $row["fecha_disponibilidad"],
                "idEmpresa" => $row["empresa"],
            );
            $unidades = array(
                "fecha_inicial" => $row["fecha_salida"],
                "fecha_final" => $row["fecha_disponibilidad"],
                "toneladas" => $row["unidad_modelo"],
                "tipo" => $row["unidad_tipo"],
                "idEmpresa" => $row["empresa"],
            );
            $row["operadorSeleccionado"] = $row["operador"];
            $row["unidadSeleccionada"] = $row["unidad"];
            $row["operador"] = clientRest($conductores, "http://www.misistema.mx/beluga/Finanzas/endpoints/confirmacion/get/conductores.php");
            $row["unidad"] =  clientRest($unidades, "http://www.misistema.mx/beluga/Finanzas/endpoints/confirmacion/get/modelosActivos.php");
            $row = consultaGastos($conexion, "SELECT * FROM gastos WHERE idViaje = " . $row["idViaje"], $row);
            $respuesta[] = $row;
        }
    } else {
        $respuesta = [];
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
        respuesta(500, 500, "Hay un error con el servidor. Llama a central Error-CONECTION-DATA", []);
    } else {
        //configuracon db
        mysqli_query($conexion, "SET CHARACTER SET 'utf8'");
        mysqli_query($conexion, "SET SESSION collation_connection ='utf8_unicode_ci'");


        //Consulta viajes
        $consultaViajes =
            "SELECT v.id as idViaje, v.estatus_app as estatus_operador, ev.estatus as estatus_empresa, v.cliente, t.entrega as direccion_carga, v.fecha_carga, e.nombre as empresa, v.operador, v.unidad, v.ruta as entrega, v.destino, v.fecha_entrega, p.precio, v.casetas, m.gasto_premium as porcentaje_gasto, v.fecha_salida, v.fecha_disponibilidad, v.unidad_tipo, v.unidad_modelo, v.distancia as kilometraje, m.rendimiento
            FROM viajes v 
            INNER JOIN empresa_viaje ev on v.id = ev.idViaje
            INNER JOIN tramos t on v.id = t.idViaje
            INNER JOIN empresa e on ev.idEmpresa = e.id
            INNER JOIN precio_viaje p on p.idViaje = v.id
            INNER JOIN metricas_precio m on p.idMetricasPrecio = m.id
            where t.tramo = 1
            and v.estatus = 'Confirmado' 
            ORDER BY v.id DESC";

        $payload = consultaViajes($conexion, $consultaViajes);


        //Response
        if (empty($payload)) {
            respuesta(200, 404, "No existen viajes por confirmar", []);
        } else {

            respuesta(200, 200, "Respuesta exitosa", $payload);
        }
    }
} else {
    $payload = ["Faltantes" => $faltantes];
    respuesta(400, 400, "Hay un error con el servidor. Llama a central Error-VI-BAD-REQUEST", $payload);
}
