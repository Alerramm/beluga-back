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
$idViaje = $datos["id"];

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
            //$row["tramos"] = consulta($conexion, "SELECT * FROM tramos WHERE idViaje = " . $row["id"]);
            $respuesta = $row;
        }
    } else {
        $respuesta = [];
    }

    return $respuesta;
}

function diesel($distancia, $diesel, $rendimiento)
{
    return round($distancia / $rendimiento * $diesel);
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
        $consultaViaje = "SELECT v.distancia, v.diesel, m.rendimiento, m.viaticos, m.comision, v.casetas, p.precio, p.idMetricasPrecio
        FROM viajes v 
        INNER JOIN precio_viaje p on p.idViaje = v.id
        INNER JOIN metricas_precio m on p.idMetricasPrecio = m.id
        and v.id = $idViaje";

        $viajes = consultaViajes($conexion, $consultaViaje);

        if ($viajes["viaticos"] === '') {
            $viajes["viaticos"] = 0;
        }

        if ($viajes["comision"] === '') {
            $viajes["comision"] = 0;
        }

        if ($viajes["casetas"] === '') {
            $viajes["casetas"] = 0;
        }
        $km = $viajes["distancia"] / 1000;


        $datosgastos = [
            [
                "tipo" => "Diesel",
                "presupuesto" => diesel($km, $viajes["diesel"], $viajes["rendimiento"])
            ],
            [
                "tipo" => "Viaticos",
                "presupuesto" => $viajes["viaticos"]
            ],
            [
                "tipo" => "Comision",
                "presupuesto" =>  $viajes["comision"]
            ],
            [
                "tipo" => "Casetas",
                "presupuesto" => $viajes["casetas"]
            ],
            [
                "tipo" => "Maniobras",
                "presupuesto" => 0
            ],
            [
                "tipo" => "Custodia",
                "presupuesto" => 0
            ],
            [
                "tipo" => "Externo",
                "presupuesto" => 0
            ],
            [
                "tipo" => "Transito",
                "presupuesto" => 0
            ],
            [
                "tipo" => "Estadias",
                "presupuesto" => 0
            ],
            [
                "tipo" => "Mantenimiento",
                "presupuesto" => 0
            ]

        ];


        $totalGastos = 0;
        foreach ($datosgastos as &$datos2) {
            //const 
            $faltantes = [];
            //datos Request
            $tipo = $datos2["tipo"];
            $presupuesto = $datos2["presupuesto"];
            $totalGastos = $totalGastos + round($presupuesto, -1);
            $insertDesgloseAuth2 =  "INSERT INTO gastos(tipo,presupuesto,idViaje,estatus) VALUES ('$tipo', '$presupuesto', '$idViaje', 'Autorizacion')";

            if ($conexion->query($insertDesgloseAuth2) === TRUE) {

                $last_id = $conexion->insert_id;
                $payload[] = ["GastosInsert" => " Exito New Travel record created successfully " . $last_id];
            } else {
                $payload[] = ["sql" => "Error: " . "<br>" . $conexion->error];
            }
        }

        $porcentajeGastos = 100;
        if ($viajes["precio"] >= $totalGastos / 1.16) {
            $porcentajeGastos = ($totalGastos / 1.16 / $viajes["precio"]) * 100;
        }

        $idMetricasPrecio = $viajes["idMetricasPrecio"];
        $updateMetricas =  "UPDATE metricas_precio set gasto_premium = '$porcentajeGastos' where id = $idMetricasPrecio";

        if ($conexion->query($updateMetricas) === TRUE) {
            $last_id = $conexion->insert_id;
            $payload["Metricas"] = " Exito New Travel record created successfully " . $last_id;
        } else {
            $payload["Metricas"] = ["sql" => "Error: " . "<br>" . $conexion->error];
        }

        //Response
        if (empty($payload)) {
            respuesta(200, 404, "No se agregaron gastos", []);
        } else {

            respuesta(200, 200, "Respuesta exitosa", $payload);
        }
    }
} else {
    $payload = ["Faltantes" => $faltantes];
    respuesta(400, 400, "Hay un error con el servidor. Llama a central Error-TRRE1", $payload);
}
