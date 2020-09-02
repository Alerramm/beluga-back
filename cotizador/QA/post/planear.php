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

function insert($conexion, $query)
{
    if ($conexion->query($query) === true) {
        $last_id = $conexion->insert_id;
        return $last_id;
    } else {
        return 0;
    }
}

function insertTramo($conexion, $tramo, $idViaje, $numeroTramo)
{
    $fecha = $tramo["fecha"];
    $origen = $tramo["origen"];
    $destino = "";
    $entrega = $tramo["destino"];
    $tiempo = $tramo["tiempo"];
    $casetas = $tramo["casetas"];
    $distancia = $tramo["distancia"];
    $lugar_carga = $tramo["lugar_carga"];
    $query = "INSERT INTO tramos(idviaje, tramo, fecha, origen, destino, entrega, tiempo, casetas, distancia, estatus, observaciones, waypoints) VALUES ('$idViaje', '$numeroTramo', '$fecha', '$origen', '$destino', '$entrega', '$tiempo', '$casetas', '$distancia', 'Pendiente', '$lugar_carga', '[]')";
    if ($conexion->query($query) === true) {
        $last_id = $conexion->insert_id;
        $payload["idTramo"] = $last_id;
        $contCaseta = 1;
        foreach ($tramo["desglose_casetas"] as &$caseta) {
            $name = $caseta["name"];
            $costoC = $caseta["cashCost"];
            $payload["casetas" . $contCaseta] = insert($conexion, "INSERT INTO casetas(idTramo, nombre, precio) VALUES ($last_id,'$name','$costoC')");
            $contCaseta = $contCaseta + 1;
        }
        return $payload;
    } else {
        $payload["idTramo"] = 0;
        return $payload;
    }
}

function update($conexion, $query)
{
    if ($conexion->query($query) === true) {
        return true;
    } else {
        return false;
    }
}

function consulta($conexion, $consulta)
{
    //Consulta 
    $query = mysqli_query($conexion, $consulta);
    $row_cnt = $query->num_rows;
    if ($row_cnt > 0) {
        while ($row = $query->fetch_array(MYSQLI_ASSOC)) {
            $row["key"] = $row["id"];
            $respuesta[] = $row;
        }
    } else {
        $respuesta = [];
    }

    return  $respuesta;
}

function registro($conexion, $datos_cliente)
{
    $fe1 = date("Y-m-d");
    $tipo = $datos_cliente["cliente"]["tipo"];
    $correo = $datos_cliente["cliente"]["correo"];
    $password = $datos_cliente["cliente"]["password"];
    $telefono = $datos_cliente["cliente"]["telefono"];
    $razonSocial = $datos_cliente["cliente"]["razonSocial"];
    $rfc = $datos_cliente["cliente"]["rfc"];
    $direccion_fiscal = $datos_cliente["cliente"]["direccion_fiscal"];
    $contacto = $datos_cliente["cliente"]["contacto"];

    $clienteBD =  consulta($conexion, "SELECT A.id FROM usuarios A INNER JOIN clientes B ON A.id = B.idUsuario WHERE A.usuario='$correo' and A.perfil='CLIENTE'");

    if (empty($clienteBD)) {
        $idCliente = insert($conexion, "INSERT INTO usuarios (usuario, password, nombre, fechaAlta, perfil, mail) VALUES ('$correo', '$password', '$contacto', '$fe1', 'CLIENTE', '$correo')");
        $idUsuario = insert($conexion, "INSERT INTO clientes (nombre, rfc, domicilio, domCarga, razonSocial, telefono, contacto, email, tipoCliente, idUsuario) VALUES ('$contacto', '$rfc', '$direccion_fiscal', '$direccion_fiscal', '$razonSocial', '$telefono', '$contacto', '$correo', '$tipo', $idCliente)");
        $idUsuarioRestricciones = insert($conexion, "INSERT INTO usuario_resticciones (idUsuario, precio_prepago_premium, precio_postpago_premium, precio_postpago_basico, precio_contrato) VALUES ( $idCliente, 1, 0, 0, 0)");
    } else {
        $idCliente = $clienteBD[0]["id"];
    }

    return $idCliente;
}

function destino($multidestino, $destino)
{
    if ($multidestino) {
        return "Multidestino";
    } else {
        return $destino;
    }
}

function get_horario()
{

    $validator = 1;
    $fecha = date('Y-m-d H:i:s');

    $dias = array('Domingo', 'Lunes', 'Martes', 'Miercoles', 'Jueves', 'Viernes', 'Sabado');
    $fecha = $dias[date('N', strtotime($fecha))];

    $payloadGastosInsert[] = ["Horario" => "Fecha" . $fecha];

    if ($fecha == "Domingo") {
        $payloadGastosInsert[] = ["Horario" => "Fecha entra en IF" . $fecha];
    } else {

        $hoy = getdate();
        $hoy =



            $hora = $hoy['hours'];
        $hora = $hora - 1;

        $payloadGastosInsert[] = ["Horario" => "Hora Else  " . $hora];


        if ($hora > 8 && $hora < 18) {
            $validator = 1;
        } else {
            $validator = 0;
        }
    }

    $payloadGastosInsert[] = ["Horario" => "Validador   " . $validator];
    return $validator;
}

//saber_dia('2015-03-13');
//const
$faltantes = [];

//datos Request
$datos = json_decode(file_get_contents('php://input'), true);

//Viaje
$tramos = $datos["tramos"];
$ejes = $datos["ejes"];
$multidestino = $datos["multidestino"];
$diesel = $datos["diesel"];
$fecha_salida_unix = strtotime('-5 hour', strtotime($datos["fecha_salida"]));
$fecha_salida = gmdate("Y-m-d\TH:i:s\Z", $fecha_salida_unix);
$fecha_carga_unix = strtotime('-5 hour', strtotime($datos["fecha_carga"]));
$fecha_carga = gmdate("Y-m-d\TH:i:s\Z", $fecha_carga_unix);
$fecha_disponibilidad_unix = strtotime('-5 hour', strtotime($datos["fecha_disponibilidad"]));
$fecha_disponibilidad = gmdate("Y-m-d\TH:i:s\Z", $fecha_disponibilidad_unix);
$total_distancia = $datos["total_distancia"];
$total_casetas = $datos["total_casetas"];
$total_tiempo = $datos["total_tiempo"];
$numero_tramos = $datos["numero_tramos"];
$servicios_adicionales = $datos["servicios_adicionales"];
$producto = $datos["producto"];
$tipo_adecuacion = $datos["tipo_adecuacion"];
$tipo_unidad = $datos["tipo_unidad"];
$idTipoPrecio = $datos["idTipoPrecio"];
$precio_viaje = $datos["precio_viaje"];
$metricas = $datos["metricas"];
$idCliente = $datos["idCliente"];
$datos_cliente = $datos["datos_cliente"];

if ($ejes == "") {
    array_push($faltantes, 'ejes');
}
if ($diesel == "") {
    array_push($faltantes, 'diesel');
}
if ($fecha_salida == "") {
    array_push($faltantes, 'fecha_salida');
}
if ($fecha_carga == "") {
    array_push($faltantes, 'fecha_carga');
}
if ($fecha_disponibilidad == "") {
    array_push($faltantes, 'fecha_disponibilidad');
}
if ($total_distancia == "") {
    array_push($faltantes, 'total_distancia');
}
if ($total_casetas == "") {
    array_push($faltantes, 'total_casetas');
}
if ($total_tiempo == "") {
    array_push($faltantes, 'total_tiempo');
}
if ($numero_tramos == "") {
    array_push($faltantes, 'numero_tramos');
}
if ($tipo_adecuacion == "") {
    array_push($faltantes, 'tipo_adecuacion');
}
if ($tipo_unidad == "") {
    array_push($faltantes, 'tipo_unidad');
}
if ($precio_viaje == "") {
    array_push($faltantes, 'precio_viaje');
}

if (empty($faltantes)) {

    $mysqli = mysqli_init();
    $conexion = mysqli_connect($_SESSION['HOST'], $_SESSION['USER'], $_SESSION['PASS'], $_SESSION['DBNAME']);
    //Validacion conexion con bd
    if (!$conexion) {
        respuesta(500, 500, "Hay un error con el servidor. Llama a central Error-CLBD1", []);
    } else {

        //registro usuario
        $registroOk = true;
        if ($idCliente == "0") {
            $idCliente =  registro($conexion, $datos_cliente);

            $payload["idCliente"] = $idCliente;
            $payload["user"] = $datos_cliente["cliente"]["correo"];
            $payload["password"] = $datos_cliente["cliente"]["password"];
        } else {
            $clienteBD =  consulta($conexion, "SELECT A.* FROM usuarios A INNER JOIN clientes B ON A.id = B.idUsuario WHERE A.id=$idCliente");
            if (empty($clienteBD)) {
                $payload = ["idCliente" => $idCliente];
                $registroOk = false;
            } else {
                $idCliente = $clienteBD[0]["id"];
                $payload["idCliente"] = $idCliente;
                $payload["user"] = $clienteBD[0]["usuario"];
                $payload["password"] = $clienteBD[0]["password"];
            }
        }
        if ($registroOk) {

            //contactos
            //contacto general
            $contactoGeneral = $datos_cliente["contactoGeneral"]["contacto"];
            $telefonoGeneral = $datos_cliente["contactoGeneral"]["telefono"];
            $correoGeneral = $datos_cliente["contactoGeneral"]["correo"];
            $payload["idContactoGeneral"] = insert($conexion, "INSERT INTO contactoCliente (idCliente ,tipoContacto ,contacto , telefono , correo ) VALUES ('$idCliente','1','$contactoGeneral','$telefonoGeneral','$correoGeneral')");

            //contacto carga
            $contactoCarga = $datos_cliente["contactoCarga"]["contacto"];
            $telefonoCarga = $datos_cliente["contactoCarga"]["telefono"];
            $correoCarga = $datos_cliente["contactoCarga"]["correo"];
            $payload["idContactoCarga"] = insert($conexion, "INSERT INTO contactoCliente (idCliente ,tipoContacto ,contacto , telefono , correo ) VALUES ('$idCliente','2','$contactoCarga','$telefonoCarga','$correoCarga')");

            //contacto entrega
            $contactoEntrega = $datos_cliente["contactoEntrega"]["contacto"];
            $telefonoEntrega = $datos_cliente["contactoEntrega"]["telefono"];
            $correoEntrega = $datos_cliente["contactoEntrega"]["correo"];
            $payload["idContactoEntrega"] = insert($conexion, "INSERT INTO contactoCliente (idCliente ,tipoContacto ,contacto , telefono , correo ) VALUES ('$idCliente','3','$contactoEntrega','$telefonoEntrega','$correoEntrega')");

            //adecuacion
            $adecuacionDB = consulta($conexion, "SELECT * FROM adecuacion WHERE idTIpoADecuacion = $tipo_adecuacion");
            $payload["adecuacion"] =  $adecuacionDB[0]["nombreAdecuacion"];
            $adecuacion = $adecuacionDB[0]["nombreAdecuacion"];

            //unidades
            $unidadDB = consulta($conexion, "SELECT * FROM unidadesNueva WHERE idTIpoUnidad = $tipo_unidad");
            $payload["unidad"] = $unidadDB[0]["nombreUnidad"];
            $unidad = $unidadDB[0]["nombreUnidad"];

            //viajes
            $baseDeOperaciones = "NAUCALPAN";
            $clienteDB = consulta($conexion, "SELECT * FROM clientes WHERE idUsuario = $idCliente");
            $cliente = $clienteDB[0]["nombre"];
            $destino = destino($multidestino, $tramos[1]["lugar_carga"]);
            $ruta = $tramos[1]["destino"];
            $fecha_entrega = $tramos[1]["fecha"];
            $redondo = true;
            $validaFechas = true;
            $payload["idViaje"] = insert($conexion, "INSERT INTO viajes(base, cliente, destino, ruta, fecha_salida, fecha_carga, fecha_entrega, fecha_disponibilidad, unidad_tipo, unidad_modelo, tramos, diesel, distancia, ejes, casetas, tiempo, redondo, valida_fechas, multidestino, estatus, estatus_app) VALUES ('$baseDeOperaciones', '$cliente', '$destino', '$ruta', '$fecha_salida', '$fecha_carga', '$fecha_entrega', '$fecha_disponibilidad', '$adecuacion', '$unidad', '$numero_tramos', '$diesel', '$total_distancia', '$ejes', '$total_casetas', '$total_tiempo', '$redondo', '$validaFechas', '$multidestino', 'Solicitud', 'Pendiente' )");

            $idViaje = $payload["idViaje"];

            $contTramo = 1;
            foreach ($tramos as &$tramo) {
                $payload["tramo" . $contTramo] = insertTramo($conexion, $tramo, $payload["idViaje"], $contTramo);
                $contTramo = $contTramo + 1;
            }

            //empres_viaje
            $payload["empresaViaje"] = insert($conexion, "INSERT INTO empresa_viaje (idViaje,idEmpresa,estatus) VALUES ('$idViaje','1','Confirmado')");

            //servicios adiconales
            $payload["idServiciosAdicionales"] = insert($conexion, "INSERT INTO serviciosAdicionales (idViaje) VALUES ($idViaje)");
            $idServiciosAdicionales = $payload["idServiciosAdicionales"];

            //mercancia asegurada
            $monto = $servicios_adicionales["mercancia_asegurada"]["monto"];
            $precio = $servicios_adicionales["mercancia_asegurada"]["precio"];
            $payload["mercancia_asegurada"] = insert($conexion, "INSERT INTO mercanciaAsegurada (idServicioAdicional,monto,precio) VALUES ($idServiciosAdicionales,$monto,$precio )");

            //maniobras
            $numero_carga = $servicios_adicionales["maniobras"]["numero_carga"];
            $numero_entrega = $servicios_adicionales["maniobras"]["numero_entrega"];
            $precio = $servicios_adicionales["maniobras"]["precio"];
            $payload["maniobras"] = insert($conexion, "INSERT INTO maniobras (idServicioAdicional,numeroCarga,numeroEntrega,precio) VALUES ($idServiciosAdicionales,$numero_carga,$numero_entrega,$precio)");

            //seguridad adicional
            $tipo = $servicios_adicionales["seguridad_adicional"]["tipo"];
            $precio = $servicios_adicionales["seguridad_adicional"]["precio"];
            $payload["seguridad_adicional"] = insert($conexion, "INSERT INTO seguridadAdicional (idServicioAdicional,tipo,precio) VALUES ('$idServiciosAdicionales','$tipo','$precio')");

            //custodia
            $km = $servicios_adicionales["custodia"]["km"];
            $precio = $servicios_adicionales["custodia"]["precio"];
            $payload["custodia"] = insert($conexion, "INSERT INTO custodia (idServicioAdicional,km,precio) VALUES ($idServiciosAdicionales,$km,$precio)");


            //producto
            $peso = $producto["peso"];
            $largo = $producto["medidas"]["largo"];
            $ancho = $producto["medidas"]["ancho"];
            $alto = $producto["medidas"]["alto"];
            $descripcion = $producto["descripcion"];
            $payload["producto"] = insert($conexion, "INSERT INTO productosByViaje (idViaje,peso,largo,ancho,alto,descripcion) VALUES ('$idviaje','$peso','$largo','$ancho','$alto','$descripcion')");

            //metricas precio
            $grupo = $metricas["idGrupo"];
            $rendimiento = $metricas["rendimiento"];
            $num_dias = $metricas["numDias"];
            $comision = $metricas["comision"];
            $viaticos = $metricas["viaticos"];
            $utilidad_premium = $metricas["utilidadPremium"];
            $gasto_premium = $metricas["gastoPremium"];
            $KilomInicial = $metricas["KilomInicial"];
            $km_final = $metricas["KilomFinal"];
            $payload["metricas"] = insert($conexion, "INSERT INTO metricas_precio (grupo,rendimiento,num_dias,comision,viaticos,utilidad_premium,gasto_premium,km_inicial,km_final) VALUES ('$grupo','$rendimiento','$num_dias','$comision','$viaticos','$utilidad_premium','$gasto_premium','$KilomInicial','$km_final')");
            $idMetricas = $payload["metricas"];


            //precio viaje
            $horario_laboral = get_horario();
            $payload["precio_viaje"] = insert($conexion, "INSERT INTO precio_viaje (idViaje,idTipoPrecio,horario_laboral,precio,idMetricasPrecio) VALUES ('$idViaje','$idTipoPrecio','$horario_laboral','$precio_viaje','$idMetricas')");

            respuesta(200, 200, "Respuesta Exitosa", $payload);
        } else {
            respuesta(200, 204, "El usuario no se encuentra registrado. Comuniquese con nosotros para continuar su proceso", $payload);
        }
    }
} else {
    $payload = ["Faltantes" => $faltantes];
    respuesta(400, 400, "Hay un error con el servidor. Llama a central Error-TARE1", $payload);
}
