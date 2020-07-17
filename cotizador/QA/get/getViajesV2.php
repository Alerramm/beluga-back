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
$cliente = $datos["cliente"];

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
if ($cliente  == null) {
    array_push($faltantes, 'cliente');
}

if (!empty($faltantes)) {
    $payload = ["Faltantes" => $faltantes];
    respuesta(400, 400, "Hay un error con el servidor. Llama a central Error-TRRE1", $payload);

}else{

    $mysqli = mysqli_init();
    $conexion = mysqli_connect($_SESSION['HOST'], $_SESSION['USER'], $_SESSION['PASS'], $_SESSION['DBNAME']);
    //Validacion conexion con bd
    if (!$conexion) {
        respuesta(500, 500, "Hay un error con el servidor. Llama a central Error-TRBD1", []);
    } else {
        //configuracon db
        mysqli_query($conexion, "SET CHARACTER SET 'utf8'");
        mysqli_query($conexion, "SET SESSION collation_connection ='utf8_unicode_ci'");

        //Consulta viajes
      


        $consulta =  "SELECT A.id, A.fecha_carga, A.unidad  , B.idTipoPrecio, B.precio
        FROM viajes A INNER JOIN precio_viaje B on A.id = B.idViaje
        WHERE A.cliente ='$cliente'  
        AND A.estatus IN ('Gastos', 'En proceso cliente', 'En proceso', 'En trayecto') ;";
        
        $viaje =  mysqli_query($conexion, $consulta);

        while ($row = $viaje->fetch_array(MYSQLI_ASSOC)) {
                $idViaje = $row["id"];
                    $consulta2 =  "SELECT destino FROM tramos where idViaje ='$idViaje' ";
                    $tramo =  mysqli_query($conexion, $consulta2);
                    while ($row2 = $tramo->fetch_array(MYSQLI_ASSOC)) {
                      

                         $tramos [] = $row2;
                        
                         }
                    $row ["tramosobject"] = $tramos;
                    $viajetotal [] = $row;  

            
        }




        



        //Response
        if (empty($viajetotal)) {
            respuesta(200, 404, "No hay viajes asignados para esta consulta", []);
        } else {
                  respuesta(200, 200, "Respuesta exitosa", $viajetotal);
        }
    }
}
