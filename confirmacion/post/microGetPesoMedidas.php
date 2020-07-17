<?PHP
//datos .env
include '../production.php';

//header
header('Access-Control-Allow-Origin: *');

//const 
$faltantes = [];

//datos Request
$datos = json_decode(file_get_contents('php://get'), true);

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








    $medidas = [
     
        "MAXIMO_LARGO_METROS" => "20",
        "MAXIMO_ANCHO_METROS" => "3",
        "MAXIMO_ALTO_METROS" => "4.1",
        
    ];



    $PESO = [
           "1.5" ,
           "3.5" ,
           "5.5" ,
           "10" ,
           "15" ,
           "25" ];

        $total = ["PESO" =>$PESO, "MEDIDAS" => $medidas ];

           
           

         if (empty($total)) {
             respuesta(200, 404, "No es una cantidad Valida", []);
         } else {
             respuesta(200, 200, "Respuesta exitosa", $total);
         }
     



