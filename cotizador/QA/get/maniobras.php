<?PHP
//datos .env
include '../production.php';

//header
header('Access-Control-Allow-Origin: *');

//const 
$faltantes = [];

//datos Request
$datos = json_decode(file_get_contents('php://input'), true);
$numeroCarga = $datos["numeroCarga"];
$numeroEntrega = $datos["numeroEntrega"];


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
if ($numeroCarga == "") {
    array_push($faltantes, 'numeroCarga');
}
if ($numeroEntrega == "") {
    array_push($faltantes, 'numeroEntrega');
}

if (empty($faltantes)) {

        $subtotal1 = $numeroCarga * 300;
        $subtotal2 = $numeroEntrega * 300;
        $total = $subtotal1 + $subtotal2 ;

        $totalArray = [
            "Total3: " => $total,

        ];


         //Response
         if (empty($datos)) {
             respuesta(200, 404, "No es una cantidad Valida", []);
         } else {
             respuesta(200, 200, "Respuesta exitosa", $totalArray);
         }

} else {
    $payload = ["Faltantes" => $faltantes];
    respuesta(400, 400, "Hay un error con el servidor. Llama a central Error-CARE1", $payload);
}
