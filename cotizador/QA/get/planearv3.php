<?PHP
$datos = json_decode(file_get_contents('php://input'), true);

include '../production.php';
/* echo json_encode($data); */
header('Access-Control-Allow-Origin: *');
header("Content-Type: application/json; charset=UTF-8");
//Conexion a base de datos
$mysqli = mysqli_init();
$conexion = mysqli_connect($_SESSION['HOST'], $_SESSION['USER'], $_SESSION['PASS'], $_SESSION['DBNAME']);
//$conexion = mysqli_connect("localhost", "root", "", "dbo574183143");
//variables globales
mysqli_set_charset($conexion, 'utf8');
$respuestaFinal = 0;
$respuestaFinalViaje = 0;
$respuestaFinalUpdate = 0;
$last_id = 0;
$faltantes = [];



$base_de_operaciones = $datos["base_de_operaciones"];
$cliente = $datos["cliente"];
$diesel = $datos["diesel"];
$fecha_salida_unix = strtotime('-5 hour', strtotime($datos["fecha_salida"]));
$fecha_salida = gmdate("Y-m-d\TH:i:s\Z", $fecha_salida_unix);
$fecha_carga_unix = strtotime('-5 hour', strtotime($datos["fecha_carga"]));
$fecha_carga = gmdate("Y-m-d\TH:i:s\Z", $fecha_carga_unix);
$fecha_disponibilidad_unix = strtotime('-5 hour', strtotime($datos["fecha_disponibilidad"]));
$fecha_disponibilidad = gmdate("Y-m-d\TH:i:s\Z", $fecha_disponibilidad_unix);
$isRoundTrip = $datos["isRoundTrip"];
$unidad = $datos["unidad"];
$numero_de_tramos = $datos["numero_de_tramos"] + 1;
$tipoDeUnidad = $datos["unidad"];
$tonelaje = $datos["tipoDeAdecuacion"];
$total_casetas = $datos["total_casetas"];
$total_distancia = $datos["total_distancia"] / 1000;
$ejes =  $datos["ejes"];
$total_tiempo = 0;
$total_tiempo_formato =  $datos["total_tiempo"];
//$operador = $datos["operador"];
$checkFecha = $datos["checkValidDate"];
$multidestino = $datos["isMultidestiny"];
$routeName = $datos["routeName"];
$datosServiciosAdicionales = $datos["seviciosAdiconales"];
$productoRes = $datos["producto"];


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

/*
if ($productoRes == "") {
    array_push($faltantes, 'productoRes');
  }
  if ($datosServiciosAdicionales == "") {
    array_push($faltantes, 'datosServiciosAdicionales');
  }
  if ($base_de_operaciones == "") {
    array_push($faltantes, 'Base de Operaciones');
  }
  if ($cliente == "") {
    array_push($faltantes, 'cliente');
  }
  if ($diesel == "") {
    array_push($faltantes, 'Disel');
  }
  if ($ejes == "") {
    array_push($faltantes, 'Ejes');
  }
  if ($fecha_salida == "") {
    array_push($faltantes, 'Fecha de Salida');
  }
  if ($fecha_carga == "") {
    array_push($faltantes, 'Fecha de Carga');
  }
  if ($fecha_disponibilidad == "") {
    array_push($faltantes, 'Fecha de Disponibilidad');
  }
  if ($unidad == "") {
    array_push($faltantes, 'Unidad');
  }
  if ($tipoDeUnidad == "") {
    array_push($faltantes, 'Tipo de Unidad');
  }
  if ($tonelaje == "") {
    array_push($faltantes, 'Tonelaje');
  }
  if ($total_distancia == "") {
    array_push($faltantes, 'Distancia');
  }
  if ($total_tiempo_formato == "") {
    array_push($faltantes, 'Tiempo Formato');
  }


*/

 // if (!empty($faltantes)) {  



        //$totalDistanceDisplay = $datos["totalDistanceDisplay"];
        $fechaEntregaTemporal = "aun nose";
        $entrega = "aun nose";
        $estatus = "Solicitudxxp";

        /*  $insertV = "INSERT INTO viaje VALUES 
        (null,'$base_de_operaciones','$cliente','$diesel','$fecha_salida','$fecha_carga','$fecha_disponibilidad','$fechaEntregaTemporal',
        '$isRoundTrip','$unidad','$numero_de_tramos','$tipoDeUnidad','$tonelaje','$total_casetas','$total_distancia',
        '$ejes','$total_tiempo','$total_tiempo_formato','$operador','$entrega','$estatus','$checkFecha','')"; */

        $insertV =  "INSERT INTO viajes VALUES 
                (null,'$base_de_operaciones','$cliente','','$routeName','$fecha_salida','$fecha_carga','$fechaEntregaTemporal',
                '$fecha_disponibilidad','$tipoDeUnidad','$tonelaje','$unidad','$operador','$numero_de_tramos','$diesel','$total_distancia',
                '$ejes','$total_casetas','$total_tiempo','$total_tiempo_formato','$isRoundTrip','$checkFecha','$multidestino','$saveTrip',
                '$estatus')";

        //Insercion de viaje
        if ($conexion->query($insertV) === TRUE) {
            $last_id = $conexion->insert_id;
            $respuestaV = 200;
            $dataV[] = " Exito New Travel record created successfully " . $last_id;

            if ($respuestaV == 500) {
                $respuestaFinalViaje = 500;
              } else {
                $respuestaFinalViaje = $respuestaV;
              }

              $dataFinal[] = [
                "statusTrip" => $respuestaFinalViaje
              ];
              $dataFinal["viaje"] = $dataV;

              

     
              //Insercion de 
              $contTramos = 1;

              foreach ($datos["rutas"] as &$valor) {
                $indexRoute = $contTramos;
                $casetas = $valor["casetas"];
                $destino = $valor["destino"];
                $distancia =  round($valor["distancia"]) / 1000;
                $fecha_unix = strtotime('-5 hour', strtotime($valor["fecha"]));
                $fecha = gmdate("Y-m-d\TH:i:s\Z", $fecha_unix);
                if ($indexRoute < 3) {
                  $entrega = $valor["ciudad"];
                  $entregaTemporal = $valor["destino"];
                  $fechaEntregaTemporal = $fecha;
                }
                $fechaLabel = $valor["fechaLabel"];
                $load_time = $valor["load_time"];
                $tiempo = $valor["tiempo"];
                $total_tiempo = $total_tiempo + $distancia / 60 + $load_time;
                $origen = $valor["origen"];
                $waypoints = $valor["waypoints"];
                $ciudad =  $valor["ciudad"];
                $observaciones = $valor["observaciones"];
                $tipo = "aun nose";
                $idviaje = $last_id;
                /* $insertT =  "INSERT INTO tramos VALUES 
              (null,$indexRoute,'$casetas','$destino','$distancia','$fecha','$fechaLabel',' $load_time','$origen',
              '$tiempo','$waypoints','$tipo','$idviaje','$observaciones','Pendiente','','','$ciudad')"; */
                $insertT =  "INSERT INTO tramos VALUES 
                (null,'$idviaje',$indexRoute,'$fecha','$origen','$ciudad','$destino',' $load_time','$tiempo','$casetas','$distancia',
                '$observaciones','$waypoints','','','Confirmado')";
                if ($conexion->query($insertT) === TRUE) {
                  $respuesta = 200;
                  $data[] = "Index Route " . $indexRoute . " Exito New record created successfully";
                } else {
                  $respuesta = 500;
                  $res = "Error: " . $insertT . "<br>" . $conexion->error;
                  $data[] = "Index Route " . $indexRoute . $res . " Algo paso";
                }
                if ($respuesta == 500) {
                  $respuestaFinal = 500;
                }
                $contTramos = $contTramos + 1;
              }
              $total_tiempo = $total_tiempo - 1;

              //Update
              $insertU =  "UPDATE viajes SET fecha_entrega='$fechaEntregaTemporal',destino='$entrega',ruta='$entregaTemporal',tiempo ='$total_tiempo' WHERE id = $idviaje;";
              if($multidestino){
                $insertU =  "UPDATE viajes SET fecha_entrega='$fechaEntregaTemporal',destino='Multidestino',tiempo ='$total_tiempo' WHERE id = $idviaje;";
              }

              if ($conexion->query($insertU) === TRUE) {
                $dataU[] = " Exito Update record successfully";
              } else {
                $respuestaFinalUpdate == 500;
                $res = "Error: " . $insertU . "<br>" . $conexion->error;
                $dataU[] = $res . " Algo paso";
              }   
              
              
              if ($respuestaFinal == 0 && $respuestaFinalViaje == 200 && $respuestaFinalUpdate == 0) {
                $respuestaFinal = 200;
                $payloadGastosInsert [] = ["Viaje Agregado" => " Exito New Travel record created successfully " . $idviaje];

                $insertT =  "INSERT INTO serviciosAdicionales (idViaje) VALUES ($idviaje)";
                if ($conexion->query($insertT) === TRUE) { 
                    $last_id = $conexion->insert_id;  

                    $payloadGastosInsert [] = ["servAdInsert" => " Exito New Travel record created successfully " . $insert_id];


                    

                                    $monto=$datosServiciosAdicionales["mercanciaAsegurada"]["monto"];
                                    $precio=$datosServiciosAdicionales["mercanciaAsegurada"]["precio"];

                                    $insertT =  "INSERT INTO mercanciaAsegurada (idServicioAdicional,monto,precio) VALUES ($last_id,$monto,$precio )";
                                    if ($conexion->query($insertT) === TRUE) { 
                                        $payloadGastosInsert [] = ["servAdMercanciaInsert" => " Exito New Travel record created successfully " . $insert_id];
                                    }else{
                                        $payloadGastosInsert [] = ["servAdMercanciaInsert" => " Error al insertar Mercnai " . $insert_id]; 
                                        
                                    } 


                                    $numeroCarga=$datosServiciosAdicionales["maniobras"]["numeroCarga"];
                                    $numeroEntrega=$datosServiciosAdicionales["maniobras"]["numeroEntrega"];
                                    $precio=$datosServiciosAdicionales["maniobras"]["precio"];


                                    $insertT =  "INSERT INTO maniobras (idServicioAdicional,numeroCarga,numeroEntrega,precio) VALUES ($last_id,$numeroCarga,$numeroEntrega,$precio)";

                                    if ($conexion->query($insertT) === TRUE) { 
                                        $payloadGastosInsert [] = ["AgregarManiobras" => " Exito New Travel record created successfully " . $insert_id];
                                    }else{
                                        $payloadGastosInsert [] = ["AgregarManiobras" => " Error al insertar Maniobras " . $insert_id]; 

                                    } 

                                    $tipo=$datosServiciosAdicionales["seguridadAdicional"]["tipo"];
                                    $precio=$datosServiciosAdicionales["seguridadAdicional"]["precio"];

                                    $insertT =  "INSERT INTO seguridadAdicional (idServicioAdicional,tipo,precio) VALUES ($last_id,$tipo,$precio)";
                                    if ($conexion->query($insertT) === TRUE) { 
                                        $payloadGastosInsert [] = ["AgregarseguridadAdicional" => " Exito New Travel record created successfully " . $insert_id];

                                    }else{
                                        $payloadGastosInsert [] = ["AgregarseguridadAdicional" => " Error al insertar Seguridad Adicional " . $insert_id]; 

                                    } 

                                    $km=$datosServiciosAdicionales["custodia"]["km"];
                                    $precio=$datosServiciosAdicionales["custodia"]["precio"];


                                    $insertT =  "INSERT INTO custodia (idServicioAdicional,km,precio) VALUES ($last_id,$km,$precio)";

                                    if ($conexion->query($insertT) === TRUE) { 
                                        $payloadGastosInsert [] = ["Agregarcustodia" => " Exito New Travel record created successfully " . $insert_id];
                                    }else{

                                        $payloadGastosInsert [] = ["Agregarcustodia" => " Error al insertar custodia " . $insert_id]; 
                                    }




                                    $categoria=$productoRes["categoria"];
                                    $peso=$productoRes["peso"];
                                    $largo=$productoRes["medidas"]["largo"];
                                    $ancho=$productoRes["medidas"]["ancho"];
                                    $alto=$productoRes["medidas"]["alto"];
                                    $descripcion= $productoRes["descripcion"];
                                    

                                    $insertT =  "INSERT INTO productosByViaje (idViaje,peso,largo,ancho,alto,descripcion) VALUES ($idViaje,$peso,$largo,$ancho,$alto,$descripcion)";
                                    if ($conexion->query($insertT) === TRUE) { 
                                        $payloadGastosInsert [] = ["AgregarProductos" => " Exito New Travel record created successfully " . $insert_id];
                                    }else{

                                        $payloadGastosInsert [] = ["AgregarProductos" => " Error al insertar Productos " . $insert_id]; 
                                    }



                                $payloadGastosInsert [] = ["Servicios Adicionales Record OK" => " Exito New Travel record created successfully " . $insert_id];

                               

                 }else{
                    $payloadGastosInsert [] = ["servAdInsert" => " Error al insertar servAdicional " . $insert_id];  
                 }



              } else {
                    $respuestaFinal = 500;
                }
                $dataFinal[] = [
                    "statusRutas" => $respuestaFinal
                  ];
                  $dataFinal["rutas"] = $data;
                  $dataFinal["update"] = $dataU;
          //        http_response_code($respuestaFinal);

            //      echo json_encode($dataFinal);

            respuesta(200, 200, "Todo Chingon", $payloadGastosInsert);

        } else {
            $respuestaV = 500;
            $res = "Error: " . $insertV . "<br>" . $conexion->error;
            $dataV[] = $res . " Algo paso";
        }










/*
  }else {

    respuesta(400, 400, "Faltan datos", $faltantes);
  }

  */