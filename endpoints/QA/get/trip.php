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
$consulta = "SELECT * FROM `viajes_guardados` WHERE cliente = '$cliente'  AND estatus = 'Activo'";
$trips =  mysqli_query($conexion, $consulta);
while ($row = $trips->fetch_array(MYSQLI_ASSOC)) {
    $data1 = [];
    $data2 = [];
    $idViaje = $row["idViaje"];
    $consultaViaje = "SELECT base from viajes where id = $idViaje";
    $viaje = mysqli_query($conexion, $consultaViaje);
    while ($row2 = $viaje->fetch_array(MYSQLI_ASSOC)) {
        $data1[] = $row2;
    }
    $consultaTramos =  "SELECT origen, entrega, waypoints FROM tramos where idViaje = $idViaje";
    $tramos = mysqli_query($conexion, $consultaTramos);
    while ($row3 = $tramos->fetch_array(MYSQLI_ASSOC)) {
        $data2[] = $row3;
    }
    $dataFinal[] = [
        "id" => $row["id"],
        "name" => $row["nombre"],
        "travel" => $data1,
        "tracts" => $data2
    ];
}



http_response_code(200);
echo json_encode($dataFinal);
