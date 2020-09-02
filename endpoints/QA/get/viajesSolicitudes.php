<?php
$datos = json_decode(file_get_contents('php://input'), true);
$cliente = $datos["cliente"];
include '../production.php';
header('Access-Control-Allow-Origin: *');
header("Content-Type: application/json; charset=UTF-8");
//Conexion a base de datos
$mysqli = mysqli_init();
$conexion = mysqli_connect($_SESSION['HOST'], $_SESSION['USER'], $_SESSION['PASS'], $_SESSION['DBNAME']);
mysqli_query($conexion, "SET CHARACTER SET 'utf8'");
mysqli_query($conexion, "SET SESSION collation_connection ='utf8_unicode_ci'");

//Query para obtener clientes
$consulta = "SELECT v.base as base_operaciones, v.* FROM `viajes` v WHERE v.estatus = 'Asignacion'";
$trips =  mysqli_query($conexion, $consulta);
while ($row = $trips->fetch_array(MYSQLI_ASSOC)) {
    $data1 = [];
    $data2 = [];
    $idViaje = $row["id"];
    $cliente = $row["cliente"];
    $consultaTramos =  "SELECT * FROM tramos where idViaje = $idViaje";
    $tramos = mysqli_query($conexion, $consultaTramos);
    while ($row3 = $tramos->fetch_array(MYSQLI_ASSOC)) {
        $data2[] = $row3;
        $cont = 0;
        $domCarga = $row3["entrega"];
        if ($cont == 0) {
            $row["cliente"] = ["domCarga" => $domCarga, "nombre" => $cliente];
            $cont = 1;
        }
    }
    $dataFinal[] = [
        "name" => $idViaje,
        "travel" => [0 => $row],
        "tracts" => $data2
    ];
}



http_response_code(200);
echo json_encode($dataFinal);
