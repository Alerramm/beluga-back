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
$totalGastos = $datos["totalGastos"];
$precio = $datos["precio"];
$idMetricasPrecio = $datos["idMetricasPrecio"];

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

function consultaViaje($conexion, $consulta)
{
    //Consulta 
    $query = mysqli_query($conexion, $consulta);
    $row_cnt = $query->num_rows;
    if ($row_cnt > 0) {
        while ($row = $query->fetch_array(MYSQLI_ASSOC)) {
            $respuesta = $row;
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
        respuesta(500, 500, "Hay un error con el servidor. Llama a central Error-TRBD1", []);
    } else {
        //configuracon db
        mysqli_query($conexion, "SET CHARACTER SET 'utf8'");
        mysqli_query($conexion, "SET SESSION collation_connection ='utf8_unicode_ci'");

        //Consulta viajes Grupo 1
        $viajeConsulta = "SELECT * FROM viajes v 
        INNER JOIN precio_viaje pv on v.id = pv.idViaje
        INNER JOIN metricas_precio mp on pv.idMetricasPrecio = mp.id
        WHERE v.id = $idViaje";

        $viaje = consultaViaje($conexion, $viajeConsulta);

        //Response
        if (empty($viaje)) {
            respuesta(200, 404, "No se encontro el id viaje " . $idViaje, []);
        } else {
            $updatePrecio =  "UPDATE precio_viaje set precio = '$precio' where idViaje = $idViaje";

            if ($conexion->query($updatePrecio) === TRUE) {
                $payload["PrecioSql"] = " Exito New Travel record created successfully " . $idViaje;
                $payload["precio"] = $precio;
            } else {
                $payload["PrecioSql"] = ["sql" => "Error: " . "<br>" . $conexion->error];
            }

            $porcentajeGastos = 100;
            if ($precio >= $totalGastos) {
                $porcentajeGastos = round(($totalGastos / 1.16 / $precio) * 100, 2);
            }

            $updateMetricas =  "UPDATE metricas_precio set gasto_premium = '$porcentajeGastos' where id = $idMetricasPrecio";

            if ($conexion->query($updateMetricas) === TRUE) {
                $payload["ProcentajeSql"] = " Exito New Travel record created successfully " . $idMetricasPrecio;
                $payload["porcentajeGasto"] = $porcentajeGastos;
            } else {
                $payload["ProcentajeSql"] = ["sql" => "Error: " . "<br>" . $conexion->error];
            }

            respuesta(200, 200, "Respuesta exitosa", $payload);
        }
    }
} else {
    $payload = ["Faltantes" => $faltantes];
    respuesta(400, 400, "Hay un error con el servidor. Llama a central Error-Update porcentaje Gasto", $payload);
}
