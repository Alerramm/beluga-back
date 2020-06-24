<?PHP
include '../production.php';
header('Access-Control-Allow-Origin: *');
header("Content-Type: application/json; charset=UTF-8");
//Conexion a base de datos
$mysqli = mysqli_init();
$conexion = mysqli_connect($_SESSION['HOST'], $_SESSION['USER'], $_SESSION['PASS'], $_SESSION['DBNAME']);
//Query para obtener clientes
$consulta =  "SELECT DISTINCT (modelo),modelo2,ejes FROM unidades";
$modelos =  mysqli_query($conexion, $consulta);

while ($row = $modelos->fetch_array(MYSQLI_ASSOC)) {
  if ($row['modelo'] == $row['modelo2']) {
    $data[] = $row;
  } else {
    $row['modelo'] = $row['modelo'] . " " . $row['modelo2'];
    $data[] = $row;
  }
}
http_response_code(200);
echo json_encode($data);
