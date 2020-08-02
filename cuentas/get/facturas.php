<?PHP
//datos .env
include '../production.php';

//header
header('Access-Control-Allow-Origin: *');
header("Content-Type: application/json; charset=UTF-8");

//const 
$faltantes = [];
$siguiente = true;

//datos Request
$datos = json_decode(file_get_contents('php://input'), true);
$id = $datos["id"];

//funciones
function respuesta($codehttp, $code, $mensaje, $factload)
{
    http_response_code($codehttp);
    $dataFinal = [
        "headerResponse" => [
            "code" => $code,
            "mensaje" => $mensaje
        ],
        "payload" => $factload
    ];
    echo json_encode($dataFinal);
}

//Validacion de Datos

if (empty($faltantes)) {
    //Conexion a base de datos
    $mysqli = mysqli_init();
    $conexion = mysqli_connect($_SESSION['HOST'], $_SESSION['USER'], $_SESSION['PASS'], $_SESSION['DBNAME']);
    //Validacion conexion con bd
    if (!$conexion) {
        respuesta(500, 500, "Hay un error con el servidor. Llama a central Error-CONECTION-DATA", []);
    } else {
        //configuracon db
        mysqli_query($conexion, "SET CHARACTER SET 'utf8'");
        mysqli_query($conexion, "SET SESSION collation_connection ='utf8_unicode_ci'");

        //Consulta viajes
        $consulta = "SELECT id,cliente , viaje, precio,sum(precio+gEstadias+gManiobras+Adicionales+desviacion) monto, sum(gEstadias+gManiobras+Adicionales+desviacion) Adic, sum(gManiobras) gManiobras, factura,fecFacturacion, montoPagado FROM operaciones 
		where  status ='27' and factura <> 'N/A' and year(fecFacturacion)='2020'  group by viaje ORDER BY fecFacturacion  DESC, viaje DESC";
        if ($id !== null) {
            $consulta = "SELECT id,cliente , viaje, precio,sum(precio+gEstadias+gManiobras+Adicionales+desviacion) monto, sum(gEstadias+gManiobras+Adicionales+desviacion) Adic, factura,fecFacturacion, montoPagado FROM operaciones 
		where status ='27' and factura <> 'N/A' and year(fecFacturacion)='2020' and id=$id  group by viaje ORDER BY fecFacturacion  DESC, viaje DESC";
        }

        $facturas = mysqli_query($conexion, $consulta);
        while ($row = $facturas->fetch_array(MYSQLI_ASSOC)) {
            if ($row['cliente'] =='Dicka' && $row['viaje'] > '13040') {
			   if ($row['monto'] > 0) {
				   
                $row['monto'] = round($row['monto'] + ($row['monto'] * .16) - ($row['precio'] * .04) - ($row['gManiobras'] * .06) , 2);
                $row['montoPagado'] = round($row['montoPagado'], 2);
                $datafacturas[] = $row;
              }
			} else{	
    		   if ($row['monto'] > 0) {
                $row['monto'] = round($row['monto'] + ($row['monto'] * .16) - ($row['precio'] * .04), 2);
                $row['montoPagado'] = round($row['montoPagado'], 2);
                $datafacturas[] = $row;
              }
			} 
        }

        //Response
        if (empty($datafacturas)) {
            respuesta(200, 404, "No existen facturas ", []);
        } else {
            //Consulta viajes
            $consultaDiltex = "SELECT id,cliente, viaje, precio, IF(date(fecha) >'2020-01-14', sum(cajas * 86) ,sum(cajas * 80) ) AS monto, sum(gEstadias+gManiobras+Adicionales+desviacion) Adic, factura,fecFacturacion, montoPagado FROM operaciones WHERE cajas <> '' and cliente like '%diltex ilu%' and status not in ('Carga*','99','Cancelado') and (destino like '%418%' or destino like '%506%' or entregar like '%418%' or entregar like '%506%') and status ='27' and factura <> 'N/A' and year(fecFacturacion)='2020' Group by viaje ORDER BY fecFacturacion  DESC, viaje DESC";
            if ($id !== null) {
                $consultaDiltex = "SELECT id,cliente, viaje, precio, IF(date(fecha) >'2020-01-14', sum(cajas * 86) ,sum(cajas * 80) ) AS monto, sum(gEstadias+gManiobras+Adicionales+desviacion) Adic, factura,fecFacturacion, montoPagado FROM operaciones WHERE cajas <> '' and cliente like '%diltex ilu%' status not in ('Carga*','99','Cancelado') and (destino like '%418%' or destino like '%506%' or entregar like '%418%' or entregar like '%506%') and status ='27' and factura <> 'N/A' and year(fecFacturacion)='2020' and id=$id Group by viaje ORDER BY fecFacturacion  DESC, viaje DESC";
            }
            $facturasDiltex = mysqli_query($conexion, $consultaDiltex);
            while ($row = $facturasDiltex->fetch_array(MYSQLI_ASSOC)) {
                $row['monto'] = round($row['monto'] + ($row['monto'] * .16) - ($row['monto'] * .04), 2);
                $row['montoPagado'] = round($row['montoPagado'], 2);
                $datafacturas[] = $row;
            }

            //Response
            if (empty($datafacturas)) {
                respuesta(200, 404, "No existen facturas Diltex", []);
            } else {
                $payload = $datafacturas;
                respuesta(200, 200, "Respuesta exitosa", $payload);
            }
        }
    }
} else {
    $payload = ["Faltantes" => $faltantes];
    respuesta(400, 400, "Hay un error con el servidor. Llama a central Error-CU-BAD-REQUEST", $payload);
}
