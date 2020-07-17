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
    $dataFinal = ["headerResponse" => ["code" => $code, "mensaje" => $mensaje], "payload" => $payload];
    echo json_encode($dataFinal);
}

function get_horario()
{

  $validator=1;
  $fecha = date('Y-m-d H:i:s');

  $dias = array('Domingo','Lunes','Martes','Miercoles','Jueves','Viernes','Sabado');
    $fecha = $dias[date('N', strtotime($fecha))];

    $payloadGastosInsert[] = ["Horario" => "Fecha" . $fecha];

  if($fecha == "Domingo"){
      $payloadGastosInsert[] = ["Horario" => "Fecha entra en IF" . $fecha];


  }else{
 
       $hoy = getdate();
       $hoy = 
      

     
      $hora= $hoy['hours'];
      $hora = $hora - 1;

      $payloadGastosInsert[] = ["Horario" => "Hora Else  " . $hora];

      
        if($hora >8 && $hora <18 ){
          $validator=1;
          }else{
            $validator=0;
          }    
  }

  $payloadGastosInsert[] = ["Horario" => "Validador   " . $validator];
return $validator;
}

//saber_dia('2015-03-13');
//const
$faltantes = [];

//datos Request
$datos = json_decode(file_get_contents('php://input') , true);

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


$tipoDeUnidad = $datos["tonelaje"];
$tipoDeAdecucacion = $datos["tipoDeUnidad"];

$total_casetas = $datos["total_casetas"];
//$total_distancia = $datos["total_distancia"] / 1000;
$total_distancia = $datos["total_distancia"];
$ejes = $datos["ejes"];
$total_tiempo = 0;
$total_tiempo_formato = $datos["total_tiempo"];
$checkFecha = $datos["checkValidDate"];
$multidestino = $datos["isMultidestiny"];
$routeName = $datos["routeName"];
$datosServiciosAdicionales = $datos["seviciosAdiconales"];
$productoRes = $datos["producto"];
$tipoCliente = $datos["tipoCliente"];
$totalPrecioViaje = $datos["totalPrecioViaje"];
$metricas = $datos["metricas"];
$idTipoPrecio = $datos["idTipoPrecio"];
$operador = $datos["operador"];


$password = $datos["password"];
/*
if ($tipoUnidad == "")
{
    array_push($faltantes, 'tipoUnidad');
}
if ($tipoDeAdecuacion == "")
{
    array_push($faltantes, 'tipoDeAdecuacion');
}
if ($metricas == "")
{
    array_push($faltantes, 'metricas');
}
if ($totalPrecioViaje == "")
{
    array_push($faltantes, 'totalPrecioViaje');
}
if ($tipoCliente == "")
{
    array_push($faltantes, 'tipoCliente');
}
if ($productoRes == "")
{
    array_push($faltantes, 'productoRes');
}
if ($datosServiciosAdicionales == "")
{
    array_push($faltantes, 'datosServiciosAdicionales');
}*/
if ($base_de_operaciones == "")
{
    array_push($faltantes, 'Base de Operaciones');
}
if ($cliente == "")
{
    array_push($faltantes, 'Cliente');
}
if ($datosClienteTot == "" && $cliente == "0")
{
    array_push($faltantes, 'Datos del cliente');
}
if ($diesel == "")
{
    array_push($faltantes, 'Disel');
}
if ($ejes == "")
{
    array_push($faltantes, 'Ejes');
}
if ($fecha_salida == "")
{
    array_push($faltantes, 'Fecha de Salida');
}
if ($fecha_carga == "")
{
    array_push($faltantes, 'Fecha de Carga');
}
if ($fecha_disponibilidad == "")
{
    array_push($faltantes, 'Fecha de Disponibilidad');
}

/*
if ($tonelaje == "")
{
    array_push($faltantes, 'Tonelaje');
}*/
if ($total_distancia == "")
{
    array_push($faltantes, 'Distancia');
}
if ($total_tiempo_formato == "")
{
    array_push($faltantes, 'Tiempo Formato');
}

if (empty($faltantes))
{

    $mysqli = mysqli_init();
    $conexion = mysqli_connect($_SESSION['HOST'], $_SESSION['USER'], $_SESSION['PASS'], $_SESSION['DBNAME']);
    //Validacion conexion con bd
    if (!$conexion)
    {
        respuesta(500, 500, "Hay un error con el servidor. Llama a central Error-CLBD1", []);
    }
    else
    {

        if ($cliente == "0")
        {

            $nombreCliente = $datosClienteTot["datosCliente"]["nombrePropietario"];
            $rfc = $datosClienteTot["datosCliente"]["rfc"];
            $telefono = $datosClienteTot["datosCliente"]["telefono"];
            $email = $datosClienteTot["datosCliente"]["email"];
            $contacto = $datosClienteTot["datosCliente"]["contacto"];
            $razonSocial = $datosClienteTot["datosCliente"]["razonSocial"];

            $insertV = "INSERT INTO clientes (nombre,rfc,telefono, email, contacto, razonSocial,tipoCliente)  VALUES ('$nombreCliente','$rfc','$telefono','$email','$contacto','$razonSocial','$tipoCliente')";
                $datosClientes[]=$nombreCliente;
                $datosClientes[]=$rfc;
                $datosClientes[]=$telefono;
                $datosClientes[]=$email;
                $datosClientes[]=$contacto;
                $datosClientes[]=$razonSocial;
                $datosClientes[]=$tipoCliente;
            if ($conexion->query($insertV) === true)
            {
                $last_id = $conexion->insert_id;
                $payloadGastosInsert[] = ["contactoInsert" => " Exito al registrar cliente" . $last_id];
                $idClienteFinal = $last_id;

                $contacto = $datosClienteTot["contactoGeneral"]["contacto"];
                $telefono = $datosClienteTot["contactoGeneral"]["telefono"];
                $correo = $datosClienteTot["contactoGeneral"]["correo"];

                $insertV = "INSERT INTO contactoCliente (idCliente ,tipoContacto ,contacto , telefono , correo )  VALUES ('$idClienteFinal','1','$contacto','$telefono','$correo')";

                if ($conexion->query($insertV) === true)
                {
                    $last_id = $conexion->insert_id;
                    $payloadGastosInsert[] = ["contactoInsert" => " Exito al insertar contacto " . $last_id];
                }
                else
                {
                    $payloadGastosInsert[] = ["contactoInsert" => " Error al insertar contacto " . $last_id];
                }

                $contacto = $datosClienteTot["contactoCarga"]["contacto"];
                $telefono = $datosClienteTot["contactoCarga"]["telefono"];
                $correo = $datosClienteTot["contactoCarga"]["correo"];

                $insertV = "INSERT INTO contactoCliente (idCliente ,tipoContacto ,contacto , telefono , correo )  VALUES ('$idClienteFinal','2','$contacto','$telefono','$correo')";
                if ($conexion->query($insertV) === true)
                {
                    $last_id = $conexion->insert_id;
                    $payloadGastosInsert[] = ["contactoInsert" => " Exito al insertar contactoCliente " . $last_id];
                }
                else
                {
                    $payloadGastosInsert[] = ["contactoInsert" => " Error al insertar contactoCliente " . $last_id];
                }

                $contacto = $datosClienteTot["contactoEntrega"]["contacto"];
                $telefono = $datosClienteTot["contactoEntrega"]["telefono"];
                $correo = $datosClienteTot["contactoEntrega"]["correo"];

                $insertV = "INSERT INTO contactoCliente (idCliente ,tipoContacto ,contacto , telefono , correo )  VALUES ('$idClienteFinal','3','$contacto','$telefono','$correo')";
                if ($conexion->query($insertV) === true)
                {
                    $last_id = $conexion->insert_id;
                    $payloadGastosInsert[] = ["contactoInsert" => " Exito al insertar contactoCliente " . $last_id];
                }
                else
                {
                    $payloadGastosInsert[] = ["contactoInsert" => " Error al insertar contactoCliente " . $last_id];
                }

                $insertV = "INSERT INTO usuarios (id,usuario ,password,nombre,mail,perfil)  VALUES ('$idClienteFinal','$email','$password','$nombreCliente','$email','CLIENTE')";
                if ($conexion->query($insertV) === true)
                {
                    $last_id = $conexion->insert_id;


                    $payloadGastosInsert[] = ["contactoInsert" => " Exito al insertar usuario" . $last_id];


                    $datosClientes[]=$idClienteFinal;
                    $datosClientes[]=$email;
                    $datosClientes[]=$password;
                    $datosClientes[]=$nombreCliente;
                    $datosClientes[]=$email;
                  
            


                    $payloadGastosInsert[] = ["contactoInsert" => " Exito al insertar usuario" . $datosClientes];

            
               



                    $consulta =  "SELECT * FROM unidadesNueva where idTipoUnidad = '$tipoDeUnidad' " ;
                    $consultaresponse =  mysqli_query($conexion, $consulta);
                    $row2 = mysqli_fetch_array($consultaresponse, MYSQLI_ASSOC);
                    $tonelaje = $row2["nombreUnidad"];



                    if(empty($tonelaje)){
                        $tonelaje=$tipoDeUnidad;

                    }
            
                    
            
                    $consulta =  "SELECT * FROM adecuacion where idTIpoADecuacion = '$tipoDeAdecucacion' " ;
                    $consultaresponse =  mysqli_query($conexion, $consulta);
                    $row2 = mysqli_fetch_array($consultaresponse, MYSQLI_ASSOC);
                    $tipoDeAdecuacion1 = $row2["nombreAdecuacion"];

                    if(empty($tipoDeAdecuacion1)){
                        $tipoDeAdecuacion1=$tipoDeAdecucacion;

                    }


                    //Termina Enrollamiento
                    $fechaEntregaTemporal = "aun nose";
                    $entrega = "aun nose";
                    $estatus = "Pendiente";
                    $base_de_operaciones = "Cda. del Proton 12, Industrial Tlatilco 2, 53470 Naucalpan de Juarez, Mex., Mexico";
                    $insertV = "INSERT INTO viajes VALUES 
                                        (null,'$base_de_operaciones','$nombreCliente','','$routeName','$fecha_salida','$fecha_carga','$fechaEntregaTemporal',
                                        '$fecha_disponibilidad','$tipoDeAdecuacion1','$tonelaje','$unidad','$operador','$numero_de_tramos','$diesel','$total_distancia',
                                        '$ejes','$total_casetas','$total_tiempo','$total_tiempo_formato','$isRoundTrip','$checkFecha','$multidestino','',false,'$estatus','Pendiente')";
                            
                    if ($conexion->query($insertV) === true)
                    {
                        $last_id = $conexion->insert_id;
                        $payloadGastosInsert[] = ["ViajeInsert" => " Exito New Travel record created successfully " . $last_id];
                        $contTramos = 1;
                        foreach ($datos["rutas"] as & $valor)
                        {
                            $indexRoute = $contTramos;
                            $casetas = $valor["casetas"];
                            $destino = $valor["destino"];
                            $distancia = round($valor["distancia"]) / 1000;
                            $fecha_unix = strtotime('-5 hour', strtotime($valor["fecha"]));
                            $fecha = gmdate("Y-m-d\TH:i:s\Z", $fecha_unix);
                            if ($indexRoute < 3)
                            {
                                $entrega = $valor["ciudad"];
                                $entregaTemporal = $valor["destino"];
                                $fechaEntregaTemporal = $fecha;
                            }
                            $entrega = $valor["ciudad"];
                            $fechaLabel = $valor["fechaLabel"];
                            $load_time = $valor["load_time"];
                            $tiempo = $valor["tiempo"];
                            $total_tiempo = $total_tiempo + $distancia / 60 + $load_time;
                            $origen = $valor["origen"];
                            $waypoints = $valor["waypoints"];
                            $ciudad = $valor["ciudad"];
                            $observaciones = $valor["observaciones"];
                            $tipo = "aun nose";
                            $idviaje = $last_id;

                            /* $insertT =  "INSERT INTO tramos VALUES
                                                (null,$indexRoute,'$casetas','$destino','$distancia','$fecha','$fechaLabel',' $load_time','$origen',
                                                '$tiempo','$waypoints','$tipo','$idviaje','$observaciones','Pendiente','','','$ciudad')"; */
                            $insertT = "INSERT INTO tramos VALUES 
                                                (null,'$idviaje',$indexRoute,'$fecha','$origen','$ciudad','$destino',' $load_time','$tiempo','$casetas','$distancia',
                                                '$observaciones','$waypoints','Pendiente')";

                            if ($conexion->query($insertT) === true)
                            {
                                $last_id_trip = $conexion->insert_id;
                                $payloadGastosInsert[] = ["tramosInsert" => " Exito New Travel record created successfully " . $last_id_trip];

                                foreach ($casetasToll[$contTramos - 1] as & $casetas)
                                {
                                    $name = $casetas["name"];
                                    $costoC = $casetas["cashCost"];
                                    $insertCasetas = "INSERT INTO casetas(id_tramo, nombre, precio) VALUES ($last_id_trip,'$name','$costoC')";
                                    if ($conexion->query($insertCasetas) === true)
                                    {

                                        $payloadGastosInsert[] = ["casetasInsert" => " Exito New Travel record created successfully " . $indexRoute];
                                    }
                                    else
                                    {

                                        $payloadGastosInsert[] = ["casetasInsert" => " Error al insertar caseta " . $indexRoute];
                                    }
                                }

                                $payloadGastosInsert[] = ["TramosCompleto" => " Tramos agregados success " . $last_id_trip];

                            }
                            else
                            {
                                $payloadGastosInsert[] = ["tramosInsert" => " Error al insertar tramo " . $conexion->error];
                            }
                            $contTramos = $contTramos + 1;
                        }

                        $total_tiempo = $total_tiempo - 1;

                        $payloadGastosInsert[] = ["TramosCompleto" => " Tramos agregados Exito " . $last_id_trip];

                        //Update
                        $insertU = "UPDATE viajes SET fecha_entrega='$fechaEntregaTemporal',destino='$entrega',ruta='$entregaTemporal',tiempo ='$total_tiempo' WHERE id = $idviaje;";
                        if ($multidestino)
                        {
                            $insertU = "UPDATE viajes SET fecha_entrega='$fechaEntregaTemporal',destino='Multidestino',tiempo ='$total_tiempo' WHERE id = $idviaje;";
                        }

                        if ($conexion->query($insertU) === true)
                        {
                            $payloadGastosInsert[] = ["updateSuccess" => " Exito al actualizar viaje" . $insert_id];

                            //aqui empieza nuevo flujo
                            $insertT = "INSERT INTO serviciosAdicionales (idViaje) VALUES ($idviaje)";
                            if ($conexion->query($insertT) === true)
                            {
                                $last_id = $conexion->insert_id;
                                $payloadGastosInsert[] = ["servAdInsert" => " Exito al insertar Servicios Adicionales " . $last_id];

                                $monto = $datosServiciosAdicionales["mercanciaAsegurada"]["monto"];
                                $precio = $datosServiciosAdicionales["mercanciaAsegurada"]["precio"];

                                $insertT = "INSERT INTO mercanciaAsegurada (idServicioAdicional,monto,precio) VALUES ($last_id,$monto,$precio )";
                                if ($conexion->query($insertT) === true)
                                {
                                    $payloadGastosInsert[] = ["servAdMercanciaInsert" => " Exito New Travel record created successfully " . $last_id];
                                }
                                else
                                {
                                    $payloadGastosInsert[] = ["servAdMercanciaInsert" => " Error al insertar Mercan " . $last_id];
                                }

                                $numeroCarga = $datosServiciosAdicionales["maniobras"]["numeroCarga"];
                                $numeroEntrega = $datosServiciosAdicionales["maniobras"]["numeroEntrega"];
                                $precio = $datosServiciosAdicionales["maniobras"]["precio"];

                                $insertT = "INSERT INTO maniobras (idServicioAdicional,numeroCarga,numeroEntrega,precio) VALUES ($last_id,$numeroCarga,$numeroEntrega,$precio)";

                                if ($conexion->query($insertT) === true)
                                {
                                    $payloadGastosInsert[] = ["AgregarManiobras" => " Exito New Travel record created successfully " . $last_id];
                                }
                                else
                                {
                                    $payloadGastosInsert[] = ["AgregarManiobras" => " Error al insertar Maniobras " . $last_id];
                                }

                                $tipo = $datosServiciosAdicionales["seguridadAdicional"]["tipo"];
                                $precio = $datosServiciosAdicionales["seguridadAdicional"]["precio"];

                                $insertT = "INSERT INTO seguridadAdicional (idServicioAdicional,tipo,precio) VALUES ('$last_id','$tipo','$precio')";
                                if ($conexion->query($insertT) === true)
                                {
                                    $payloadGastosInsert[] = ["AgregarseguridadAdicional" => " Exito New Travel record created successfully " . $last_id];

                                }
                                else
                                {
                                    $payloadGastosInsert[] = ["AgregarseguridadAdicional" => " Error al insertar Seguridad Adicional " . $conexion->error];

                                }

                                $km = $datosServiciosAdicionales["custodia"]["km"];
                                $precio = $datosServiciosAdicionales["custodia"]["precio"];

                                $insertT = "INSERT INTO custodia (idServicioAdicional,km,precio) VALUES ($last_id,$km,$precio)";

                                if ($conexion->query($insertT) === true)
                                {
                                    $payloadGastosInsert[] = ["Agregarcustodia" => " Exito New Travel record created successfully " . $last_id];
                                }
                                else
                                {

                                    $payloadGastosInsert[] = ["Agregarcustodia" => " Error al insertar custodia " . $last_id];
                                }

                                $categoria = $productoRes["categoria"];
                                $peso = $productoRes["peso"];
                                $largo = $productoRes["medidas"]["largo"];
                                $ancho = $productoRes["medidas"]["ancho"];
                                $alto = $productoRes["medidas"]["alto"];
                                $descripcion = $productoRes["descripcion"];

                                $insertT = "INSERT INTO productosByViaje (idViaje,peso,largo,ancho,alto,descripcion) VALUES ('$last_id','$peso','$largo','$ancho','$alto','$descripcion')";
                                if ($conexion->query($insertT) === true)
                                {
                                    $payloadGastosInsert[] = ["AgregarProductos" => " Exito New Travel record created successfully " . $last_id];
                                }
                                else
                                {

                                    $payloadGastosInsert[] = ["AgregarProductos" => " Error al insertar Productos " . $conexion->error];
                                }

                                $payloadGastosInsert[] = ["Servicios Adicionales Record OK" => " Exito New Travel record created successfully " . $last_id];

                               

                                //aqui Continua el flujo parte 2





                                $grupo=$metricas["idGrupo"];
                                $rendimiento=$metricas["rendimiento"];
                                $num_dias=$metricas["numDias"];
                                $comision=$metricas["comision"];
                                $viaticos=$metricas["viaticos"];
                                $utilidad_premium=$metricas["utilidadPremium"];
                                $gasto_premium=$metricas["gastoPremium"];
                                $km_inicial=$metricas["KilomInicial"];
                                $km_final=$metricas["KilomFinal"];

                                $insertT = "INSERT INTO metricas_precio (grupo,rendimiento,num_dias,comision,viaticos,utilidad_premium,gasto_premium,km_inicial,km_final) VALUES ('$grupo','$rendimiento','$num_dias','$comision','$viaticos','$utilidad_premium','$gasto_premium','$km_inicial','$km_final')";
                                if ($conexion->query($insertT) === true)
                                {
                                  $last_idmetricas_precio = $conexion->insert_id;
                                  
                                    $payloadGastosInsert[] = ["Agregarmetricas_precio" => " Exito New metricas_precio record created successfully " . $last_idmetricas_precio];
                                }
                                else
                                {

                                    $payloadGastosInsert[] = ["Agregarpmetricas_precio" => " Error al insertar metricas_precio " . $last_idmetricas_precio];
                                }



                                $horario_laboral = get_horario();
                                $insertT = "INSERT INTO precio_viaje (idViaje,idTipoPrecio,horario_laboral,precio,idMetricasPrecio) VALUES ('$idviaje','$idTipoPrecio','$horario_laboral','$precio','$last_idmetricas_precio')";
                                if ($conexion->query($insertT) === true)
                                {
                                    $payloadGastosInsert[] = ["Agregarprecio_viaje" => " Exito New precio_viaje record created successfully " . $idviaje];
                                }
                                else
                                {

                                    $payloadGastosInsert[] = ["Agregarprecio_viaje" => " Error al insertar precio_viaje " . $idviaje];
                                }


                                $insertT = "INSERT INTO empresa_viaje (idViaje,idEmpresa,estatus) VALUES ('$idviaje','1','Confirmado' )";

                                if ($conexion->query($insertT) === true)
                                {
                                   
                                    $payloadGastosInsert[] = ["Insert empresa_viaje" => " Exito New empresa_viaje record created successfully " . $idviaje];
                                   
                                }
                                else
                                {
                                    $payloadGastosInsert[] = ["Insert empresa_viaje" => " Error al insertar empresa_viaje " . $idviaje];
                                  
                                }


                                respuesta(200, 200, "Total record", $payloadGastosInsert);

                                

                                

                                
                            }
                            else
                            {
                                $payloadGastosInsert[] = ["servAdInsert" => " Error al insertar Servicios Adicionales " . $insert_id];
                                respuesta(500, 500, "Total record", $payloadGastosInsert);

                            }

                        }
                        else
                        {
                            $payloadGastosInsert[] = ["updateSuccess" => " Error al actualizar Viaje " . $insert_id];
                            respuesta(500, 500, "Total record", $payloadGastosInsert);
                        }

                    }
                    else
                    {
                        $payloadGastosInsert[] = ["ViajeInsert" => " Error at record created  " . $last_id];
                        respuesta(500, 500, "Total record", $payloadGastosInsert);
                    }
        


                }
                else
                {
                  $payloadGastosInsert[] = ["contactoInsert" => " Error al registrar usuario" . $conexion->error];
                  respuesta(500, 500, "Error al registrar usuario", $payloadGastosInsert);
                }
            }
            else
            {
                $payloadGastosInsert[] = ["contactoInsert" => " Error al registrar cliente" . $last_id];
                respuesta(500, 500, "Hay un error con el servidor. Llama a central Error-CLBD1", $payloadGastosInsert);
            }

        }
        else
        {

        //Analisis de la informacion



        $consulta =  "SELECT * FROM clientes where id = '$cliente' " ;
        $consultaresponse =  mysqli_query($conexion, $consulta);
        $row2 = mysqli_fetch_array($consultaresponse, MYSQLI_ASSOC);
        $consultaresponseDato = $row2["nombre"];




                    if (empty($consultaresponseDato)){
                        $consulta =  "SELECT * FROM clientes where nombre = '$cliente' " ;
                        $consultaresponse =  mysqli_query($conexion, $consulta);
                        $row2 = mysqli_fetch_array($consultaresponse, MYSQLI_ASSOC);
                        $consultaresponseDato = $row2["nombre"];   
                    }



                  
                    $consulta =  "SELECT * FROM unidadesNueva where idTipoUnidad = '$tipoDeUnidad' " ;
                    $consultaresponse =  mysqli_query($conexion, $consulta);
                    $row2 = mysqli_fetch_array($consultaresponse, MYSQLI_ASSOC);
                    $tonelaje = $row2["nombreUnidad"];



                    if(empty($tonelaje)){
                        $tonelaje=$tipoDeUnidad;

                    }
            
                    
            
                    $consulta =  "SELECT * FROM adecuacion where idTIpoADecuacion = '$tipoDeAdecucacion' " ;
                    $consultaresponse =  mysqli_query($conexion, $consulta);
                    $row2 = mysqli_fetch_array($consultaresponse, MYSQLI_ASSOC);
                    $tipoDeAdecuacion1 = $row2["nombreAdecuacion"];

                    if(empty($tipoDeAdecuacion1)){
                        $tipoDeAdecuacion1=$tipoDeAdecucacion;

                    }

            if (!empty($consultaresponseDato))
            {
              

                    $fechaEntregaTemporal = "aun nose";
                    $entrega = "aun nose";
                    $estatus = "Pendiente";
                    $base_de_operaciones = "Cda. del Proton 12, Industrial Tlatilco 2, 53470 Naucalpan de Juarez, Mex., Mexico";

                    $insertV = "INSERT INTO viajes VALUES 
                                  (null,'$base_de_operaciones','$consultaresponseDato','','$routeName','$fecha_salida','$fecha_carga','$fechaEntregaTemporal',
                                  '$fecha_disponibilidad','$tipoDeAdecuacion1','$tonelaje','$unidad','$operador','$numero_de_tramos','$diesel','$total_distancia',
                                  '$ejes','$total_casetas','$total_tiempo','$total_tiempo_formato','$isRoundTrip','$checkFecha','$multidestino','',false,'$estatus','Pendiente')";

                    if ($conexion->query($insertV) === true)
                    {
                        $last_id = $conexion->insert_id;
                        $payloadGastosInsert[] = ["ViajeInsert" => " Exito New Travel record created successfully TOTAL DISTANCIA" . $last_id];
                        $contTramos = 1;
                        foreach ($datos["rutas"] as & $valor)
                        {
                            $indexRoute = $contTramos;
                            $casetas = $valor["casetas"];
                            $destino = $valor["destino"];
                            $distancia = round($valor["distancia"]) / 1000;
                            $fecha_unix = strtotime('-5 hour', strtotime($valor["fecha"]));
                            $fecha = gmdate("Y-m-d\TH:i:s\Z", $fecha_unix);
                            if ($indexRoute < 3)
                            {
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
                            $ciudad = $valor["ciudad"];
                            $observaciones = $valor["observaciones"];
                            $tipo = "aun nose";
                            $idviaje = $last_id;

                            /* $insertT =  "INSERT INTO tramos VALUES
                                                    (null,$indexRoute,'$casetas','$destino','$distancia','$fecha','$fechaLabel',' $load_time','$origen',
                                                    '$tiempo','$waypoints','$tipo','$idviaje','$observaciones','Pendiente','','','$ciudad')"; */
                            $insertT = "INSERT INTO tramos VALUES 
                                                    (null,'$idviaje',$indexRoute,'$fecha','$origen','$ciudad','$destino',' $load_time','$tiempo','$casetas','$distancia',
                                                    '$observaciones','$waypoints','Pendiente')";
                            if ($conexion->query($insertT) === true)
                            {
                                $last_id_trip = $conexion->insert_id;
                                $payloadGastosInsert[] = ["tramosInsert" => " Exito New Travel record created successfully " . $last_id_trip];

                                foreach ($casetasToll[$contTramos - 1] as & $casetas)
                                {
                                    $name = $casetas["name"];
                                    $costoC = $casetas["cashCost"];
                                    $insertCasetas = "INSERT INTO casetas(id_tramo, nombre, precio) VALUES ($last_id_trip,'$name','$costoC')";
                                    if ($conexion->query($insertCasetas) === true)
                                    {

                                        $payloadGastosInsert[] = ["casetasInsert" => " Exito New Travel record created successfully " . $indexRoute];
                                    }
                                    else
                                    {

                                        $payloadGastosInsert[] = ["casetasInsert" => " Error al insertar caseta " . $indexRoute];
                                    }
                                }
                            }
                            else
                            {
                                $payloadGastosInsert[] = ["tramosInsert" => " Error al insertar tramo " . $last_id];
                            }
                            $contTramos = $contTramos + 1;
                        }

                        $total_tiempo = $total_tiempo - 1;

                        $payloadGastosInsert[] = ["TramosCompleto" => " Tramos agregados Sucess " . $last_id_trip];

                        //Update
                        $insertU = "UPDATE viajes SET fecha_entrega='$fechaEntregaTemporal',destino='$entrega',ruta='$entregaTemporal',tiempo ='$total_tiempo' WHERE id = $idviaje;";
                        if ($multidestino)
                        {
                            $insertU = "UPDATE viajes SET fecha_entrega='$fechaEntregaTemporal',destino='Multidestino',tiempo ='$total_tiempo' WHERE id = $idviaje;";
                        }

                        if ($conexion->query($insertU) === true)
                        {
                            $payloadGastosInsert[] = ["updateSuccess" => " Exito al actualizar viaje" . $insert_id];

                            //aqui empieza nuevo flujo
                            $insertT = "INSERT INTO serviciosAdicionales (idViaje) VALUES ($idviaje)";
                            if ($conexion->query($insertT) === true)
                            {
                                $last_id = $conexion->insert_id;
                                $payloadGastosInsert[] = ["servAdInsert" => " Exito al insertar Servicios Adicionales " . $last_id];

                                $monto = $datosServiciosAdicionales["mercanciaAsegurada"]["monto"];
                                $precio = $datosServiciosAdicionales["mercanciaAsegurada"]["precio"];

                                $insertT = "INSERT INTO mercanciaAsegurada (idServicioAdicional,monto,precio) VALUES ('$last_id','$monto','$precio' )";
                                if ($conexion->query($insertT) === true)
                                {
                                    $payloadGastosInsert[] = ["servAdMercanciaInsert" => " Exito New Travel record created successfully " . $last_id];
                                }
                                else
                                {
                                    $payloadGastosInsert[] = ["servAdMercanciaInsert" => " Error al insertar Mercan " . $last_id];
                                }

                                $numeroCarga = $datosServiciosAdicionales["maniobras"]["numeroCarga"];
                                $numeroEntrega = $datosServiciosAdicionales["maniobras"]["numeroEntrega"];
                                $precio = $datosServiciosAdicionales["maniobras"]["precio"];

                                $insertT = "INSERT INTO maniobras (idServicioAdicional,numeroCarga,numeroEntrega,precio) VALUES ('$last_id','$numeroCarga','$numeroEntrega','$precio')";

                                if ($conexion->query($insertT) === true)
                                {
                                    $payloadGastosInsert[] = ["AgregarManiobras" => " Exito New Travel record created successfully " . $last_id];
                                }
                                else
                                {
                                    $payloadGastosInsert[] = ["AgregarManiobras" => " Error al insertar Maniobras " . $last_id];
                                }

                                $tipo = $datosServiciosAdicionales["seguridadAdicional"]["tipo"];
                                $precio = $datosServiciosAdicionales["seguridadAdicional"]["precio"];

                                $insertT = "INSERT INTO seguridadAdicional (idServicioAdicional,tipo,precio) VALUES ('$last_id','$tipo','$precio')";
                                if ($conexion->query($insertT) === true)
                                {
                                    $payloadGastosInsert[] = ["AgregarseguridadAdicional" => " Exito New Travel record created successfully " . $last_id];

                                }
                                else
                                {
                                    $payloadGastosInsert[] = ["AgregarseguridadAdicional" => " Error al insertar Seguridad Adicional " . $last_id];

                                }

                                $km = $datosServiciosAdicionales["custodia"]["km"];
                                $precio = $datosServiciosAdicionales["custodia"]["precio"];

                                $insertT = "INSERT INTO custodia (idServicioAdicional,km,precio) VALUES ('$last_id','$km','$precio')";

                                if ($conexion->query($insertT) === true)
                                {
                                    $payloadGastosInsert[] = ["Agregarcustodia" => " Exito New Travel record created successfully " . $last_id];
                                }
                                else
                                {

                                    $payloadGastosInsert[] = ["Agregarcustodia" => " Error al insertar custodia " . $last_id];
                                }

                                $categoria = $productoRes["categoria"];
                                $peso = $productoRes["peso"];
                                $largo = $productoRes["medidas"]["largo"];
                                $ancho = $productoRes["medidas"]["ancho"];
                                $alto = $productoRes["medidas"]["alto"];
                                $descripcion = $productoRes["descripcion"];

                                $insertT = "INSERT INTO productosByViaje (idViaje,peso,largo,ancho,alto,descripcion) VALUES ('$idViaje','$peso','$largo','$ancho','$alto','$descripcion')";
                                if ($conexion->query($insertT) === true)
                                {
                                    $payloadGastosInsert[] = ["AgregarProductos" => " Exito New Travel record created successfully " . $last_id];
                                }
                                else
                                {

                                    $payloadGastosInsert[] = ["AgregarProductos" => " Error al insertar Productos " . $last_id];
                                }

                                $payloadGastosInsert[] = ["Servicios Adicionales Record OK" => " Exito New Travel record created successfully " . $last_id];

                                $grupo=$metricas["idGrupo"];
                                $rendimiento=$metricas["rendimiento"];
                                $num_dias=$metricas["numDias"];
                                $comision=$metricas["comision"];
                                $viaticos=$metricas["viaticos"];
                                $utilidad_premium=$metricas["utilidadPremium"];
                                $gasto_premium=$metricas["gastoPremium"];
                                $km_inicial=$metricas["KilomInicial"];
                                $km_final=$metricas["KilomFinal"];

                                $insertT = "INSERT INTO metricas_precio (grupo,rendimiento,num_dias,comision,viaticos,utilidad_premium,gasto_premium,km_inicial,km_final) VALUES ('$grupo','$rendimiento','$num_dias','$comision','$viaticos','$utilidad_premium','$gasto_premium','$km_inicial','$km_final')";
                                if ($conexion->query($insertT) === true)
                                {
                                  $last_idmetricas_precio = $conexion->insert_id;
                                  
                                    $payloadGastosInsert[] = ["Agregarmetricas_precio" => " Exito New metricas_precio record created successfully " . $last_idmetricas_precio];
                                }
                                else
                                {

                                    $payloadGastosInsert[] = ["Agregarpmetricas_precio" => " Error al insertar metricas_precio " . $last_idmetricas_precio];
                                }



                                $horario_laboral = get_horario();
                                $insertT = "INSERT INTO precio_viaje (idViaje,idTipoPrecio,horario_laboral,precio,idMetricasPrecio) VALUES ('$idviaje','$idTipoPrecio','$horario_laboral','$precio','$last_idmetricas_precio')";
                                if ($conexion->query($insertT) === true)
                                {
                                    $payloadGastosInsert[] = ["Agregar precio_viaje" => " Exito New precio_viaje record created successfully " . $idviaje];
                                }
                                else
                                {

                                    $payloadGastosInsert[] = ["Agregar precio_viaje" => " Error al insertar precio_viaje " . $idviaje];
                                }



                                $insertT = "INSERT INTO empresa_viaje (idViaje,idEmpresa,estatus) VALUES ('$idviaje','1','Confirmado' )";
                                

                                if ($conexion->query($insertT) === true)
                                {
                                   
                                    $payloadGastosInsert[] = ["Insert empresa_viaje" => " Exito New empresa_viaje record created successfully " . $idviaje];
                                   
                                }
                                else
                                {
                                    $payloadGastosInsert[] = ["Insert empresa_viaje" => " Error al empresa_viaje " . $idviaje];
                                  
                                }

                                respuesta(200, 200, "Total record", $payloadGastosInsert);

                                //aqui Continua el flujo parte 2
                                
                            }
                            else
                            {
                                $payloadGastosInsert[] = ["servAdInsert" => " Error al insertar Servicios Adicionales " . $insert_id];
                                respuesta(500, 500, "Total record", $payloadGastosInsert);

                            }

                        }
                        else
                        {
                            $payloadGastosInsert[] = ["updateSuccess" => " Error al actualizar Viaje " . $insert_id];
                            respuesta(500, 500, "Total record", $payloadGastosInsert);
                        }

                    }
                    else
                    {
                        $payloadGastosInsert[] = ["ViajeInsert" => " Error at record created  " . $last_id];
                        respuesta(500, 500, "Total record", $payloadGastosInsert);
                    }

              
            }
            else
            {
      
                $payloadGastosInsert[] = ["Select cliente" => " Error al buscar cliente: __ " .  $consulta];
                $payloadGastosInsert[] = ["Select cliente" => " Error al buscar cliente: " .  $cliente];
                respuesta(404, 404, "Error al seleccionar cliente", $payloadGastosInsert);
            }

        }
    }
}
else
{
    $payload = ["Faltantes" => $faltantes];
    respuesta(400, 400, "Hay un error con el servidor. Llama a central Error-TARE1", $payload);
}