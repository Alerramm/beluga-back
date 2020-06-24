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

//VAriables del post
$fechaInicialV_unix = strtotime('-4 hour', strtotime($datos["fecha_inicial"]));
$fechaInicialV = gmdate("Y-m-d\TH:i:s\Z", $fechaInicialV_unix);
$datos["fecha_inicial"] = $fechaInicialV;
$fechaFinalV_unix = strtotime('-5 hour', strtotime($datos["fecha_final"]));
$fechaFinalV = gmdate("Y-m-d\TH:i:s\Z", $fechaFinalV_unix);
$datos["fecha_final"] = $fechaFinalV;
$toneladas = $datos["toneladas"];
$tipo = $datos["tipo"];


if ($fechaInicialV == null || $fechaFinalV == null) {
  $data[] = [
    "camion" => "Selecciona Fecha Entrega y Disponibilidad"
  ];
  http_response_code(200);
  echo json_encode($data);
} else {
  $consultaToneladasTipo = "SELECT camion,ejes,rendimientoLocal,rendimientoForaneo FROM unidades WHERE toneladas ='$toneladas' AND tipo = '$tipo' and activa ='Si'";
  $consultaDisponibilidad = "SELECT distinct (camion) FROM intinerarioUnidades WHERE (fechaInicial  <= '$fechaInicialV' AND fechaFinal >= '$fechaFinalV') OR (fechaInicial BETWEEN '$fechaInicialV' AND '$fechaFinalV') OR (fechaFinal BETWEEN '$fechaInicialV' AND '$fechaFinalV')";
  //$conexion = mysqli_connect("localhost", "root", "", "dbo574183143");
  //Query para obtener clientes
  $unidadesDisponibles =  mysqli_query($conexion, $consultaDisponibilidad);
  $unidadesToneladas =  mysqli_query($conexion, $consultaToneladasTipo);
  while ($row2 = $unidadesDisponibles->fetch_array(MYSQLI_ASSOC)) {
    $data2[] = $row2;
  }
  if (!empty($data2)) {
    while ($row = $unidadesToneladas->fetch_array(MYSQLI_ASSOC)) {
      $var = true;
      foreach ($data2 as &$valor) {
        if ($valor["camion"] == $row["camion"]) {
          $var = false;
        }
      }
      if ($var) {
        $data[] = $row;
      }
    }
  } else {
    while ($row = $unidadesToneladas->fetch_array(MYSQLI_ASSOC)) {
      $data[] = $row;
    }
  }


  /*   $dataFinal["Disponibles"] = count($data);
  $dataFinal["NoDisponibles"] = count($data3); */

  if (empty($data)) {
    $data[] = [
      "camion" => "No hay unidades disponibles"
    ];
    http_response_code(200);
    echo json_encode($data);
  } else {
    //$data["REQUEST"]= $datos;
    http_response_code(200);
    echo json_encode($data);
  }
}
