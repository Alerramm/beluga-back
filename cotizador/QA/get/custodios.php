<?PHP
//datos .env
include '../production.php';

//header
header('Access-Control-Allow-Origin: *');

//const 
$faltantes = [];

//datos Request
$datos = json_decode(file_get_contents('php://input'), true);
$km = $datos["kilometros"];


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
if ($km == "") {
    array_push($faltantes, 'Kilometros');
}

if (empty($faltantes)) {
    $tiempo = round(($km / 60), 0);

    switch (true) {
        case $tiempo <= 4:
            $total = 1850;
            break;
        case $tiempo == 5:
            $total = 3700;
            break;
        case $tiempo == 6:
            $total = 2950;
            break;
        case $tiempo == 7:
            $total = 5000;
            break;
        case $tiempo == 8:
            $total = 5650;
            break;
        case $tiempo == 9:
            $total = 6500;
            break;
        case $tiempo == 10:
            $total = 7000;
            break;
        case $tiempo == 11:
            $total = 7600;
            break;
        case $tiempo == 12:
            $total = 8250;
            break;
        case $tiempo == 13:
            $total = 8900;
            break;
        case $tiempo == 14:
            $total = 9550;
            break;
        case $tiempo > 14 || $tiempo <= 20:
            $total = 13450;
            break;
        default:
            $total = 0;
            break;
    }

    if ($tiempo > 20) {
        $total = 13450 + (($tiempo - 20) * 200);
    }

    //Response
    if ($total == 0) {
        respuesta(200, 404, "Servicio no disponible", []);
    } else {
        $payload =  [
            "total" => $total
        ];
        respuesta(200, 200, "Respuesta exitosa", $payload);
    }
} else {
    $payload = ["Faltantes" => $faltantes];
    respuesta(400, 400, "Hay un error con el servidor. Llama a central Error-CARE1", $payload);
}
