<?PHP
//datos .env
include '../production.php';

//header
header('Access-Control-Allow-Origin: *');
header("Content-Type: application/json; charset=UTF-8");

//const 
$faltantes = [];

//datos Request
file_get_contents("http://www.misistema.mx/beluga/Finanzas/endpoints/confirmacion/QA/post/inicioConfirmacion.php");

//funciones
function respuesta($codehttp, $code, $mensaje, $factload)
{
    http_response_code($codehttp);
    $dataFinal = [
        "headerResponse" => [
            "code" => $code,
            "mensaje" => $mensaje
        ],
        "payload" => $factload
    ];
    echo json_encode($dataFinal);
}

//Validacion de Datos

if (empty($faltantes)) {
    //Conexion a base de datos
    $mysqli = mysqli_init();
    $conexion = mysqli_connect($_SESSION['HOST'], $_SESSION['USER'], $_SESSION['PASS'], $_SESSION['DBNAME']);
    //Validacion conexion con bd
    if (!$conexion) {
        respuesta(500, 500, "Hay un error con el servidor. Llama a central Error-CONECTION-DATA", []);
    } else {
        //configuracon db
        mysqli_query($conexion, "SET CHARACTER SET 'utf8'");
        mysqli_query($conexion, "SET SESSION collation_connection ='utf8_unicode_ci'");


        //Consulta viajes
        $consulta =
            "SELECT v.estatus_app as estatus_operador, ev.estatus as estatus_empresa, v.cliente, t.entrega as direccion_carga, v.fecha_carga, ev.nombre, v.operador, v.unidad, v.ruta as entrega, v.destino, v.fecha_entrega, p.precio  
            FROM viajes v 
            INNER JOIN empresa_viaje ev on v.id = ev.idViaje
            INNER JOIN tramos t on v.id = t.idViaje
            INNER JOIN empresa e on ev.idEmpresa = e.id
            INNER JOIN precio_viaje p on p.idViaje = v.id
            where t.tramo = 1
            and estatus = 'Confirmado'";

        $viajes = mysqli_query($conexion, $consulta);
        while ($row = $viajes->fetch_array(MYSQLI_ASSOC)) {
            $dateC = new DateTime($row["fecha_carga"]);
            $row["fecha_carga"] = $dateC->format('Y-m-d H:i');
            $dateE = new DateTime($row["fecha_entrega"]);
            $row["fecha_entrega"] = $dateE->format('Y-m-d H:i');
            $idViaje = $row["id"];
            $dataViajes[] = $row;
        }
        //Response
        if (empty($dataViajes)) {
            respuesta(200, 404, "No existen viajes ", []);
        } else {
            $payload = $dataViajes;
            respuesta(200, 200, "Respuesta exitosa", $payload);
        }
    }
} else {
    $payload = ["Faltantes" => $faltantes];
    respuesta(400, 400, "Hay un error con el servidor. Llama a central Error-VI-BAD-REQUEST", $payload);
}
