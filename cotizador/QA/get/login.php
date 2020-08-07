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
$user = $datos["user"];
$pass = $datos["password"];

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
if ($user == "") {
    array_push($faltantes, 'user');
}
if ($pass == "") {
    array_push($faltantes, 'password');
}

if (empty($faltantes)) {
    //Conexion a base de datos
    $mysqli = mysqli_init();
    $conexion = mysqli_connect($_SESSION['HOST'], $_SESSION['USER'], $_SESSION['PASS'], $_SESSION['DBNAME']);
    //Validacion conexion con bd
    if (!$conexion) {
        respuesta(500, 500, "Hay un error con el servidor. Llama a central Error-LOBD1", []);
    } else {
        //configuracon db
        mysqli_query($conexion, "SET CHARACTER SET 'utf8'");
        mysqli_query($conexion, "SET SESSION collation_connection ='utf8_unicode_ci'");

        //Analisis de la informacion
        //nombre,rfc,telefono, email, contacto, razonSocial,tipoCliente
        //usuarios (id,usuario ,password,nombre,mail,perfil)
        $consulta =  "SELECT B.id as idCliente ,A.usuario,A.password,A.nombre,A.mail,A.perfil,B.nombre, B.rfc, B.telefono, B.email, B.contacto, B.razonSocial,B.tipoCliente  FROM usuarios A INNER JOIN clientes B ON A.id = B.idUsuario WHERE A.usuario='$user' and A.password='$pass' and A.perfil='CLIENTE'";

        $usuarios =  mysqli_query($conexion, $consulta);
        $row = mysqli_fetch_array($usuarios, MYSQLI_ASSOC);






        //Response
        if (empty($row)) {
            respuesta(200, 404, "Usuario o contraseña incorrecto", []);
        } else {
            respuesta(200, 200, "Respuesta exitosa", $row);
        }
    }
} else {
    $payload = ["Faltantes" => $faltantes];
    respuesta(400, 400, "Hay un error con el servidor. Llama a central Error-LORE1", $row);
}
