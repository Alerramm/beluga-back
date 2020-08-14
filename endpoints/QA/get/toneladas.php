<?PHP
include '../production.php';
header('Access-Control-Allow-Origin: *');
header("Content-Type: application/json; charset=UTF-8");
//Conexion a base de datos
$mysqli = mysqli_init();
$conexion = mysqli_connect($_SESSION['HOST'], $_SESSION['USER'], $_SESSION['PASS'], $_SESSION['DBNAME']);
//$conexion = mysqli_connect("localhost", "root", "", "dbo574183143");
//Query para obtener clientes
$consulta =  "SELECT DISTINCT (nombreUnidad) as toneladas FROM unidadesNueva";
$toneladas =  mysqli_query($conexion, $consulta);

while ($row = $toneladas->fetch_array(MYSQLI_ASSOC)) {
  $data[] = $row;
}
http_response_code(200);
echo json_encode($data);
