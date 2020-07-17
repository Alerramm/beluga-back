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
$card = $datos["card"];
$month = $datos["month"];
$year = $datos["year"];
$cvv = $datos["cvv"];

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
if ($card == "") {
    array_push($faltantes, 'card');
}
if ($month == "") {
    array_push($faltantes, 'month');
}

if ($year == "") {
    array_push($faltantes, 'year');
}
if ($cvv == "") {
    array_push($faltantes, 'cvv');
}



if (empty($faltantes)) {

    switch ($card) {
        case '5031755734530604':
            if ($month === '11') {
                if ($year === '25') {
                    if ($cvv === '123') {
                        respuesta(200, 200, "Respuesta exitosa Mastercard", ["status" => "Aprobado"]);
                    }
                }
            }
            break;
        case '4170068810108020':
            if ($month === '11') {
                if ($year === '25') {
                    if ($cvv === '123') {
                        respuesta(200, 200, "Respuesta exitosa Visa", ["status" => "Aprobado"]);
                    }
                }
            }
            break;/* 
        case '371180303257522':
            if ($month === '11') {
                if ($year === '25') {
                    if ($cvv === '1234') {
                        respuesta(404, 404, "Respuesta exitosa", []);
                    }
                }
            }
            break; */
        default:
            respuesta(404, 404, "Transaccion Rechazada", ["status" => "Rechazado"]);
    }
} else {
    $payload = ["Faltantes" => $faltantes];
    respuesta(400, 400, "Hay un error con el servidor. Llama a central Error-TARE1", $payload);
}
