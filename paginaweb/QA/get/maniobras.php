<?PHP
//datos .env
include '../production.php';

//header
header('Access-Control-Allow-Origin: *');

//const 
$faltantes = [];

//datos Request
$datos = json_decode(file_get_contents('php://input'), true);
$cantmaniobristas = $datos["cantmaniobristas"];
$horasadicionales = $datos["horasadicionales"];


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
if ($cantmaniobristas == "") {
    array_push($faltantes, 'cantmaniobristas');
}

if (empty($faltantes)) {

    $horasadicionalestotal=$horasadicionales*100;
    $totalmaniobrista= 550*$cantmaniobristas;

    if($horasadicionales==""){
        $horasadicionales=0;
    }
    $subtotal= $totalmaniobrista+$horasadicionalestotal;

    $total = [
     //   "total" => $subtotal
        "total" => "500"

    ];
    // //Conexion a base de datos
    // $mysqli = mysqli_init();
    // $conexion = mysqli_connect($_SESSION['HOST'], $_SESSION['USER'], $_SESSION['PASS'], $_SESSION['DBNAME']);
    // //Validacion conexion con bd
    // if (!$conexion) {
    //     respuesta(500, 500, "Hay un error con el servidor. Llama a central Error-CABD1", []);
    // } else {
    //     //configuracon db
    //     mysqli_query($conexion, "SET CHARACTER SET 'utf8'");
    //     mysqli_query($conexion, "SET SESSION collation_connection ='utf8_unicode_ci'");

    //     //Analisis de la informacion
    //     $consulta =  "SELECT nombreUnidad FROM unidadesNueva where importe = '$importe' ";

    //     $selectUnidades =  mysqli_query($conexion, $consulta);
    //     while ($row = $selectUnidades->fetch_array(MYSQLI_ASSOC)) {
    //         $Unidades[] = $row;
    //     }

         //Response
         if (empty($total)) {
             respuesta(200, 404, "No es una cantidad Valida", []);
         } else {
             respuesta(200, 200, "Respuesta exitosa", $total);
         }
     



} else {
    $payload = ["Faltantes" => $faltantes];
    respuesta(400, 400, "Hay un error con el servidor. Llama a central Error-CARE1", $payload);
}
