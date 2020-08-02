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

$payloadGastosInsert = [] ;
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

//Validacion de Datos
if ($km == "") {
    array_push($faltantes, 'km');
}
if ($casetas == "") {
    array_push($faltantes, 'casetas');
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
        $dieselPrecio =  "SELECT contennidoConstante FROM dbs304381.constantes WHERE idTipoConstante=1" ;
        $dieselresponse =  mysqli_query($conexion, $dieselPrecio);
        $row = mysqli_fetch_array($dieselresponse, MYSQLI_ASSOC);
        $dieselxdia = $row["contennidoConstante"];
        array_push($payloadGastosInsert, "Primera Consulta", "datos " . $dieselxdia );

        

        if (empty($row)) {
           
            array_push($payloadGastosInsert, "Fallo primera Consulta", "datos" . 1 );
            respuesta(200, 404, "No hay constante para ese ID" . 1, $payloadGastosInsert);
        } else {

            $consulta =  "SELECT * FROM dbs304381.Kilometros WHERE '$km' BETWEEN KilomInicial and KilomFinal" ;
            $datos =  mysqli_query($conexion, $consulta);
            $rowDatos = mysqli_fetch_array($datos, MYSQLI_ASSOC);
            array_push($payloadGastosInsert, "Segunda Consulta", "datos" . $rowDatos["idKilometros"] );
            if (empty($rowDatos)) {
                
                array_push($payloadGastosInsert, "Fallo  Segunda Consulta", "datos" . $km );
                respuesta(200, 404, "No metricas para este tipo de Kilometro ". $km, $payloadGastosInsert);
            }else {


                $costoDiaComision =  "SELECT contennidoConstante FROM dbs304381.constantes WHERE idTipoConstante=2" ;
                $costoDiaComisionresponse =  mysqli_query($conexion, $costoDiaComision);
                $row2 = mysqli_fetch_array($costoDiaComisionresponse, MYSQLI_ASSOC);
                $costoDiaComisionDato = $row2["contennidoConstante"];

                array_push($payloadGastosInsert, "Tercera Consulta", "datos " . $costoDiaComisionDato );

                if (empty($row2)) {
                    array_push($payloadGastosInsert, "Fallo Tercera Consulta", "datos" . 2 );
                    respuesta(200, 404, "No hay constante para ese ID" . 2, $payloadGastosInsert);
                } else {
                    $costoDiesel = ($km / $rowDatos["rendimiento"] ) * $dieselxdia;
                 
                    $comision = $rowDatos["numDias"] * $costoDiaComisionDato  ;
                    $costos = $costoDiesel + $casetas + $rowDatos["viaticos"] + $comision;
                    
                    array_push($payloadGastosInsert, "Primera Operacion", "datos" . $costos );
                    

                                $formula = ($costos/ 1.16) / $rowDatos["gastoPremium"];
                                    
                                if($idTipoAdecuacion === 3){
                                    $gastotal = $formula * 1.07;
                                }else{
                                    $gastotal = $formula;
                                }


                                array_push($payloadGastosInsert, "Segunda Operacion", "datos" . $gastotal );            

                                array_push($payloadGastosInsert, "Tipo de Unidad: ", "datos: " . $idTipoUnidad );   

                                switch(true){
                                    case($idTipoUnidad == 1):
                                    $gastototal= $gastotal * .88;
                                    $basico= $gastotal * .85;
                                    $premiumpospago= $gastotal;
                                    $premiumprepago =$gastotal  * .95;
                                    $contrato = 0;
                                        

                                        
                                    break;

                                    case($idTipoUnidad == 2):
                                        $basico= $gastotal * .85;
                                        $premiumprepago =$gastotal  * .95;
                                        $premiumpospago= $gastotal;
                                        $contrato = 0;  
                                    

                                    

                                    
                                    break;
                                    case($idTipoUnidad == 3):
                                        $gastototal= $gastotal * 1.12;
                                        $basico= $gastotal * .85;
                                        $premiumpospago= $gastotal;
                                        $premiumprepago =$gastotal  * .95;
                                        $contrato = 0;
                                        


                                   
                                    break;
                                    case($idTipoUnidad == 4):
                                        $gastototal= ($gastotal * 1.12) * 1.2 ;
                                        $basico= $gastotal * .85;
                                        $premiumpospago= $gastotal;
                                        $premiumprepago =$gastotal  * .95;
                                        $contrato = 0;
                                        


                                  
                                    break;
                                    case($idTipoUnidad == 5):
                                        $gastototal= ($gastotal * 1.12) * 1.12 ;
                                        $basico= $gastotal * .85;
                                        $premiumpospago= $gastotal;
                                        $premiumprepago =$gastotal  * .95;
                                        $contrato = 0;
                                        



                                    
                                    break;

                                 
                                }
                                $total = [
                                    "basico" =>[
                                    "precio" => $basico,
                                    "id" =>3
                                    ],


                                    "premiumprepago" =>[
                                        "precio" => $premiumprepago,
                                        "id" =>1
                                    ],

                                        "premiumpospago" =>[
                                            "precio" => $premiumpospago,
                                            "id" =>2],

                                      
                                            
                                            ];

                                            $total["metricas"]=$rowDatos;



                   

                         //       $total["metricas"]= $rowDatos;  

                                array_push($payloadGastosInsert, "Operaciones", "datos" . $total );
                                respuesta(200, 200, "Resultados", $total);

                }
                
                

            }


        }



    }   

}else{

$payload = ["Faltantes" => $faltantes];
respuesta(400, 400, "Hay un error con el servidor. Llama a central Error-TARE1", $payload);

}
