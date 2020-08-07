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

//Validacion de Datos
if ($idTramo == "") {
    array_push($faltantes, 'idTramo');
}


if (empty($faltantes)) {
    //Conexion a base de datos
    $mysqli = mysqli_init();
    $conexion = mysqli_connect($_SESSION['HOST'], $_SESSION['USER'], $_SESSION['PASS'], $_SESSION['DBNAME']);
    //Validacion conexion con bd
    if (!$conexion) {
        respuesta(500, 500, "Hay un error con el servidor. Llama a central Error-TABD1", []);
    } else {
        //configuracon db
        mysqli_query($conexion, "SET CHARACTER SET 'utf8'");
        mysqli_query($conexion, "SET SESSION collation_connection ='utf8_unicode_ci'");

        //Analisis de la informacion
        $row = consulta($conexion, "SELECT * FROM tramos where id = $idTramo");

        if (empty($row)) {
            respuesta(200, 404, "No hay registros con este ID de tramo" . $id, []);
        } else {
            $idViaje = $row[0]["idviaje"];
            $tramo = $row[0]["tramo"];

            $tramosDB = consulta($conexion, "SELECT * FROM tramos WHERE idviaje = $idViaje");

            if (count($tramosDB) == $tramo) {
                $updateEstatusViaje =  "UPDATE viajes SET estatus = 'Evidencia completa' WHERE id = '$idViaje'";
                if ($estatus == 'Bloqueado') {
                    $updateEstatusViaje =  "UPDATE viajes SET estatus = 'Evidencia incompleta' WHERE id = '$idViaje'";
                }


                if ($conexion->query($updateEstatusViaje) === TRUE) {

                    $payload[] = ["sqlViaje" => "Exito Update record successfully " . $idViaje];
                } else {
                    $payload[] = ["sqlViaje" => "Error: " . $updateEstatus . "<br>" . $conexion->error];
                }
            }

            //Insert
            $updateEstatus =  "UPDATE tramos SET estatus = '$estatus' WHERE id = $idTramo";

            if ($conexion->query($updateEstatus) === TRUE) {

                $payload[] = ["sqlTramo" => "Exito Update record successfully" . $idTramo];

                respuesta(200, 200,  "Respuesta exitosa", $payload);
            } else {
                $payload = ["sqlTramo" => "Error: " . $updateEstatus . "<br>" . $conexion->error];
                respuesta(500, 500,  "Hay un error con el servidor. Llama a central Error-TAUPD", $payload);
            }
        }
    }
} else {
    $payload = ["Faltantes" => $faltantes];
    respuesta(400, 400, "Hay un error con el servidor. Llama a central Error-Bad request", $payload);
}
