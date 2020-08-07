<?PHP
//datos .env
include '../production.php';

//header
header('Access-Control-Allow-Origin: *');
header("Content-Type: application/json; charset=UTF-8");

//const 
$faltantes = [];
$fe1 = date("Y-m-d");

//datos Request
$datos = json_decode(file_get_contents('php://input'), true);
$tipo = $datos["tipo"];
$correo = $datos["correo"];
$password = $datos["password"];
$telefono = $datos["telefono"];
$razonSocial = $datos["razonSocial"];
$rfc = $datos["rfc"];
$dirFiscal = $datos["dirFiscal"];
$contacto = $datos["contacto"];


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
if ($tipo == "") {
    array_push($faltantes, 'tipo');
}
if ($correo == "") {
    array_push($faltantes, 'correo');
}
if ($password == "") {
    array_push($faltantes, 'password');
}
if ($telefono == "") {
    array_push($faltantes, 'telefono');
}
if ($contacto == "") {
    array_push($faltantes, 'contacto');
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
        $consulta =  "SELECT * FROM usuarios A INNER JOIN clientes B ON A.id = B.idUsuario WHERE A.usuario='$correo' and A.password='$password' and A.perfil='CLIENTE'";
        $usuariosDB =  mysqli_query($conexion, $consulta);
        $row = mysqli_fetch_array($usuariosDB, MYSQLI_ASSOC);

        if (!empty($row)) {
            respuesta(200, 404, "Ya existe un usuario registrado con correo " . $correo, []);
        } else {
            //Update
            $insertUsuarios =  "INSERT INTO usuarios (usuario, password, nombre, fechaAlta, perfil, mail) VALUES ('$correo', '$password', '$contacto', '$fe1', 'CLIENTE', '$correo')";



            if ($conexion->query($insertUsuarios) === TRUE) {
                $last_id = $conexion->insert_id;

                $insertCliente = "INSERT INTO clientes (nombre, rfc, domicilio, domCarga, razonSocial, telefono, contacto, email, tipoCliente, idUsuario) VALUES ('$contacto', '$rfc', '$dirFiscal', '$dirFiscal', '$razonSocial', '$telefono', '$contacto', '$correo', '$tipo', $last_id)";


                if ($conexion->query($insertCliente) === TRUE) {
                    $last_idCliente = $conexion->insert_id;

                    $insertRestricciones = "INSERT INTO usuario_resticciones (idUsuario, precio_prepago_premium, precio_postpago_premium, precio_postpago_basico, precio_contrato) VALUES ( $last_id, 1, 0, 0, 0)";


                    if ($conexion->query($insertRestricciones) === TRUE) {
                        $last_idClienteRestricciones = $conexion->insert_id;


                        $payload = ["user" => $correo, "pass" => $password];

                        respuesta(200, 200,  "Respuesta exitosa", $payload);
                    } else {
                        $payload = ["sql" => "Error: " . $insertRestricciones . "<br>" . $conexion->error];
                        respuesta(500, 500,  "Hay un error con el servidor. Llama a central Error-TAUPD", $payload);
                    }
                } else {
                    $payload = ["sql" => "Error: " . $insertCliente . "<br>" . $conexion->error];
                    respuesta(500, 500,  "Hay un error con el servidor. Llama a central Error-TAUPD", $payload);
                }
            } else {
                $payload = ["sql" => "Error: " . $insertUsuarios . "<br>" . $conexion->error];
                respuesta(500, 500,  "Hay un error con el servidor. Llama a central Error-TAUPD", $payload);
            }
        }
    }
} else {
    $payload = ["Faltantes" => $faltantes];
    respuesta(400, 400, "Hay un error con el servidor. Llama a central BAD REQUEST", $payload);
}
