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
$base = $datos["base"];
$destino = $datos["destino"];
$ruta = $datos["ruta"];
$fecha_salida_unix = strtotime('-5 hour', strtotime($datos["fecha_salida"]));
$fecha_salida = gmdate("Y-m-d\TH:i:s\Z", $fecha_salida_unix);
$fecha_carga_unix = strtotime('-5 hour', strtotime($datos["fecha_carga"]));
$fecha_carga = gmdate("Y-m-d\TH:i:s\Z", $fecha_carga_unix);
$fecha_entrega_unix = strtotime('-5 hour', strtotime($datos["fecha_entrega"]));
$fecha_entrega = gmdate("Y-m-d\TH:i:s\Z", $fecha_entrega_unix);
$fecha_disponibilidad_unix = strtotime('-5 hour', strtotime($datos["fecha_disponibilidad"]));
$fecha_disponibilidad = gmdate("Y-m-d\TH:i:s\Z", $fecha_disponibilidad_unix);
$unidad_tipo = $datos["unidad_tipo"];
$unidad_modelo = $datos["unidad_modelo"];
$unidad = $datos["unidad"];
$operador = $datos["operador"];
$tramos = $datos["tramos"] + 1;
$disel = $datos["disel"];
$distancia = $datos["distancia"] / 1000;
$ejes =  $datos["ejes"];
$casetas = $datos["casetas"];
$tiempo = $datos["tiempo"];
$tiempo_formato =  $datos["tiempo_formato"];
$redondo = $datos["redondo"];
$valida_fechas = $datos["valida_fechas"];
$multidestino = $datos["multidestino"];
$ruta_guardada = $datos["ruta_guardada"];
$estatus = $datos["estatus"];
$tipo_request = $datos["tipo_request"];
$id_update = $datos["id_update"];

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
if ($base_de_operaciones == "") {
    array_push($faltantes, 'base');
  }
  if ($cliente == "") {
    array_push($faltantes, 'cliente');
  }
  if ($destino == "") {
    array_push($faltantes, 'destino');
  }
  if ($ruta == "") {
    array_push($faltantes, 'ruta');
  }
  if ($fecha_salida == "") {
    array_push($faltantes, 'fecha_salida');
  }
  if ($fecha_carga == "") {
    array_push($faltantes, 'fecha_carga');
  }
  if ($fecha_entrega == "") {
    array_push($faltantes, 'fecha_entrega');
  }
  if ($fecha_disponibilidad == "") {
    array_push($faltantes, 'fecha_disponibilidad');
  }
  if ($unidad_tipo == "") {
    array_push($faltantes, 'unidad_tipo');
  }
  if ($unidad_modelo == "") {
    array_push($faltantes, 'unidad_modelo');
  }
  if ($unidad == "") {
    array_push($faltantes, 'unidad');
  }
  if ($operador == "") {
    array_push($faltantes, 'Operador');
  }
  if ($tramos == "") {
    array_push($faltantes, 'tramos');
  }
  if ($disel == "") {
    array_push($faltantes, 'disel');
  }
  if ($distancia == "") {
    array_push($faltantes, 'distancia');
  }
  if ($ejes == "") {
    array_push($faltantes, 'ejes');
  }
  if ($casetas == "") {
    array_push($faltantes, 'casetas');
  }
  if ($tiempo == "") {
    array_push($faltantes, 'tiempo');
  }
  if ($tiempo_formato == "") {
    array_push($faltantes, 'tiempo_formato');
  }
  


if (empty($faltantes)) {
    //Conexion a base de datos
    $mysqli = mysqli_init();
    $conexion = mysqli_connect($_SESSION['HOST'], $_SESSION['USER'], $_SESSION['PASS'], $_SESSION['DBNAME']);
    //Validacion conexion con bd
    if (!$conexion) {
        respuesta(500, 500, "Hay un error con el servidor. Llama a central Error-VIAJE-BD-CONEXION", []);
    } else {
        //configuracon db
        mysqli_query($conexion, "SET CHARACTER SET 'utf8'");
        mysqli_query($conexion, "SET SESSION collation_connection ='utf8_unicode_ci'");

        //Analisis de la informacion
         //Insert
         $query =  "INSERT INTO viajes VALUES 
         (null,'$base','$cliente','$destino','$ruta','$fecha_salida','$fecha_carga','$fecha_entrega','$fecha_disponibilidad',
         '$unidad_tipo',''$unidad_modelo','$unidad','$operador','$tramos','$disel','$distancia','$ejes','$casetas','$tiempo',
         '$tiempo_formato','$redondo','$valida_fechas','$multidestino','$ruta_guardada','$estatus')";
         if($tipo_request == "Update"){
            $query =  "UPDATE viajes SET base='$base',cliente='$cliente',destino=$destino,ruta='$ruta',
            fecha_salida=$fecha_salida,fecha_carga='$fecha_carga',`fecha_entrega`='$fecha_entrega',
            fecha_disponibilidad='$fecha_disponibilidad',unidad_tipo='$unidad_tipo',unidad_modelo='$unidad_modelo',unidad='$unidad',
            operador='$operador',tramos='$tramos',diesel='$disel',distancia='$distancia',ejes='$ejes',
            casetas='$casetas',tiempo='$tiempo',tiempo_formato='$tiempo_formato',redondo='$redondo',valida_fechas='$valida_fechas',
            multidestino='$multidestino',ruta_guardada='$ruta_guardada',estatus='$estatus' WHERE id =$id_update";
         }

         if ($conexion->query($insert) === TRUE) {
             $payload = ["sql" => "Exito record successfully", "id" => $conexion->insert_id];

             respuesta(200, 200,  "Respuesta exitosa", $payload);
         } else {
             $payload = ["sql" => "Error: " . $query . "<br>" . $conexion->error];
             respuesta(500, 500,  "Hay un error con el servidor. Llama a central Error-VIAJE-INSERT", $payload);
         }
    }
} else {
    $payload = ["Faltantes" => $faltantes];
    respuesta(400, 400, "Hay un error con el servidor. Llama a central Error-TARE1", $payload);
}
