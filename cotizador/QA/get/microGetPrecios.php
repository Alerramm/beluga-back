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
$km = $datos["km"];
$casetas = $datos["casetas"];
$idTipoAdecuacion = $datos["idTipoAdecuacion"];
$idTipoUnidad = $datos["idTipoUnidad"];
$idCliente = $datos["idCliente"];

$payloadGastosInsert = [];
$total = [];
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
            $respuesta = $row;
        }
    } else {
        $respuesta = [];
    }

    return $respuesta;
}

//Validacion de Datos
if ($km == "") {
    array_push($faltantes, 'km');
}
if ($idTipoAdecuacion == "") {
    array_push($faltantes, 'idTipoAdecuacion');
}
if ($idTipoUnidad == "") {
    array_push($faltantes, 'idTipoUnidad');
}
if ($idCliente == "") {
    array_push($faltantes, 'idCliente');
}


if (empty($faltantes)) {

    $mysqli = mysqli_init();
    $conexion = mysqli_connect($_SESSION['HOST'], $_SESSION['USER'], $_SESSION['PASS'], $_SESSION['DBNAME']);
    //Validacion conexion con bd
    if (!$conexion) {
        respuesta(500, 500, "Hay un error con el servidor. Llama a central Error-TABD1", []);
    } else {

        mysqli_query($conexion, "SET CHARACTER SET 'utf8'");
        mysqli_query($conexion, "SET SESSION collation_connection ='utf8_unicode_ci'");

        //Analisis de la informacion
        $dieselPrecio =  "SELECT contennidoConstante FROM dbs304381.constantes WHERE idTipoConstante=1";
        $dieselresponse =  mysqli_query($conexion, $dieselPrecio);
        $row = mysqli_fetch_array($dieselresponse, MYSQLI_ASSOC);
        $dieselxdia = $row["contennidoConstante"];
        array_push($payloadGastosInsert, "Primera Consulta", "datos " . $dieselxdia);



        if (empty($row)) {

            array_push($payloadGastosInsert, "Fallo primera Consulta", "datos" . 1);
            respuesta(200, 404, "No hay constante para ese ID" . 1, $payloadGastosInsert);
        } else {

            $consulta =  "SELECT * FROM dbs304381.Kilometros WHERE '$km' BETWEEN KilomInicial and KilomFinal";
            $datos =  mysqli_query($conexion, $consulta);
            $rowDatos = mysqli_fetch_array($datos, MYSQLI_ASSOC);
            array_push($payloadGastosInsert, "Segunda Consulta", "datos" . $rowDatos["idKilometros"]);
            if (empty($rowDatos)) {

                array_push($payloadGastosInsert, "Fallo  Segunda Consulta", "datos" . $km);
                respuesta(200, 404, "No metricas para este tipo de Kilometro " . $km, $payloadGastosInsert);
            } else {


                $costoDiaComision =  "SELECT contennidoConstante FROM dbs304381.constantes WHERE idTipoConstante=2";
                $costoDiaComisionresponse =  mysqli_query($conexion, $costoDiaComision);
                $row2 = mysqli_fetch_array($costoDiaComisionresponse, MYSQLI_ASSOC);
                $costoDiaComisionDato = $row2["contennidoConstante"];

                array_push($payloadGastosInsert, "Tercera Consulta", "datos " . $costoDiaComisionDato);

                if (empty($row2)) {
                    array_push($payloadGastosInsert, "Fallo Tercera Consulta", "datos" . 2);
                    respuesta(200, 404, "No hay constante para ese ID" . 2, $payloadGastosInsert);
                } else {
                    $costoDiesel = ($km / $rowDatos["rendimiento"]) * $dieselxdia;

                    $comision = $rowDatos["numDias"] * $costoDiaComisionDato;
                    $costos = $costoDiesel + $casetas + $rowDatos["viaticos"] + $comision;

                    array_push($payloadGastosInsert, "Primera Operacion", "datos" . $costos);


                    $formula = ($costos / 1.16) / $rowDatos["gastoPremium"];

                    if ($idTipoAdecuacion === 3) {
                        $gastotal = $formula * 1.07;
                    } else {
                        $gastotal = $formula;
                    }


                    array_push($payloadGastosInsert, "Segunda Operacion", "datos" . $gastotal);

                    array_push($payloadGastosInsert, "Tipo de Unidad: ", "datos: " . $idTipoUnidad);

                    switch (true) {
                            //1.5 
                        case ($idTipoUnidad == 1 || $idTipoUnidad == 8):
                            $gastototal = $gastotal * .88;
                            $basico = $gastototal * .85;
                            $premiumpospago = $gastototal;
                            $premiumprepago = $gastototal  * .95;
                            $contrato = 0;
                            break;

                            // 3.5
                        case ($idTipoUnidad == 2 || $idTipoUnidad == 9):
                            $gastototal = $gastotal;
                            $basico = $gastototal * .85;
                            $premiumpospago = $gastototal;
                            $premiumprepago = $gastototal  * .95;
                            $contrato = 0;
                            break;

                            //5.5
                        case ($idTipoUnidad == 3 || $idTipoUnidad == 10):
                            $gastototal = $gastotal * 1.12;
                            $basico = $gastototal * .85;
                            $premiumpospago = $gastototal;
                            $premiumprepago = $gastototal  * .95;
                            $contrato = 0;
                            break;


                            //10
                        case ($idTipoUnidad == 4 || $idTipoUnidad == 11):
                            $gastototal = ($gastotal * 1.12) * 1.2;
                            $basico = $gastototal * .85;
                            $premiumpospago = $gastototal;
                            $premiumprepago = $gastototal  * .95;
                            $contrato = 0;
                            break;

                            //18
                        case ($idTipoUnidad == 5 || $idTipoUnidad == 12):
                            $gastototal = (($gastotal * 1.12) * 1.12) * 1.2;
                            $basico = $gastototal * .85;
                            $premiumpospago = $gastototal;
                            $premiumprepago = $gastototal  * .95;
                            $contrato = 0;
                            break;

                            //25
                        case ($idTipoUnidad == 6 || $idTipoUnidad == 13):
                            $gastototal = ((($gastotal * 1.12) * 1.12) * 1.2) * 1.12;
                            $basico = $gastototal * .85;
                            $premiumpospago = $gastototal;
                            $premiumprepago = $gastototal  * .95;
                            $contrato = 0;
                            break;

                            //50
                        case ($idTipoUnidad == 7 || $idTipoUnidad == 14):
                            $gastototal = (((($gastotal * 1.12) * 1.12) * 1.2) * 1.12) * 1.12;
                            $basico = $gastototal * .85;
                            $premiumpospago = $gastototal;
                            $premiumprepago = $gastototal  * .95;
                            $contrato = 0;
                            break;
                    }


                    $restricciones = consulta($conexion, "SELECT * FROM usuario_resticciones WHERE idUsuario = $idCliente");
                    if (empty($restricciones)) {
                        $premiumPrepagoDisponible = true;
                        $premiumPostpagoDisponible = false;
                        $basicoPostpagoDisponible = false;
                        $contratoDisponible = false;
                    } else {
                        $premiumPrepagoDisponible = filter_var($restricciones["precio_prepago_premium"], FILTER_VALIDATE_BOOLEAN);
                        $premiumPostpagoDisponible = filter_var($restricciones["precio_postpago_premium"], FILTER_VALIDATE_BOOLEAN);
                        $basicoPostpagoDisponible = filter_var($restricciones["precio_postpago_basico"], FILTER_VALIDATE_BOOLEAN);
                        $contratoDisponible = filter_var($restricciones["precio_contrato"], FILTER_VALIDATE_BOOLEAN);
                    }

                    $total = [
                        "basico" => [
                            "precio" => round($basico, 0),
                            "id" => 3,
                            "disponible" => $basicoPostpagoDisponible
                        ],
                        "premiumprepago" => [
                            "precio" => round($premiumprepago, 0),
                            "id" => 1,
                            "disponible" => $premiumPrepagoDisponible
                        ],
                        "premiumpospago" => [
                            "precio" => round($premiumpospago, 0),
                            "id" => 2,
                            "disponible" => $premiumPostpagoDisponible
                        ],
                        "contrato" => [
                            "precio" => round($contrato, 0),
                            "id" => 0,
                            "disponible" => $contratoDisponible
                        ],
                    ];

                    $total["metricas"] = $rowDatos;

                    //       $total["metricas"]= $rowDatos;  

                    array_push($payloadGastosInsert, "Operaciones", "datos" . $total);
                    respuesta(200, 200, "Resultados", $total);
                }
            }
        }
    }
} else {

    $payload = ["Faltantes" => $faltantes];
    respuesta(400, 400, "Hay un error con el servidor. Llama a central Error-TARE1", $payload);
}
