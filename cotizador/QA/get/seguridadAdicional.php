<?PHP
//datos .env
include '../production.php';

//header
header('Access-Control-Allow-Origin: *');

//const 
$faltantes = [];

//datos Request
$datos = json_decode(file_get_contents('php://input'), true);
$tipoSeguridad = $datos["tipoSeguridad"];



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
if ($tipoSeguridad == "") {
    array_push($faltantes, 'tipoSeguridad');
}


if (empty($faltantes)) {


    if ($tipoSeguridad == 'Forte' ){

        $tipo = 'Forte';
        $precio = 450;
    }elseif($tipo == 'Elemental'){

        $tipo = 'Elemental';
        $precio = 0;
    }



        $totalArray = ["tipo: " => $tipo, "precio" => $precio];


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
