<?PHP
//datos .env
include '../production.php';

//header
header('Access-Control-Allow-Origin: *');
header("Content-Type: application/json; charset=UTF-8");

//const 
$faltantes = [];
$disel = 0;
$casetas = 0;
$alimentos = 0;
$comision = 0;
$estadias = 0;
$maniobras = 0;
$transito = 0;
$mantenimiento = 0;

//datos Request
$datos = json_decode(file_get_contents('php://input'), true);
$id = $datos["id"];

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
if ($id == "") {
    array_push($faltantes, 'id');
}

if (empty($faltantes)) {
    //Conexion a base de datos
    $mysqli = mysqli_init();
    $conexion = mysqli_connect($_SESSION['HOST'], $_SESSION['USER'], $_SESSION['PASS'], $_SESSION['DBNAME']);
    //Validacion conexion con bd
    if (!$conexion) {
        respuesta(500, 500, "Hay un error con el servidor. Llama a central Error-GABD1", []);
    } else {
        //configuracon db
        mysqli_query($conexion, "SET CHARACTER SET 'utf8'");
        mysqli_query($conexion, "SET SESSION collation_connection ='utf8_unicode_ci'");

        //Analisis de la informacion
        $datosDesgloseGastos =  mysqli_query($conexion, "SELECT diesel, pagoDie, folDiesel, casetas, pagoCas, folCasetas, alimentos, pagoAli, folAlimentos, comision, pagoCom, folComision, estadias, pagoEst, folEstadias, maniobras, pagoMan, folManiobras, transito, pagoTra, folTransito, mantenimiento, pagoMto, folMante, observacion, status FROM desgloseGastos where idViaje = '$id'");
        while ($row = $datosDesgloseGastos->fetch_array(MYSQLI_ASSOC)) {
            $disel = $disel + $row["diesel"];
            $casetas = $casetas + $row["casetas"];
            $alimentos = $alimentos + $row["alimentos"];
            $comision = $comision + $row["comision"];
            $estadias = $estadias + $row["estadias"];
            $maniobras = $maniobras + $row["maniobras"];
            $transito = $transito + $row["transito"];
            $mantenimiento = $mantenimiento + $row["mantenimiento"];
            if ($row["diesel"] != "0.00") {
                $data[] = [
                    "folio" => $row["folDiesel"],
                    "concepto" => "disel",
                    "monto" => $row["diesel"],
                    "tipo" =>  $row["pagoDie"]
                ];
            }
            if ($row["casetas"] != "0.00") {
                $data[] = [
                    "folio" => $row["folCasetas"],
                    "concepto" => "casetas",
                    "monto" => $row["casetas"],
                    "tipo" =>  $row["pagoCas"]
                ];
            }
            if ($row["alimentos"] != "0.00") {
                $data[] = [
                    "folio" => $row["folAlimentos"],
                    "concepto" => "alimentos",
                    "monto" => $row["alimentos"],
                    "tipo" =>  $row["pagoAli"]
                ];
            }
            if ($row["comision"] != "0.00") {
                $data[] = [
                    "folio" => $row["folComision"],
                    "concepto" => "comision",
                    "monto" => $row["comision"],
                    "tipo" =>  $row["pagoCom"]
                ];
            }
            if ($row["estadias"] != "0.00") {
                $data[] = [
                    "folio" => $row["pagoEst"],
                    "concepto" => "estadias",
                    "monto" => $row["estadias"],
                    "tipo" =>  $row["folEstadias"]
                ];
            }
            if ($row["maniobras"] != "0.00") {
                $data[] = [
                    "folio" => $row["folManiobras"],
                    "concepto" => "maniobras",
                    "monto" => $row["maniobras"],
                    "tipo" =>  $row["pagoMan"]
                ];
            }
            if ($row["transito"] != "0.00") {
                $data[] = [
                    "folio" => $row["folTransito"],
                    "concepto" => "transito",
                    "monto" => $row["transito"],
                    "tipo" =>  $row["pagoTra"]
                ];
            }
            if ($row["mantenimiento"] != "0.00") {
                $data[] = [
                    "folio" => $row["folMante"],
                    "concepto" => "mantenimiento",
                    "monto" => $row["mantenimiento"],
                    "tipo" =>  $row["pagoMto"]
                ];
            }
        }

        //Response
        if (empty($data)) {
            respuesta(200, 404, "Este viaje no tiene depositos", []);
        } else {
            $payload = [
                "diselTotal" => $disel,
                "casetasTotal" => $casetas,
                "alimentosTotal" => $alimentos,
                "comisionTotal" => $comision,
                "estadiasTotal" => $estadias,
                "maniobrasTotal" => $maniobras,
                "transitoTotal" => $transito,
                "mantenimientoTotal" => $mantenimiento,
                "depositos" => $data
            ];
            respuesta(200, 200, "Respuesta exitosa", $payload);
        }
    }
} else {
    $payload = ["Faltantes" => $faltantes];
    respuesta(400, 400, "Hay un error con el servidor. Llama a central Error-GARE1", $payload);
}
