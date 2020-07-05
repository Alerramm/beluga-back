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
$idTramo = $datos["idTramo"];
$estatus = $datos["estatus"];
$motivo = $datos["motivo"];
$tipoRobo = $datos["tipoRobo"];
$todosBien = $datos["todosBien"];

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
if ($idTramo == "") {
    array_push($faltantes, 'idTramo');
}
if ($estatus == "") {
    array_push($faltantes, 'estatus');
}
if ($motivo == "") {
    array_push($faltantes, 'motivo');
}


if (empty($faltantes)) {
    //Conexion a base de datos
    $mysqli = mysqli_init();
    $conexion = mysqli_connect($_SESSION['HOST'], $_SESSION['USER'], $_SESSION['PASS'], $_SESSION['DBNAME']);
    //Validacion conexion con bd
    if (!$conexion) {
        respuesta(500, 500, "Hay un error con el servidor. Llama a central Error-INBD1", []);
    } else {
        //configuracon db
        mysqli_query($conexion, "SET CHARACTER SET 'utf8'");
        mysqli_query($conexion, "SET SESSION collation_connection ='utf8_unicode_ci'");

        //Analisis de la informacion
        $consulta =  "SELECT *  FROM tramos where id = $idTramo";
        $viajes =  mysqli_query($conexion, $consulta);
        $row = mysqli_fetch_array($viajes, MYSQLI_ASSOC);

        if (empty($row)) {
            respuesta(200, 404, "Hay un error con el servidor. Llama a central. Error-INTOD" . $id, []);
        } else {
            //Update
            $insertIncidencia =  "INSERT INTO incidencias(idTramo, estatus, motivo, tipo_robo, todos_bien) VALUES ('$idTramo','$estatus','$motivo','$tipoRobo','$todosBien')";
            if ($conexion->query($insertIncidencia) === TRUE) {
                $incidenciasTramo =  mysqli_query($conexion,  "SELECT *  FROM incidencias where idTramo = $idTramo");
                while ($row = $incidenciasTramo->fetch_array(MYSQLI_ASSOC)) {
                    $incidenciasTramoResponse[] = $row;
                }

                $updateEstatus =  "UPDATE tramos SET estatus='Bloqueado' WHERE id = $idTramo;";
                if ($conexion->query($updateEstatus) === TRUE) {

                    $payload = ["sql" => "Exito Insert record successfully", "incidencias" => $incidenciasTramoResponse, "updateTramo" => "Exito update"];

                    respuesta(200, 200,  "Respuesta exitosa", $payload);
                    // respuesta(400, 404, "Hubo un rechazo. Llama a central para poder continuar con tu viaje.", $payload);
                } else {
                    $payload = ["sql" => "Error: " . $updateEstatus . "<br>" . $conexion->error];
                    respuesta(500, 500,  "Hay un error con el servidor. Llama a central Error-FTUPD", $payload);
                }
            } else {
                $payload = ["sql" => "Error: " . $insertIncidencia . "<br>" . $conexion->error];
                respuesta(500, 500,  "Hay un error con el servidor. Llama a central Error-INUPD", $payload);
            }
        }
    }
} else {
    $payload = ["Faltantes" => $faltantes];
    respuesta(400, 400, "Hay un error con el servidor. Llama a central Error-INRE1", $payload);
}
