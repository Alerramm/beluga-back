<?PHP
//datos .env
include '../production.php';

//header
header('Access-Control-Allow-Origin: *');
header("Content-Type: application/json; charset=UTF-8");


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




function get_horario($nombredia) {

  $dias = array('Domingo','Lunes','Martes','Miercoles','Jueves','Viernes','Sabado');
  $fecha = $dias[date('N', strtotime($nombredia))];


}


 //saber_dia('2015-03-13');

//const 
$faltantes = [];

//datos Request
$datos = json_decode(file_get_contents('php://input'), true);

//Viaje
$base_de_operaciones = $datos["base_de_operaciones"];
$cliente = $datos["cliente"];
$datosClienteTot = $datos["datosClienteTot"];
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
$checkFecha = $datos["checkValidDate"];
$multidestino = $datos["isMultidestiny"];
$routeName = $datos["routeName"];
$datosServiciosAdicionales = $datos["seviciosAdiconales"];
$productoRes = $datos["producto"];
$tipoCliente =$datos["tipoCliente"];

if ($tipoCliente == "") {
  array_push($faltantes, 'tipoCliente');
}
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
  array_push($faltantes, 'Cliente');
}
if ($datosClienteTot == "" &&  $cliente =="0"){
  array_push($faltantes, 'Datos del cliente');
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

  if (empty($faltantes)) {

    $mysqli = mysqli_init();
    $conexion = mysqli_connect($_SESSION['HOST'], $_SESSION['USER'], $_SESSION['PASS'], $_SESSION['DBNAME']);
    //Validacion conexion con bd
    if (!$conexion) {
      respuesta(500, 500, "Hay un error con el servidor. Llama a central Error-CLBD1", []);
    } else {

    if($cliente =="0"){

      $nombreCliente=$datosClienteTot["datosCliente"]["nombrePropietario"];
      $rfc=$datosClienteTot["datosCliente"]["rfc"];
      $telefono=$datosClienteTot["datosCliente"]["telefono"];
      $email=$datosClienteTot["datosCliente"]["email"];
      $contacto=$datosClienteTot["datosCliente"]["contacto"];
      $razonSocial=$datosClienteTot["datosCliente"]["razonSocial"];

      $insertV =  "INSERT INTO clientes (nombre,rfc,telefono, email, contacto, razonSocial,tipoCliente)  VALUES ('$nombreCliente','$rfc','$telefono','$email','$contacto','$razonSocial','$tipoCliente')";

      if ($conexion->query($insertV) === TRUE) {
        $last_id = $conexion->insert_id;
        
        $payloadGastosInsert [] = ["contactoInsert" => " Exito al registrar cliente" . $last_id];

        $idClienteFinal=$last_id;
    
    
    
                $contacto=$datosClienteTot["contactoGeneral"]["contacto"];
                $telefono=$datosClienteTot["contactoGeneral"]["telefono"];
                $correo=$datosClienteTot["contactoGeneral"]["correo"];
    
                $insertV =  "INSERT INTO contactoCliente (idCliente ,tipoContacto ,contacto , telefono , correo )  VALUES ('$idClienteFinal','1','$contacto','$telefono','$correo')";
    
                if ($conexion->query($insertV) === TRUE) {
                  $last_id = $conexion->insert_id;
                  $payloadGastosInsert [] = ["contactoInsert" => " Exito al insertar contacto " . $last_id];
                } else {
                  $payloadGastosInsert [] = ["contactoInsert" => " Error al insertar contacto " . $last_id];
                }
    
                $contacto=$datosClienteTot["contactoCarga"]["contacto"];
                $telefono=$datosClienteTot["contactoCarga"]["telefono"];
                $correo=$datosClienteTot["contactoCarga"]["correo"];
    
                $insertV =  "INSERT INTO contactoCliente (idCliente ,tipoContacto ,contacto , telefono , correo )  VALUES ('$idClienteFinal','2','$contacto','$telefono','$correo')";
                if ($conexion->query($insertV) === TRUE) {
                  $last_id = $conexion->insert_id;
                  $payloadGastosInsert [] = ["contactoInsert" => " Exito al insertar contactoCliente " . $last_id];
                } else {
                  $payloadGastosInsert [] = ["contactoInsert" => " Error al insertar contactoCliente " . $last_id];
                }
    
                $contacto=$datosClienteTot["contactoEntrega"]["contacto"];
                $telefono=$datosClienteTot["contactoEntrega"]["telefono"];
                $correo=$datosClienteTot["contactoEntrega"]["correo"];
    
                $insertV =  "INSERT INTO contactoCliente (idCliente ,tipoContacto ,contacto , telefono , correo )  VALUES ('$idClienteFinal','3','$contacto','$telefono','$correo')";
                if ($conexion->query($insertV) === TRUE) {
                  $last_id = $conexion->insert_id;
                  $payloadGastosInsert [] = ["contactoInsert" => " Exito al insertar contactoCliente " . $last_id];
                } else {
                  $payloadGastosInsert [] = ["contactoInsert" => " Error al insertar contactoCliente " . $last_id];
                }


               // $dateLocal=date ( string $format [, int $timestamp = time() ] ) : string;
                $insertV =  "INSERT INTO usuarios (id	 ,usuario , password , nombre, mail, fechaAlta ,perfil)  VALUES ('$idClienteFinal','$email','123456','$nombreCliente','$email','$dateLocal','CLIENTE')";
                if ($conexion->query($insertV) === TRUE) {
                  $last_id = $conexion->insert_id;
                  $payloadGastosInsert [] = ["contactoInsert" => " Exito al insertar usuario" . $last_id];
                } else {
                  $payloadGastosInsert [] = ["contactoInsert" => " Error al insertar usuario " . $last_id];
                }
            

                //Termina Enrollamiento
                                                  $fechaEntregaTemporal = "aun nose";
                                                  $entrega = "aun nose";
                                                  $estatus = "Solicitudx";
                                                  $insertV =  "INSERT INTO viajes VALUES 
                                                        (null,'$base_de_operaciones','$nombreCliente','','$routeName','$fecha_salida','$fecha_carga','$fechaEntregaTemporal',
                                                        '$fecha_disponibilidad','$tipoDeUnidad','$tonelaje','$unidad','','$numero_de_tramos','$diesel','$total_distancia',
                                                        '$ejes','$total_casetas','$total_tiempo','$total_tiempo_formato','$isRoundTrip','$checkFecha','$multidestino','',false,'$estatus','Pendientex')";

                                                    if ($conexion->query($insertV) === TRUE) {
                                                      $last_id = $conexion->insert_id;
                                                      $payloadGastosInsert [] = ["ViajeInsert" => " Exito New Travel record created successfully " . $last_id];

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
                                                      '$observaciones','$waypoints','Pendiente')";
                                                      if ($conexion->query($insertT) === TRUE) {
                                                        $last_id_trip = $conexion->insert_id;
                                                        $payloadGastosInsert [] = ["tramosInsert" => " Exito New Travel record created successfully " . $last_id_trip];

                                                          foreach ($casetasToll[$contTramos - 1] as &$casetas) {
                                                            $name = $casetas["name"];
                                                            $costoC = $casetas["cashCost"];
                                                            $insertCasetas = "INSERT INTO casetas(id_tramo, nombre, precio) VALUES ($last_id_trip,'$name','$costoC')";
                                                            if ($conexion->query($insertCasetas) === TRUE) {

                                                              $payloadGastosInsert [] = ["casetasInsert" => " Exito New Travel record created successfully " . $indexRoute];
                                                            } else {

                                                              $payloadGastosInsert [] = ["casetasInsert" => " Error al insertar caseta " . $indexRoute];
                                                            }
                                                          }

                                                          $payloadGastosInsert [] = ["TramosCompleto" => " Tramos agregados success " . $last_id_trip];

                                                      }else{
                                                          $payloadGastosInsert [] = ["tramosInsert" => " Error al insertar tramo " . $last_id];
                                                      }
                                                    }



                                                            $payloadGastosInsert [] = ["TramosCompleto" => " Tramos agregados Error " . $last_id_trip];


                                                            //Update
                                                            $insertU =  "UPDATE viajes SET fecha_entrega='$fechaEntregaTemporal',destino='$entrega',ruta='$entregaTemporal',tiempo ='$total_tiempo' WHERE id = $idviaje;";
                                                            if($multidestino){
                                                              $insertU =  "UPDATE viajes SET fecha_entrega='$fechaEntregaTemporal',destino='Multidestino',tiempo ='$total_tiempo' WHERE id = $idviaje;";
                                                            }
                                                            if ($conexion->query($insertU) === TRUE) {
                                                                    $payloadGastosInsert [] = ["updateSuccess" => " Exito al actualizar viaje" . $insert_id];

                                                                  //aqui empieza nuevo flujo
                                                                  $insertT =  "INSERT INTO serviciosAdicionales (idViaje) VALUES ($idviaje)";
                                                                  if ($conexion->query($insertT) === TRUE) { 
                                                                      $last_id = $conexion->insert_id;  
                                                                      $payloadGastosInsert [] = ["servAdInsert" => " Exito al insertar Servicios Adicionales " . $insert_id];



                                                                      $monto=$datosServiciosAdicionales["mercanciaAsegurada"]["monto"];
                                                                      $precio=$datosServiciosAdicionales["mercanciaAsegurada"]["precio"];

                                                                      $insertT =  "INSERT INTO mercanciaAsegurada (idServicioAdicional,monto,precio) VALUES ($last_id,$monto,$precio )";
                                                                      if ($conexion->query($insertT) === TRUE) { 
                                                                          $payloadGastosInsert [] = ["servAdMercanciaInsert" => " Exito New Travel record created successfully " . $insert_id];
                                                                      }else{
                                                                          $payloadGastosInsert [] = ["servAdMercanciaInsert" => " Error al insertar Mercan " . $insert_id]; 
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


                                                                      //aqui Continua el flujo parte 2






                                                                  }else{
                                                                    $payloadGastosInsert [] = ["servAdInsert" => " Error al insertar Servicios Adicionales " . $insert_id];

                                                                  }

                                                          }else{
                                                            $payloadGastosInsert [] = ["updateSuccess" => " Error al actualizar Viaje " . $insert_id];
                                                          }        



                                                    } else { 
                                                      $payloadGastosInsert [] = ["ViajeInsert" => " Error al insertar viaje " . $last_id];
                                                    }  

                                                    respuesta(200, 200, "Todo Chido", $payloadGastosInsert); 
                                                    }                     
                                                  }

    
    
              } else {
                $payloadGastosInsert [] = ["contactoInsert" => " Error al insertar cliente " . $indexRoute];
                respuesta(500, 500, "Error al insertar cliente", $payloadGastosInsert);
              }       



    }else{
      
      
      $consulta =  "SELECT nombre FROM clientes where id = '$cliente' ";
      $selectCliente =  mysqli_query($conexion, $consulta);
      if ($conexion->query($consulta) === TRUE) {
        if($selectCliente ==""){
          $payloadGastosInsert [] = ["Select cliente" => " No existe ID cliente" . $selectCliente];
          respuesta(404, 404, "Error al seleccionar cliente", $payloadGastosInsert);
        

       
                             
                                          $fechaEntregaTemporal = "aun nose";
                                          $entrega = "aun nose";
                                          $estatus = "Solicitudx";
                                          $insertV =  "INSERT INTO viajes VALUES 
                                                (null,'$base_de_operaciones','$selectCliente','','$routeName','$fecha_salida','$fecha_carga','$fechaEntregaTemporal',
                                                '$fecha_disponibilidad','$tipoDeUnidad','$tonelaje','$unidad','','$numero_de_tramos','$diesel','$total_distancia',
                                                '$ejes','$total_casetas','$total_tiempo','$total_tiempo_formato','$isRoundTrip','$checkFecha','$multidestino','',false,'$estatus','Pendientex')";

                                            if ($conexion->query($insertV) === TRUE) {
                                              $last_id = $conexion->insert_id;
                                              $payloadGastosInsert [] = ["ViajeInsert" => " Exito New Travel record created successfully " . $last_id];

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
                                              '$observaciones','$waypoints','Pendiente')";
                                              if ($conexion->query($insertT) === TRUE) {
                                                $last_id_trip = $conexion->insert_id;
                                                $payloadGastosInsert [] = ["tramosInsert" => " Exito New Travel record created successfully " . $last_id_trip];

                                                  foreach ($casetasToll[$contTramos - 1] as &$casetas) {
                                                    $name = $casetas["name"];
                                                    $costoC = $casetas["cashCost"];
                                                    $insertCasetas = "INSERT INTO casetas(id_tramo, nombre, precio) VALUES ($last_id_trip,'$name','$costoC')";
                                                    if ($conexion->query($insertCasetas) === TRUE) {

                                                      $payloadGastosInsert [] = ["casetasInsert" => " Exito New Travel record created successfully " . $indexRoute];
                                                    } else {

                                                      $payloadGastosInsert [] = ["casetasInsert" => " Error al insertar caseta " . $indexRoute];
                                                    }
                                                  }

                                                  $payloadGastosInsert [] = ["TramosCompleto" => " Tramos agregados success " . $last_id_trip];

                                              }else{
                                                  $payloadGastosInsert [] = ["tramosInsert" => " Error al insertar tramo " . $last_id];
                                              }



                                                    $payloadGastosInsert [] = ["TramosCompleto" => " Tramos agregados Error " . $last_id_trip];


                                                    //Update
                                                    $insertU =  "UPDATE viajes SET fecha_entrega='$fechaEntregaTemporal',destino='$entrega',ruta='$entregaTemporal',tiempo ='$total_tiempo' WHERE id = $idviaje;";
                                                    if($multidestino){
                                                      $insertU =  "UPDATE viajes SET fecha_entrega='$fechaEntregaTemporal',destino='Multidestino',tiempo ='$total_tiempo' WHERE id = $idviaje;";
                                                    }
                                                    if ($conexion->query($insertU) === TRUE) {
                                                            $payloadGastosInsert [] = ["updateSuccess" => " Exito al actualizar viaje" . $insert_id];

                                                          //aqui empieza nuevo flujo
                                                          $insertT =  "INSERT INTO serviciosAdicionales (idViaje) VALUES ($idviaje)";
                                                          if ($conexion->query($insertT) === TRUE) { 
                                                              $last_id = $conexion->insert_id;  
                                                              $payloadGastosInsert [] = ["servAdInsert" => " Exito al insertar Servicios Adicionales " . $insert_id];



                                                              $monto=$datosServiciosAdicionales["mercanciaAsegurada"]["monto"];
                                                              $precio=$datosServiciosAdicionales["mercanciaAsegurada"]["precio"];

                                                              $insertT =  "INSERT INTO mercanciaAsegurada (idServicioAdicional,monto,precio) VALUES ($last_id,$monto,$precio )";
                                                              if ($conexion->query($insertT) === TRUE) { 
                                                                  $payloadGastosInsert [] = ["servAdMercanciaInsert" => " Exito New Travel record created successfully " . $insert_id];
                                                              }else{
                                                                  $payloadGastosInsert [] = ["servAdMercanciaInsert" => " Error al insertar Mercan " . $insert_id]; 
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


                                                             // $insertT =  "INSERT INTO custodia (idServicioAdicional,km,precio) VALUES ($last_id,$km,$precio)";

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


                                                              //aqui Continua el flujo parte 2







                                                  }else{
                                                    $payloadGastosInsert [] = ["updateSuccess" => " Error al actualizar Viaje " . $insert_id];
                                                  }        



                                            } else { 
                                              $payloadGastosInsert [] = ["ViajeInsert" => " Error al insertar viaje " . $last_id];
                                            }  
          
                                            respuesta(200, 200, "Todo Chido", $payloadGastosInsert); 
                                               
                                            
            
      }else{
        $payloadGastosInsert [] = ["Select cliente" => " Error al buscar cliente" . $cliente];
        respuesta(404, 404, "Error al seleccionar cliente", $payloadGastosInsert);
      } 
    } 
    
  }else{
      $payload = ["Faltantes" => $faltantes];
      respuesta(400, 400, "Hay un error con el servidor. Llama a central Error-TARE1", $payloadGastosInsert);
  }