<?PHP
include '../production.php';
header('Access-Control-Allow-Origin: *');
header("Content-Type: application/json; charset=UTF-8");

//Conexion a base de datos
$mysqli = mysqli_init();
$conexion = mysqli_connect($_SESSION['HOST'], $_SESSION['USER'], $_SESSION['PASS'], $_SESSION['DBNAME']);
mysqli_query($conexion, "SET CHARACTER SET 'utf8'");
mysqli_query($conexion, "SET SESSION collation_connection ='utf8_unicode_ci'");

//Query para obtener clientes
$consulta =  "SELECT nombre FROM ciudades";

$base =  mysqli_query($conexion, $consulta);

while ($row = $base->fetch_array(MYSQLI_ASSOC)) {
    $data[] = $row;
}

if (empty($data)) {
    $data = array(
        'estatus' => 500,
        'mensaje' => "No hay ciudades disponibles",
    );
    http_response_code(500);
    echo json_encode($data);
} else {
    http_response_code(200);
    echo json_encode($data);
}
