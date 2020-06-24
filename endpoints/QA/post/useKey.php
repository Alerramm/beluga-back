<?PHP
$datos = json_decode(file_get_contents('php://input'), true);
$idKey = $datos["idKey"];
include '../production.php';
/* echo json_encode($data); */
header('Access-Control-Allow-Origin: *');
header("Content-Type: application/json; charset=UTF-8");
//Conexion a base de datos
$mysqli = mysqli_init();
$conexion = mysqli_connect($_SESSION['HOST'], $_SESSION['USER'], $_SESSION['PASS'], $_SESSION['DBNAME']);
//variables globales
mysqli_set_charset($conexion, 'utf8');
$select = "SELECT * from llaveTollGuru where id = $idKey";
$llave =  mysqli_query($conexion, $select);
$row = mysqli_fetch_assoc($llave);
$numero = $row["conteoUso"] + 1;
$Update = "UPDATE llaveTollGuru SET conteoUso = '$numero' WHERE id = $idKey";

//Insercion de viaje
if ($conexion->query($Update) === TRUE) {
    $respuestaFinal = 200;
} else {
    $respuestaFinal = 500;
    $res = "Error: " . $Update . "<br>" . $conexion->error;
    $dataFinal["sql"] = $res . " Algo paso";
}
$llave2 =  mysqli_query($conexion, $select);
$row2 = mysqli_fetch_assoc($llave2);
$dataFinal["status"] = $respuestaFinal;
$dataFinal["response"] = $row2;
http_response_code($respuestaFinal);
echo json_encode($dataFinal);
