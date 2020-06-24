

<?PHP
//datos .env

//header
header('Access-Control-Allow-Origin: *');
header("Content-Type: application/json; charset=UTF-8");

//const 
$faltantes = [];
$modulos = [
    "LO" => "login",
    "TR" => "travel",
    "CL" => "checklist",
    "TA" => "travelAccept",
    "GA" => "gastos",
    "FT" => "finalizarTramo",
    "CA" => "casetas"
];
$mensajes = [
    "BD1" => "Se perdio la conexion con la base de datos",
    "RE1" => "Request Incorrecto",
    "UPD" => "Error al realizar una modificacion a la base de datos",
    "EVI" => "El conductor tiene mas de un viaje asignado",
    "ETO" => "El conductor tiene mas de un viaje en la tabla tempOperacion",
    "VID" => "No existe viaje con id ",
    "TOD" => "No existe viaje en tempOperacion con idViaje ",
    "TRI" => "No existe tramo con id ",
    "TRD" => "No existe tramos relacionado a el viaje con id ",
    "OPD" => "No existe viaje en operaciones con id ",
    "VSI" => "No tiene un estatus valido el viaje con id ",
    "BOD" => "No existen registro con la direccion de la base de operaciones que indica el viaje con id ",
    "CLD" => "No existen registro con el direccion de el cliente que indica el viaje con cliente ",
];

//datos Request
$datos = json_decode(file_get_contents('php://input'), true);
$codigo = $datos["codigo"];

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
if ($codigo == "") {
    array_push($faltantes, 'codigo');
}

if (empty($faltantes)) {

    $codigoMod = substr($codigo, -strlen($codigo), 2);
    switch ($codigoMod) {
        case "LO":
            $idViaje = substr($codigo, -strlen($codigo) + 5, strlen($codigo));
            $codigoMen = substr($codigo, -strlen($codigo) + 2, 3);
            $existe = true;
            break;

        case "TR":
            $idViaje = substr($codigo, -strlen($codigo) + 5, strlen($codigo));
            $codigoMen = substr($codigo, -strlen($codigo) + 2, 3);
            $existe = true;
            break;

        case "CL":
            $idViaje = substr($codigo, -strlen($codigo) + 5, strlen($codigo));
            $codigoMen = substr($codigo, -strlen($codigo) + 2, 3);
            $existe = true;
            break;

        case "TA":
            $idViaje = substr($codigo, -strlen($codigo) + 5, strlen($codigo));
            $codigoMen = substr($codigo, -strlen($codigo) + 2, 3);
            $existe = true;
            break;

        case "GA":
            $idViaje = substr($codigo, -strlen($codigo) + 5, strlen($codigo));
            $codigoMen = substr($codigo, -strlen($codigo) + 2, 3);
            $existe = true;
            break;

        case "FT":
            $idViaje = substr($codigo, -strlen($codigo) + 5, strlen($codigo));
            $codigoMen = substr($codigo, -strlen($codigo) + 2, 3);
            $existe = true;
            break;

        case "CA":
            $idViaje = substr($codigo, -strlen($codigo) + 5, strlen($codigo));
            $codigoMen = substr($codigo, -strlen($codigo) + 2, 3);
            $existe = true;
            break;

        default:
            respuesta(200, 404, "No existe el modulo " .  $codigoMod . " en el codigo " . $codigo, []);
            $existe = false;
    }

    if ($existe) {
        if ($mensajes[$codigoMen] != null) {
            if ($idViaje == $codigo) {
                $idViaje = "";
            }
            respuesta(200, 200, "Respuesta exitosa", ["modulo" => $modulos[$codigoMod], "mensaje" => $mensajes[$codigoMen] . $idViaje]);
        } else {
            respuesta(200, 404, "No existe el mensaje " .  $codigoMen . " en el codigo " . $codigo, []);
        }
    }
} else {
    $payload = ["Faltantes" => $faltantes];
    respuesta(400, 400, "Faltantes", $payload);
}
