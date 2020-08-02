<?PHP
//datos .env
include '../production.php';

//header
header('Access-Control-Allow-Origin: *');

//const 
$faltantes = [];

//datos Request
$datos = json_decode(file_get_contents('php://input'), true);


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





    $validator=1;
    $fecha = date('Y-m-d H:i:s');
  
    $dias = array('Domingo','Lunes','Martes','Miercoles','Jueves','Viernes','Sabado');
      $fecha = $dias[date('N', strtotime($fecha))];

      $payloadGastosInsert[] = ["Horario" => "Fecha" . $fecha];

    if($fecha == "Domingo"){
        $payloadGastosInsert[] = ["Horario" => "Fecha entra en IF" . $fecha];

  
    }else{
   
         $hoy = getdate();
         $payloadGastosInsert[] = ["Horario" => "Fecha else " . $hoy];

       
        $hora= $hoy['hours'];

        $payloadGastosInsert[] = ["Horario" => "Hora Else  " . $hora];

        
          if($hora < 8 && $hora > 18   ){
            $validator=1;
            }else{
              $validator=0;
            }    
    }
  




    $payload = ["Faltantes" => $faltantes];
    respuesta(200, 200, "prueba", $payload);

