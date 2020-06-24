<?PHP
include '../production.php';
$datos = json_decode(file_get_contents('php://input'), true);
header('Access-Control-Allow-Origin: *');
header("Content-Type: application/json; charset=UTF-8");
//Conexion a base de datos
$mysqli = mysqli_init();
$conexion = mysqli_connect($_SESSION['HOST'], $_SESSION['USER'], $_SESSION['PASS'], $_SESSION['DBNAME']);
mysqli_query($conexion, "SET CHARACTER SET 'utf8'");
mysqli_query($conexion, "SET SESSION collation_connection ='utf8_unicode_ci'");

$fechaInicialV_unix = strtotime('-4 hour', strtotime($datos["fecha_inicial"]));
$fechaInicialV = gmdate("Y-m-d\TH:i:s\Z", $fechaInicialV_unix);
$datos["fecha_inicial"] = $fechaInicialV;
$fechaFinalV_unix = strtotime('-5 hour', strtotime($datos["fecha_final"]));
$fechaFinalV = gmdate("Y-m-d\TH:i:s\Z", $fechaFinalV_unix);
$datos["fecha_final"] = $fechaFinalV;

if ($fechaInicialV == null || $fechaFinalV == null) {
  $data[] = [
    "nombre" => "Selecciona Fecha Entrega y Disponibilidad"
  ];
  http_response_code(200);
  echo json_encode($data);
} else {
  /*   $consultaDisponibilidad = "SELECT nombre FROM intinerarioConductores WHERE fechaInicial  BETWEEN '$fechaInicialV' AND '$fechaFinalV' OR fechaFinal BETWEEN '$fechaInicialV' AND '$fechaFinalV'";
 */
  $consultaDisponibilidad = "SELECT DISTINCT(nombre) FROM intinerarioConductores WHERE (fechaInicial  <= '$fechaInicialV' AND fechaFinal >= '$fechaFinalV') OR (fechaInicial BETWEEN '$fechaInicialV' AND '$fechaFinalV') OR (fechaFinal BETWEEN '$fechaInicialV' AND '$fechaFinalV')";
  //$conexion = mysqli_connect("localhost", "root", "", "dbo574183143");
  //Query para obtener clientes
  $consulta =  "SELECT DISTINCT(nombre) FROM empleados where puesto = 'OPERADOR' AND activo = 1";
  $conductores =  mysqli_query($conexion, $consulta);
  $conductoresD =  mysqli_query($conexion, $consultaDisponibilidad);
  while ($row2 = $conductoresD->fetch_array(MYSQLI_ASSOC)) {
    $data2[] = $row2;
  }
  while ($row = $conductores->fetch_array(MYSQLI_ASSOC)) {
    if (in_array($row, $data2)) {
      /* $data3[] = $row; */
    } else {
      $data[] = $row;
    }
  }

  /*   $dataFinal["Disponibles"] = count($data);
  $dataFinal["NoDisponibles"] = count($data3); */

  if ($data == null) {
    $data[] = [
      "nombre" => "No hay unidades disponibles"
    ];
    http_response_code(200);
    echo json_encode($data);
  } else {
    //$data["REQUEST"]= $datos;
    http_response_code(200);
    echo json_encode($data);
  }
}
