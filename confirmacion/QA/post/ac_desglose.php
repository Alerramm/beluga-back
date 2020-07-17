<?php
session_start(); 
$userL = $_SESSION['user']; 
$fecha=date("Y-m-d");
//print_r ($_GET);
//$_GET['viaje']='12649';
//$_GET['tipo']='diesel';

$mysqli = mysqli_init();
$mysqli->options(mysqlI_OPT_CONNECT_TIMEOUT, 5);
$mysqli->set_charset("utf8");
$enlace = mysqli_connect("db574183143.db.1and1.com", "dbo574183143", "b3lug42015", "db574183143");
//print_r($_GET);
//QA
$enlace = mysqli_connect("db5000311817.hosting-data.io", "dbu13503", "belugaQA20.", "dbs304381");
 
//$_GET['catId']='4481';
// $_GET['refeCat']='99999';
//********************

if(isset($_GET['catIdAut'])){
  $ident= $_GET['catIdAut'];
  mysqli_query($enlace,"UPDATE relacionGastos SET status='',autoriza='$userL'   WHERE id ='$ident'");

  header("Location: desgloseGastosAutorizacion.php");
} //catID	
//********eLIMINA***********

if(isset($_GET['nreg'])){
  $ident= $_GET['nreg'];
   mysqli_query($enlace,"DELETE  FROM desgloseGastosAut   WHERE id ='$ident'");

  header("Location: desgloseGastosAutorizacion.php");
} //catID

if(isset($_GET['nregCat'])){
  $ident= $_GET['nregCat'];
   mysqli_query($enlace,"DELETE  FROM relacionGastos   WHERE id ='$ident'");

  header("Location: desgloseGastosAutorizacion.php");
} //catID

//********************

if(isset($_GET['catId'])){
  $ident= $_GET['catId'];
  $tipo= $_GET['tipoCat'];
  $refer= $_GET['refeCat'];
  $factu= $_GET['factCat'];
$ProdRel='';
   mysqli_query($enlace,"UPDATE relacionGastos SET referencia='".$refer."',tipoGasto='".$tipo."',factura='".$factu."',status='Depositado' ,deposita='".$userL."' WHERE id ='$ident'");

 $ssqlD="select categoria ,importe,fecha,pedido ,idProd,idReg from relacionGastos  WHERE id ='$ident'";
//echo $ssqlD;
 $qryD = mysqli_query($enlace,$ssqlD);
  while($arrD = mysqli_fetch_assoc($qryD))
   {
     $catR=$arrD['categoria'];
	 $impR=$arrD['importe'];
	 $fecR=$arrD['fecha'];
     $noPed=$arrD['pedido'];
     $ProdRel=$arrD['idProd'];	
     $ProdReg=$arrD['idReg'];		 
   }
   
if ($catR == 'Mantenimiento'){

	if ($ProdRel!=''){
		$produc="and idProducto ='$ProdRel' and id='$ProdReg'";
	} else {
		$produc="";
			
	}
 
$cnt_qry = mysqli_query($enlace,"SELECT 
numSol,proved,idProducto,cantidad,precioU,unidad,depositoA,id FROM pedidoDetalle WHERE noPedido='$noPed' and folioPed ='' $produc");
	while ($rowP= mysqli_fetch_assoc($cnt_qry)){
	$numrows = $rowP['numrows'];
	$numSol = $rowP['numSol'];
	$Auxprov = $rowP['proved'];
	$cant = $rowP['cantidad'];
	$precio = $rowP['precioU'];
	$unid = $rowP['unidad'];
	$idProd = $rowP['idProducto'];
	$depositoA = $rowP['depositoA'];
    $idP = $rowP['id'];
	$tot=$cant*$precio;
 if($Auxprov=='Pla'){
		 $refa='S';
		 $comp='N';
		 $repa='N';
	 } else {
		$refa='N';
		 $comp='S';
		 $repa='N'; 
		 
	 }
   if ( $tot =='$impR'){
	   $totUno="and (cantidad*precioU)='$impR'";
   } else {
	   $totUno=" ";
   }
  $Unidqry = mysqli_query($enlace,"SELECT count(*) AS numrows,solicitud ,id_camion ,id,cantidad,sistema FROM unidades_mantDetalle WHERE
  subsistema !='' and sistema='$idProd'  and refaciones='$refa' and compras='$comp'  limit 1");
	while ($rowU= mysqli_fetch_assoc($Unidqry)){
	$SolicU = $rowU['solicitud'];
	$cantU = $rowU['cantidad'];
	$unidU = $rowU['id_camion'];
	$idU = $rowU['id'];
    $idPrdU = $rowU['sistema'];
	$numren = $rowU['numrows'];
	 $FecTer = $rowU['fecha_termino'];
	}
if ($numren > 0){
for ( $i=1 ; $i<=$cantU ; $i++ ){
  $actual = mysqli_query($enlace,"UPDATE pedidoDetalle SET folioPed='$refer',unidad='$unidU',numSol='$SolicU'  WHERE noPedido='$noPed' and id ='$idP' and idProducto ='$idPrdU' and numSol ='' $totUno limit 1");	
}
  $actual1 = mysqli_query($enlace,"UPDATE unidades_mantDetalle SET subsistema ='', precio='$precio',mecanico='$depositoA',folioDep2='$noPed' ,folioDep='$refer'  WHERE  sistema='$idPrdU' and id='$idU' ");
	
} else {
	 $actual = mysqli_query($enlace,"UPDATE pedidoDetalle SET folioPed='$refer'  WHERE noPedido='$noPed' and id ='$idP'  and idProducto ='$ProdRel' and numSol ='' $totUno limit 1");	
	
} 
	}//while Pedidodetalle
  
	
	  //and cantidad ='$cant'
	

//echo "UPDATE Pedidos SET folioGML='$refer' ,fechaPago='$fecha' ,status='PENDIENTE PROGRA' ,factura='$factu' WHERE noPedido='$noPed'";
$cnt_qry = mysqli_query($enlace,"SELECT count(*) AS numrows,cantidad,precioU,unidad,depositoA FROM pedidoDetalle WHERE noPedido='$noPed' and folioPed =''");
	while ($rowP= mysqli_fetch_assoc($cnt_qry)){
	$cant = $rowP['cantidad'];
	$numrows = $rowP['numrows'];
	$precio = $rowP['precioU'];
	$unid = $rowP['unidad'];
	$idProd = $rowP['idProducto'];
	$depositoA = $rowP['depositoA'];
	$totPed+=$precio*$cant;
	}
	if ($numrows ==0){
		if ($FecTer !='0000-00-00 00:00:00' )
		{$stat='PENDIENTE PROGRA';} else {
			$stat='PENDIENTE PROGRA';}
// "UPDATE Pedidos SET folioGML='$refer' ,fechaPago='$fecha' ,status='PENDIENTE PROGRA' ,factura='$factu' WHERE noPedido='$noPed'";
 $actual = mysqli_query($enlace,"UPDATE Pedidos SET folioGML='$refer' ,fechaPago='$fecha' ,status='$stat' ,factura='$factu' WHERE noPedido='$noPed'");	

 if($totPed===$impR){
//este   $actGto = mysqli_query($enlace,"UPDATE relacionGastos SET comprobado='SI'  WHERE id ='$ident'");	
 }

}

}///mante

  header("Location: desgloseGastos.php");
} //catID
//************************
if(isset($_GET['ident'])){
  $ident= $_GET['ident'];
  
  $IDAUX =$_GET['IDAUX'];
//print_r($_GET); 
  $diesel= $_GET['diesel'];
  $caseta= $_GET['caseta'];
  $alimen= $_GET['alimen'];
  $comis= $_GET['comis'];
  $transi= $_GET['transi'];
  $manio= $_GET['manio'];
  $externo= $_GET['externo'];
  $viati= $_GET['viati'];
 
$Depdiesel= $_GET['Depdiesel'];
$Depcaseta= $_GET['Depcaseta'];
$Depalimen= $_GET['Depalimen'];
$Deptransi= $_GET['Deptransi'];
$Depmanio= $_GET['Depmanio'];
$Depexter= $_GET['Depexter'];
$Depviati= $_GET['Depviati'];

$Foldiesel= $_GET['Foldiesel'];
$Folcaseta= $_GET['Folcaseta'];
$Folalimen= $_GET['Folalimen'];
$Foltransi= $_GET['Foltransi']; 
$Folmani= $_GET['Folmanio'];
$Folexter= $_GET['Folexter'];
$Folviati= $_GET['Folviati']; 

//
//Echo "select * from desgloseGastosAut where viaje='$ident' and id ='$IDAUX'";
$ssqlD="select * from desgloseGastosAut where viaje='$ident'  and id ='$IDAUX'";
$qryD = mysqli_query($enlace,$ssqlD);
  while($arrD = mysqli_fetch_assoc($qryD))
   {
     $preDie+=$arrD['PREdiesel'];
	 $preCas+=$arrD['PREcasetas'];
	 $preAli+=$arrD['PREviaticos'];
	 $preTra+=$arrD['PREtransito'];
	 $preMan+=$arrD['PREmaniobras'];
	 $preEst+=$arrD['PREestadias'];
	 $preRep+=$arrD['PRErepartos'];
   }

$ssqlO="select idViaje from operaciones where viaje='$ident'";

$qryO = mysqli_query($enlace,$ssqlO);
  while($arrD = mysqli_fetch_assoc($qryO))
   {
     $idViaje=$arrD['idViaje'];
   }
   
    mysqli_query($enlace,"UPDATE viajes  set  estatus = 'En proceso cliente' WHERE id = '$idViaje'");
 
  mysqli_query($enlace,"UPDATE desgloseGastos SET idViaje ='$idViaje',
  diesel='".$Depdiesel."',casetas='".$Depcaseta."',alimentos='".$Depalimen."',transito='".$Deptransi."',maniobras='".$Depmanio."',externo='".$Depexter."'viaticos='".$Depviati."'
  pagoDie='".$diesel."',  pagoCas='".$caseta."', pagoAli='".$alimen."',pagoTra='".$transi."',pagoMan='".$manio."',pagoExt='".$externo."',folDiesel='".$Foldiesel."',folCasetas='".$Folcaseta.
  "',folAlimento='".$Folalimen."',folTransito='".$Foltransi."',folManiobras='".$Folmanio."',folExt='".$Folexter."',folVia='".$FolVia."',  status='Realizado' WHERE viaje ='$ident' and id ='$IDAUX'");
// echo "UPDATE desgloseGastos SET idViaje ='$idViaje',diesel='".$Depdiesel."',casetas='".$Depcaseta."',alimentos='".$Depalimen."',transito='".$Deptransi."',maniobras='".$Depmanio."', pagoDie='".$diesel."',  pagoCas='".$caseta."', pagoAli='".$alimen."',pagoTra='".$transi."',pagoMan='".$manio."',folDiesel='".$Foldiesel."',folCasetas='".$Folcaseta."',folAlimentos='".$Folalimen."',folTransito='".$Foltransi."',folManiobras='".$Folmanio."', status='Realizado' WHERE viaje ='$ident' and id ='$IDAUX'";
 
 $tabla='operaciones'; 
	
$ssqlD="select * from desgloseGastos where viaje='$ident' and id ='$IDAUX'";
//echo $ssqlD; 
$qryD = mysqli_query($enlace,$ssqlD);
  while($arrD = mysqli_fetch_assoc($qryD))
   {
     $impDie=$arrD['diesel'];
	 $impCas=$arrD['casetas'];
	 $impAli=$arrD['alimentos'];
	 $impCom=$arrD['comision'];
	 $impTra=$arrD['transito'];
	 $impMan=$arrD['maniobras'];
	 $impExt=$arrD['externo'];
	 $impVia=$arrD['viaticos'];
	 $Oper=$arrD['operador'];
   }
 
   $resultado = substr("$ident", -1, 1);

	//echo $resultado; // imprime "d"
	
	if ($resultado =='R')
	  {$aux=substr("$ident", -1, 1); 
       $ident1= substr("$ident", 0, -1);

 //echo "UPDATE operaciones set status ='26',alimentos  ='".$impAli."' ,casetas  ='".$impCas."' ,diesel  ='".$impDie."' ,transito  ='".$impTra."' ,comision  ='".$impCom."'  WHERE viaje ='$ident1' and anexo='$aux'";  
   mysqli_query($enlace,"UPDATE operaciones set status ='26',alimentos  ='".$impAli."' ,casetas  ='".$impCas."' ,diesel  ='".$impDie."' ,transito  ='".$impTra."' ,maniobras  ='".$impMan."'   WHERE viaje ='$ident1' and anexo='$aux'");

   } else{
// echo  "UPDATE operaciones set status ='26',alimentos  ='".$impAli."' ,casetas  ='".$impCas."' ,diesel  ='".$impDie."' ,transito  ='".$impTra."' ,comision  ='".$impCom."'  WHERE viaje ='$ident'";  
   mysqli_query($enlace,"UPDATE operaciones set status ='26',alimentos  ='".$impAli."' ,casetas  ='".$impCas."' ,diesel  ='".$impDie."' ,transito  ='".$impTra."' ,maniobras  ='".$impMan."' WHERE viaje ='$ident'");
   }
//echo "DEPODIESEL--->".$Depdiesel;  
if ($Depdiesel<> 0 ){
//	echo "select id from gastos where idViaje='$idViaje' and tipo ='Diesel'";
	$ssqlG="select id from gastos where idViaje='$idViaje' and tipo ='Diesel'";
    $qryG = mysqli_query($enlace,$ssqlG);
    while($arrG = mysqli_fetch_assoc($qryG))
   {
     $idGasto=$arrG['id'];
   }
  //The JSON data.
  $Datos = array(
  idGasto=>$idGasto,
  referencia=>$Foldiesel,
  emisor=>'Beluga BBVA',
  receptor=>$Oper,
  estatus=>'Exitoso',
  realiza=>$userL,
  metodoPago=>$diesel,
  cobroCliente=>'0',
  iva=>'0',
  subtotal=>$Depdiesel,
  total=>$Depdiesel
);
//print_r($Datos);
  $url = 'http://www.misistema.mx/beluga/Finanzas/endpoints/confirmacion/QA/post/microDispersion.php';
  $ch = curl_init($url);
  $jsonDataEncoded = json_encode($Datos);
  curl_setopt($ch, CURLOPT_POST, 1);
  curl_setopt($ch, CURLOPT_POSTFIELDS, $jsonDataEncoded);
  //curl_setopt($ch, CURLOPT_HTTPHEADER, array('Content-Type: application/json')); 
  $result = curl_exec($ch); 
//  $data = json_decode( file_get_contents('http://www.misistema.mx/beluga/Finanzas/endpoints/confirmacion/QA/post/microDispersion.php'), true );

}
//casetas************
if ($Depcaseta<> 0 ){
	$ssqlG="select id from gastos where idViaje='$idViaje' and tipo ='Casetas'";
//echo $ssqlG;
    $qryG = mysqli_query($enlace,$ssqlG);
    while($arrG = mysqli_fetch_assoc($qryG))
   {
     $idGasto=$arrG['id'];
   }
  //The JSON data.
  $Datos = array(
  idGasto=>$idGasto,
  referencia=>$Folcaseta,
  emisor=>'Beluga BBVA',
  receptor=>$Oper,
  estatus=>'Exitoso',
  realiza=>$userL,
  metodoPago=>$caseta,
  cobroCliente=>'0',
  iva=>'0',
  subtotal=>$Depcaseta,
  total=>$Depcaseta
);
  $url = 'http://www.misistema.mx/beluga/Finanzas/endpoints/confirmacion/QA/post/microDispersion.php';
  $ch = curl_init($url);
  $jsonDataEncoded = json_encode($Datos);
  curl_setopt($ch, CURLOPT_POST, 1);
  curl_setopt($ch, CURLOPT_POSTFIELDS, $jsonDataEncoded);
  curl_setopt($ch, CURLOPT_HTTPHEADER, array('Content-Type: application/json')); 
  $result = curl_exec($ch); 
//  $data = json_decode( file_get_contents('http://www.misistema.mx/beluga/Finanzas/endpoints/confirmacion/QA/post/microDispersion.php'), true );

}
//Depalimen************
if ($Depalimen<> 0 ){
  $ssqlG="select id from gastos where idViaje='$idViaje' and tipo ='Alimentos'";
//echo $ssqlG;

    $qryG = mysqli_query($enlace,$ssqlG);
    while($arrG = mysqli_fetch_assoc($qryG))
   {
     $idGasto=$arrG['id'];
   }
  //The JSON data.
  $Datos = array(
  idGasto=>$idGasto,
  referencia=>$Folalimen,
  emisor=>'Beluga BBVA',
  receptor=>$Oper,
  estatus=>'Exitoso',
  realiza=>$userL,
  metodoPago=>$alimen,
  cobroCliente=>'0',
  iva=>'0',
  subtotal=>$Depalimen,
  total=>$Depalimen
);
  $url = 'http://www.misistema.mx/beluga/Finanzas/endpoints/confirmacion/QA/post/microDispersion.php';
  $ch = curl_init($url);
  $jsonDataEncoded = json_encode($Datos);
  curl_setopt($ch, CURLOPT_POST, 1);
  curl_setopt($ch, CURLOPT_POSTFIELDS, $jsonDataEncoded);
  curl_setopt($ch, CURLOPT_HTTPHEADER, array('Content-Type: application/json')); 
  $result = curl_exec($ch); 
  ///$data = json_decode( file_get_contents('http://www.misistema.mx/beluga/Finanzas/endpoints/confirmacion/QA/post/microGastos.php'), true );

}
//Deptransi************
if ($Deptransi<> 0 ){
    $ssqlG="select id from gastos where idViaje='$idViaje' and tipo ='Transito'";
    $qryG = mysqli_query($enlace,$ssqlG);
    while($arrG = mysqli_fetch_assoc($qryG))
   {
     $idGasto=$arrG['id'];
   }
  //The JSON data.
  $Datos = array(
  idGasto=>$idGasto,
  referencia=>$Foltransi,
  emisor=>'Beluga BBVA',
  receptor=>$Oper,
  estatus=>'Exitoso',
  realiza=>$userL,
  metodoPago=>$transi,
  cobroCliente=>'0',
  iva=>'0',
  subtotal=>$Deptransi,
  total=>$Deptransi

);
  $url = 'http://www.misistema.mx/beluga/Finanzas/endpoints/confirmacion/QA/post/microDispersion.php';
  $ch = curl_init($url);
  $jsonDataEncoded = json_encode($Datos);
  curl_setopt($ch, CURLOPT_POST, 1);
  curl_setopt($ch, CURLOPT_POSTFIELDS, $jsonDataEncoded);
  curl_setopt($ch, CURLOPT_HTTPHEADER, array('Content-Type: application/json')); 
  $result = curl_exec($ch); 
//  $data = json_decode( file_get_contents('http://www.misistema.mx/beluga/Finanzas/endpoints/confirmacion/QA/post/microDispersion.php'), true );

}
//Depmanio************
if ($Depmanio<> 0 ){
  $ssqlG="select id from gastos where idViaje='$idViaje' and tipo ='Maniobras'";
    $qryG = mysqli_query($enlace,$ssqlG);
    while($arrG = mysqli_fetch_assoc($qryG))
   {
     $idGasto=$arrG['id'];
   }
  //The JSON data.
  $Datos = array(
  idGasto=>$idGasto,
  referencia=>$Folmani,
  emisor=>'Beluga BBVA',
  receptor=>$Oper,
  estatus=>'Exitoso',
  realiza=>$userL,
  metodoPago=>$manio,
  cobroCliente=>'0',
  iva=>'0',
  subtotal=>$Depmanio,
  total=>$Depmanio

);
  $url = 'http://www.misistema.mx/beluga/Finanzas/endpoints/confirmacion/QA/post/microDispersion.php';
  $ch = curl_init($url);
  $jsonDataEncoded = json_encode($Datos);
  curl_setopt($ch, CURLOPT_POST, 1);
  curl_setopt($ch, CURLOPT_POSTFIELDS, $jsonDataEncoded);
  curl_setopt($ch, CURLOPT_HTTPHEADER, array('Content-Type: application/json')); 
  $result = curl_exec($ch); 
  //$data = json_decode( file_get_contents('http://www.misistema.mx/beluga/Finanzas/endpoints/confirmacion/QA/post/microDispersion.php'), true );

}
?><script>window.location.href = 'desgloseGastos.php'</script><?
//header_remove("Content-Type: application/json; charset=UTF-8");
//       header("Location: desgloseGastos.php",TRUE,301);
	   //header("Location: desgloseGastos.php");
} //ident
//*********************************************************************************

if(isset($_GET['identAut'])){
///print_r ($_GET);
$identA= $_GET['identAut'];

//Array (    [Depcusto] => 444  )

$Depdiesel= $_GET['Depdiesel'];
$Depcaseta= $_GET['Depcaseta'];

$Depalimen= $_GET['Depalimen'];
$Depexterno= $_GET['Depexterno'];
$Depmanio= $_GET['Depmanio'];
$Depcusto= $_GET['Depcusto'];
$Depestad= $_GET['Depestad'];
$Depcomis= $_GET['Depcomis'];
$Depviatico= $_GET['Depviaticos'];
$IDAUX= $_GET['IDAUX'];

$ssqlO="select idViaje from operaciones where viaje='$identA'";

$qryO = mysqli_query($enlace,$ssqlO);
  while($arrD = mysqli_fetch_assoc($qryO))
   {
    $idViaje=$arrD['idViaje'];
   }
$ssqlD="select * from desgloseGastosAut where viaje='$identA' and id ='$IDAUX' and status =''";
//echo $ssqlD; 
$qryD = mysqli_query($enlace,$ssqlD);
  while($arrD = mysqli_fetch_assoc($qryD))
   {
     $preDie+=$arrD['PREdiesel'];
	 $preCas+=$arrD['PREcasetas'];
	 $preAli+=$arrD['PREalimentos'];
	 $preTra+=$arrD['PREtransito'];
	 $preMan+=$arrD['PREmaniobras'];
	 $preEst+=$arrD['PREestadias'];
	 $preRep+=$arrD['PRErepartos'];
	 $preCom+=$arrD['PREcomision'];
	 $preVia+=$arrD['PREviaticos'];
	 $preCus+=$arrD['PREcustodia'];
	 $StaAux=$arrD['status'];
     $fecha=$arrD['fecha'];
     $oper=$arrD['operador'];
     $destin=$arrD['destino'];  
   }

   $ssqlD="select * from desgloseGastosAut where viaje='$identA' ";
$qryD = mysqli_query($enlace,$ssqlD);
  while($arrD = mysqli_fetch_assoc($qryD))
   {
     $oper=$arrD['operador'];
     $destin=$arrD['destino'];  
   }
  
//echo "UPDATE desgloseGastosAut SET idViaje='".$idViaje."' WHERE viaje ='$identA'";
mysqli_query($enlace,"UPDATE desgloseGastosAut SET idViaje='".$idViaje."' WHERE viaje ='$identA'");

//adicionales
if($Depmanio <> 0){
 mysqli_query($enlace,"UPDATE desgloseGastosAut SET maniobras=maniobras+'".$Depmanio."', status='Autorizado' WHERE viaje ='$identA'");
 $tipo='Maniobras';
 $Deposito=$Depmanio;
 $obs='';
//The JSON data.
$Datos = array(
    idViaje=>$idViaje,
    tipo     =>$tipo,
    subtotal =>'0',
    iva      =>'0',
    total    =>'0',
    idTramo  =>'0',
    observacion =>$obs,
    estatus  =>'Autorizado',
    comprobado=>'false',
    cobroCliente=>'0',
    autoriza  =>$userL,
    montoAprobado=>$Deposito
);

$url = 'http://www.misistema.mx/beluga/Finanzas/endpoints/confirmacion/QA/post/microGastos.php';
 
//Initiate cURL.
$ch = curl_init($url);
 //Encode the array into JSON.
$jsonDataEncoded = json_encode($Datos);
//Tell cURL that we want to send a POST request.
curl_setopt($ch, CURLOPT_POST, 1);
//Attach our encoded JSON string to the POST fields.
curl_setopt($ch, CURLOPT_POSTFIELDS, $jsonDataEncoded);
//Set the content type to application/json
curl_setopt($ch, CURLOPT_HTTPHEADER, array('Content-Type: application/json')); 
//Execute the request
//$result = curl_exec($ch);
curl_exec($ch); 
//$data = json_decode( file_get_contents('http://www.misistema.mx/beluga/Finanzas/endpoints/confirmacion/QA/post/microGastos.php'), true );
}

if($Depdiesel <> 0){
//echo "UPDATE desgloseGastosAut SET diesel=diesel+'".$Depdiesel."', status='Autorizado' WHERE viaje ='$identA' and id ='$IDAUX' ";
  mysqli_query($enlace,"UPDATE desgloseGastosAut SET diesel=diesel+'".$Depdiesel."', status='Autorizado' WHERE viaje ='$identA' and id ='$IDAUX'");
  $tipo='Diesel';
  $Deposito=$Depdiesel;
  $obs='';
//The JSON data.
$Datos = array(
    idViaje=>$idViaje,
    tipo     =>$tipo,
    subtotal =>'0',
    iva      =>'0',
    total    =>'0',
    idTramo  =>'0',
    observacion =>$obs,
    estatus  =>'Autorizado',
    comprobado=>'false',
    cobroCliente=>'0',
    autoriza  =>$userL,
    montoAprobado=>$Deposito
);

$url = 'http://www.misistema.mx/beluga/Finanzas/endpoints/confirmacion/QA/post/microGastos.php';
 
//Initiate cURL.
$ch = curl_init($url);
 //Encode the array into JSON.
$jsonDataEncoded = json_encode($Datos);
//Tell cURL that we want to send a POST request.
curl_setopt($ch, CURLOPT_POST, 1);
//Attach our encoded JSON string to the POST fields.
curl_setopt($ch, CURLOPT_POSTFIELDS, $jsonDataEncoded);
//Set the content type to application/json
curl_setopt($ch, CURLOPT_HTTPHEADER, array('Content-Type: application/json')); 
//Execute the request
//$result = curl_exec($ch);
curl_exec($ch); 
//$data = json_decode( file_get_contents('http://www.misistema.mx/beluga/Finanzas/endpoints/confirmacion/QA/post/microGastos.php'), true );
}
if($Depcaseta <> 0){
 mysqli_query($enlace,"UPDATE desgloseGastosAut SET casetas=casetas+'".$Depcaseta."', status='Autorizado' WHERE viaje ='$identA' and id ='$IDAUX'");
  $tipo='Casetas';
  $Deposito=$Depcaseta;
  $obs='';
//The JSON data.
$Datos = array(
    idViaje=>$idViaje,
    tipo     =>$tipo,
    subtotal =>'0',
    iva      =>'0',
    total    =>'0',
    idTramo  =>'0',
    observacion =>$obs,
    estatus  =>'Autorizado',
    comprobado=>'false',
    cobroCliente=>'0',
    autoriza  =>$userL,
    montoAprobado=>$Deposito
);

$url = 'http://www.misistema.mx/beluga/Finanzas/endpoints/confirmacion/QA/post/microGastos.php';
 
//Initiate cURL.
$ch = curl_init($url);
 //Encode the array into JSON.
$jsonDataEncoded = json_encode($Datos);
//Tell cURL that we want to send a POST request.
curl_setopt($ch, CURLOPT_POST, 1);
//Attach our encoded JSON string to the POST fields.
curl_setopt($ch, CURLOPT_POSTFIELDS, $jsonDataEncoded);
//Set the content type to application/json
curl_setopt($ch, CURLOPT_HTTPHEADER, array('Content-Type: application/json')); 
//Execute the request
//$result = curl_exec($ch);
curl_exec($ch); 
//$data = json_decode( file_get_contents('http://www.misistema.mx/beluga/Finanzas/endpoints/confirmacion/QA/post/microGastos.php'), true );
}

if($Deptransi <> 0){
  mysqli_query($enlace,"UPDATE desgloseGastosAut SET transito=transito+'".$Deptransi."', status='Autorizado' WHERE viaje ='$identA' and id ='$IDAUX'");
  $tipo='Transito';
  $Deposito=$Deptransi;
  $obs='';
//The JSON data.
$Datos = array(
    idViaje=>$idViaje,
    tipo     =>$tipo,
    subtotal =>'0',
    iva      =>'0',
    total    =>'0',
    idTramo  =>'0',
    observacion =>$obs,
    estatus  =>'Autorizado',
    comprobado=>'false',
    cobroCliente=>'0',
    autoriza  =>$userL,
    montoAprobado=>$Deposito
);

$url = 'http://www.misistema.mx/beluga/Finanzas/endpoints/confirmacion/QA/post/microGastos.php';
 
//Initiate cURL.
$ch = curl_init($url);
 //Encode the array into JSON.
$jsonDataEncoded = json_encode($Datos);
//Tell cURL that we want to send a POST request.
curl_setopt($ch, CURLOPT_POST, 1);
//Attach our encoded JSON string to the POST fields.
curl_setopt($ch, CURLOPT_POSTFIELDS, $jsonDataEncoded);
//Set the content type to application/json
curl_setopt($ch, CURLOPT_HTTPHEADER, array('Content-Type: application/json')); 
//Execute the request
//$result = curl_exec($ch);
curl_exec($ch); 
//$data = json_decode( file_get_contents('http://www.misistema.mx/beluga/Finanzas/endpoints/confirmacion/QA/post/microGastos.php'), true );
}

if($Depreparto <> 0){
 mysqli_query($enlace,"UPDATE desgloseGastosAut SET repartos=repartos+'".$Depreparto."', status='Autorizado' WHERE viaje ='$identA' and id ='$IDAUX'");
  $tipo='Reparto';
  $Deposito=$Depreparto;
  $obs='';
//The JSON data.
$Datos = array(
    idViaje=>$idViaje,
    tipo     =>$tipo,
    subtotal =>'0',
    iva      =>'0',
    total    =>'0',
    idTramo  =>'0',
    observacion =>$obs,
    estatus  =>'Autorizado',
    comprobado=>'false',
    cobroCliente=>'0',
    autoriza  =>$userL,
    montoAprobado=>$Deposito
);

$url = 'http://www.misistema.mx/beluga/Finanzas/endpoints/confirmacion/QA/post/microGastos.php';
 
//Initiate cURL.
$ch = curl_init($url);
 //Encode the array into JSON.
$jsonDataEncoded = json_encode($Datos);
//Tell cURL that we want to send a POST request.
curl_setopt($ch, CURLOPT_POST, 1);
//Attach our encoded JSON string to the POST fields.
curl_setopt($ch, CURLOPT_POSTFIELDS, $jsonDataEncoded);
//Set the content type to application/json
curl_setopt($ch, CURLOPT_HTTPHEADER, array('Content-Type: application/json')); 
//Execute the request
//$result = curl_exec($ch);
curl_exec($ch); 
//$data = json_decode( file_get_contents('http://www.misistema.mx/beluga/Finanzas/endpoints/confirmacion/QA/post/microGastos.php'), true );
}
if($Depestad <> 0){
 mysqli_query($enlace,"UPDATE desgloseGastosAut SET estadias=estadias+'".$Depestad."', status='Autorizado' WHERE viaje ='$identA' and id ='$IDAUX'");
  $tipo='Estadias';
  $Deposito=$Depestad;
  $obs='';
//The JSON data.
$Datos = array(
    idViaje=>$idViaje,
    tipo     =>$tipo,
    subtotal =>'0',
    iva      =>'0',
    total    =>'0',
    idTramo  =>'0',
    observacion =>$obs,
    estatus  =>'Autorizado',
    comprobado=>'false',
    cobroCliente=>'0',
    autoriza  =>$userL,
    montoAprobado=>$Deposito
);

$url = 'http://www.misistema.mx/beluga/Finanzas/endpoints/confirmacion/QA/post/microGastos.php';
 
//Initiate cURL.
$ch = curl_init($url);
 //Encode the array into JSON.
$jsonDataEncoded = json_encode($Datos);
//Tell cURL that we want to send a POST request.
curl_setopt($ch, CURLOPT_POST, 1);
//Attach our encoded JSON string to the POST fields.
curl_setopt($ch, CURLOPT_POSTFIELDS, $jsonDataEncoded);
//Set the content type to application/json
curl_setopt($ch, CURLOPT_HTTPHEADER, array('Content-Type: application/json')); 
//Execute the request
//$result = curl_exec($ch);
curl_exec($ch); 
//$data = json_decode( file_get_contents('http://www.misistema.mx/beluga/Finanzas/endpoints/confirmacion/QA/post/microGastos.php'), true );
}
if($Depalimen <> 0){
	if( $StaAux !='Adicional'){
  mysqli_query($enlace,"UPDATE desgloseGastosAut SET alimentos=alimentos+'".$Depalimen."', status='Autorizado' WHERE viaje ='$identA' and id ='$IDAUX'");
  $tipo='Alimentos';
  $Deposito=$Depalimen;
  $obs='';
 } else {
 mysqli_query($enlace,"UPDATE desgloseGastosAut SET viaticos=viaticos+'".$Depalimen."', status='Autorizado' WHERE viaje ='$identA' and id ='$IDAUX'");
  $tipo='Viaticos';
  $Deposito=$Depalimen;
  $obs='';	 
 }
 //The JSON data.
$Datos = array(
    idViaje=>$idViaje,
    tipo     =>$tipo,
    subtotal =>'0',
    iva      =>'0',
    total    =>'0',
    idTramo  =>'0',
    observacion =>$obs,
    estatus  =>'Autorizado',
    comprobado=>'false',
    cobroCliente=>'0',
    autoriza  =>$userL,
    montoAprobado=>$Deposito
);

$url = 'http://www.misistema.mx/beluga/Finanzas/endpoints/confirmacion/QA/post/microGastos.php';
 
//Initiate cURL.
$ch = curl_init($url);
 //Encode the array into JSON.
$jsonDataEncoded = json_encode($Datos);
//Tell cURL that we want to send a POST request.
curl_setopt($ch, CURLOPT_POST, 1);
//Attach our encoded JSON string to the POST fields.
curl_setopt($ch, CURLOPT_POSTFIELDS, $jsonDataEncoded);
//Set the content type to application/json
curl_setopt($ch, CURLOPT_HTTPHEADER, array('Content-Type: application/json')); 
//Execute the request
//$result = curl_exec($ch);
curl_exec($ch); 
//$data = json_decode( file_get_contents('http://www.misistema.mx/beluga/Finanzas/endpoints/confirmacion/QA/post/microGastos.php'), true );
}
 if($Depcomis <> 0){
//	 echo "UPDATE desgloseGastosAut SET comision='".$Depcomis."', status='Autorizado' WHERE viaje ='$identA'";
 mysqli_query($enlace,"UPDATE desgloseGastosAut SET comision=comision+'".$Depcomis."', status='Autorizado' WHERE viaje ='$identA' and id ='$IDAUX'");
$tipo='Comision';
  $Deposito=$Depcomis;
  $obs='';
//The JSON data.
$Datos = array(
    idViaje=>$idViaje,
    tipo     =>$tipo,
    subtotal =>'0',
    iva      =>'0',
    total    =>'0',
    idTramo  =>'0',
    observacion =>$obs,
    estatus  =>'Autorizado',
    comprobado=>'false',
    cobroCliente=>'0',
    autoriza  =>$userL,
    montoAprobado=>$Deposito
);

$url = 'http://www.misistema.mx/beluga/Finanzas/endpoints/confirmacion/QA/post/microGastos.php';
 
//Initiate cURL.
$ch = curl_init($url);
 //Encode the array into JSON.
$jsonDataEncoded = json_encode($Datos);
//Tell cURL that we want to send a POST request.
curl_setopt($ch, CURLOPT_POST, 1);
//Attach our encoded JSON string to the POST fields.
curl_setopt($ch, CURLOPT_POSTFIELDS, $jsonDataEncoded);
//Set the content type to application/json
curl_setopt($ch, CURLOPT_HTTPHEADER, array('Content-Type: application/json')); 
//Execute the request
$result = curl_exec($ch);
//curl_exec($ch); 
//$data = json_decode( file_get_contents('http://www.misistema.mx/beluga/Finanzas/endpoints/confirmacion/QA/post/microGastos.php'), true );
 }
 
//echo "INSERT INTO desgloseGastos(fecha,operador,destino,viaje,diesel,PREdiesel,casetas,Precasetas,alimentos,PREalimentos,PREcomision,transito,PREtransito,
//	maniobras,PREmaniobras,autoriza)
 // VALUES ('$fecha', '$oper','$destin','$identA','$Depdiesel','$preDie','$Depcaseta','$preCas','$Depalimen','$preAli','$preCom','$Deptransi','$preTra','$Depmanio','$preMan','$userL')";
 	mysqli_query($enlace,"INSERT INTO desgloseGastos(fecha,operador,destino,viaje,diesel,PREdiesel,casetas,Precasetas,alimentos,PREalimentos,PREcomision,transito,PREtransito,
	maniobras,PREmaniobras,autoriza)
  VALUES ('$fecha', '$oper','$destin','$identA','$Depdiesel','$preDie','$Depcaseta','$preCas','$Depalimen','$preAli','$preCom','$Deptransi','$preTra','$Depmanio','$preMan','$userL')");

?><script>window.location.href = 'desgloseGastosAutorizacion.php'</script><?
  
  //header("Location: desgloseGastosAutorizacion.php",TRUE,301);
}//idenAdic 

//*********************************************************************************

if(isset($_GET['tipo'])){
//	print_r($_GET);
 $tipoAut= $_GET['tipo'];
  $viajeAut= $_GET['viaje'];

if ($tipoAut=='diesel'){
	$concep ='sum(diesel) ,PRE'.$tipoAut;
	$campo='diesel';
	$campo1=' and PREdiesel > 0';
	 
}
if ($tipoAut=='casetas'){
	$concep ='sum(casetas) ,PRE'.$tipoAut;
	$campo='casetas';
	$campo1=' and PREcasetas > 0';	 
}
if ($tipoAut=='maniobras'){
	$concep ='sum(maniobras) ,PRE'.$tipoAut;
	$campo='maniobras';
	$campo1=' and PREmaniobras > 0';	 
}
if ($tipoAut=='transito'){
	$concep ='sum(transito) ,PRE'.$tipoAut;
	$campo='transito';
	$campo1=' and PREtransito > 0';	 
}
if ($tipoAut=='repartos'){
	$concep ='sum(repartos) ,PRE'.$tipoAut;
	$campo='repartos';
	$campo1=' and PRErepartos > 0';	 
}
if ($tipoAut=='estadias'){
	$concep ='sum(estadias) ,PRE'.$tipoAut;
	$campo='estadias';
	$campo1=' and PREestadias > 0';	 
}
if ($tipoAut=='viaticos'){
	$concep ='sum(viaticos) ,PRE'.$tipoAut;
	$campo='viaticos';
	$campo1=' and PREviaticos > 0';	 
}

if ($tipoAut=='comision'){
	$concep ='sum(comision) ,PRE'.$tipoAut;
	$campo='comision';
	$campo1=' and PREcomision > 0';	 
}


$ssqlD="select $concep,observacion,operador,destino from desgloseGastosAut where viaje='$viajeAut'";
//echo $ssqlD; 
$qryD = mysqli_query($enlace,$ssqlD);
  while($arrD = mysqli_fetch_array($qryD))
   {
     $acum=$arrD[0];
	 $pres=$arrD[1];
	 $obser=$arrD[2];
	 $opera=$arrD[3];
	 $destin=$arrD[4];
	
   }
   
/*   $resto =$pres-$acum;
  echo "Acum-->".$acum;
  echo "Pres-->".$pres;
  echo "OBS-->".$obser;
   
echo "INSERT INTO desgloseGastosAut(fecha,operador,destino,viaje,$campo)
  VALUES ('$fecha', '$opera','$destin','$viajeAut','$resto')";
   */ 
if ($obser=='' and $pres > 0){
 //echo "UPDATE desgloseGastosAut set status='Incompleto' WHERE viaje ='$viajeAut' $campo1";
 mysqli_query($enlace,"UPDATE desgloseGastosAut set status='Incompleto' WHERE viaje ='$viajeAut' $campo1");
 


//	mysqli_query($enlace,"INSERT INTO desgloseGastos(fecha,operador,destino,viaje,diesel,PREdiesel,casetas,Precasetas,alimentos,PREalimentos,comision,PREcomision,transito,PREtransito,
//	maniobras,PREmaniobras,autoriza)
//  VALUES ('$fecha', '$oper','$destin','$identA','$Depdiesel','$preDie','$Depcaseta','$preCas','$Depalimen','$preAli','$Depcomis','$preCom','$Deptransi','$preTra','$Depmanio',$preMan,'$userL')");
}


 header("Location: desgloseGastosAutorizacion.php");
}
//idenAdic 

//*********************************************************************************

if(isset($_GET['identAdicAut'])){
  $identA= $_GET['identAdicAut'];
   $idAux= $_GET['idAux'];
 //print_r ($_GET);
$Depdiesel= $_GET['Depdiesel'];
$Depcaseta= $_GET['Depcaseta'];
$Depviatico= $_GET['Depalimen'];
$Deptransi= $_GET['Deptransi'];
$Depmanio= $_GET['Depmanio'];
$Depreparto= $_GET['Depreparto'];
$Depestad= $_GET['Depestad'];
$DepMto= $_GET['Depmanto'];
$DepCom= $_GET['DepCom'];

$ssqlO="select idViaje from operaciones where viaje='$identA'";
$qryO = mysqli_query($enlace,$ssqlO);
  while($arrD = mysqli_fetch_assoc($qryO))
   {
    $idViaje=$arrD['idViaje'];
   }
$ssqlD="select * from desgloseGastosAut where  id ='$idAux' and status <> 'Autorizado'";
//echo $ssqlD; 
$qryD = mysqli_query($enlace,$ssqlD);
  while($arrD = mysqli_fetch_assoc($qryD))
   {
     $fecha=$arrD['fecha'];
     $oper=$arrD['operador'];
     $destin=$arrD['destino'];
	 $Prediesel=$arrD['PREdiesel'];
	 $Precasetas=$arrD['PREcasetas'];
	 $Precomision=$arrD['PREcomision'];
	 $Pretransito=$arrD['PREtransito'];
	 $Premaniobras=$arrD['PREmaniobras'];
	 $Preestadias=$arrD['PREestadias'];
	 $Prerepartos=$arrD['PRErepartos'];
	 $Previaticos=$arrD['PREviaticos'];
	 $Premantenimiento=$arrD['PREmantenimiento'];
	 $seCobra=$arrD['seCobra'];
	 $obs=$arrD['observacion'];
   }
   
//adicionales 
if($Depmanio > 0){
 mysqli_query($enlace,"UPDATE desgloseGastosAut SET maniobras='".$Depmanio."', status='Autorizado' WHERE viaje ='$identA' and id ='$idAux'");
}

if($Depdiesel > 0){
 mysqli_query($enlace,"UPDATE desgloseGastosAut SET diesel='".$Depdiesel."', status='Autorizado' WHERE viaje ='$identA' and id ='$idAux'");
}
if($Depcaseta > 0){
 mysqli_query($enlace,"UPDATE desgloseGastosAut SET casetas='".$Depcaseta."',status='Autorizado' WHERE viaje ='$identA' and id ='$idAux'");
}

if($Deptransi > 0){
 mysqli_query($enlace,"UPDATE desgloseGastosAut SET transito='".$Deptransi."', status='Autorizado' WHERE viaje ='$identA' and id ='$idAux'");
}

if($Depreparto > 0){
 mysqli_query($enlace,"UPDATE desgloseGastosAut SET repartos='".$Depreparto."', status='Autorizado' WHERE viaje ='$identA' and id ='$idAux'");
}
if($Depestad > 0){
	//echo "UPDATE desgloseGastosAut SET estadias='".$Depestad."', status='Autorizado' WHERE viaje ='$identA' and id ='$idAux'";
 mysqli_query($enlace,"UPDATE desgloseGastosAut SET estadias='".$Depestad."', status='Autorizado' WHERE viaje ='$identA' and id ='$idAux'");
}
if($Depviatico > 0){
//	echo "UPDATE desgloseGastosAut SET viaticos='".$Depviatico."', status='Autorizado' WHERE viaje ='$identA' and id ='$idAux'";
 mysqli_query($enlace,"UPDATE desgloseGastosAut SET seCobra='".$seCobra."',viaticos='".$Depviatico."', status='Autorizado' WHERE viaje ='$identA' and id ='$idAux'");
 
}
if($DepCom > 0){
 mysqli_query($enlace,"UPDATE desgloseGastosAut SET comision='".$DepCom."', status='Autorizado' WHERE viaje ='$identA' and id ='$idAux'");
}
if($DepMto > 0){
 mysqli_query($enlace,"UPDATE desgloseGastosAut SET mantenimiento='".$DepMto."', status='Autorizado' WHERE viaje ='$identA' and id ='$idAux'");
}
//echo "INSERT INTO desgloseGastos(fecha,operador,destino,viaje,diesel,casetas,comision,transito,maniobras,estadias,repartos,viaticos,PREdiesel,Precasetas,PREcomision,PREtransito,PREmaniobras,PREestadias,PRErepartos,PREviaticos,status,observacion)
// VALUES ('$fecha', '$oper','$destin','$identA','$Depdiesel','$Depcaseta','$DepCom','$Deptransi','$Depmanio','$Depestad','$Depreparto','$Depviatico',$Prediesel,$Precasetas,$Precomision,$Pretransito,$Premaniobras,$Preestadias,$Prerepartos,$Previaticos,'Adicional','$obs')";
  mysqli_query($enlace,"INSERT INTO desgloseGastos(fecha,operador,destino,viaje,diesel,casetas,comision,transito,maniobras,estadias,repartos,viaticos,mantenimiento,PREdiesel,Precasetas,PREcomision,PREtransito,PREmaniobras,PREestadias,PRErepartos,PREviaticos,PREmantenimiento,status,observacion,seCobra,autoriza)
  VALUES ('$fecha', '$oper','$destin','$identA','$Depdiesel','$Depcaseta','$DepCom','$Deptransi','$Depmanio','$Depestad','$Depreparto','$Depviatico','$DepMto',$Prediesel,$Precasetas,$Precomision,$Pretransito,$Premaniobras,$Preestadias,$Prerepartos,$Previaticos,$Premantenimiento,'Adicional','$obs','$seCobra','$userL')");

  header("Location: desgloseGastosAutorizacion.php");
}//idenAdic 
//*********************************************************************************

if(isset($_GET['identAdic'])){
  $identA= $_GET['identAdic'];
$idAux= $_GET['idAux'];
//print_r($_GET);
$diesel= $_GET['diesel'];
$caseta= $_GET['caseta'];
$alimen= $_GET['alimen'];
$transi= $_GET['transi'];
$manio= $_GET['manio'];
$reparto= $_GET['reparto'];
$estadia= $_GET['estadia'];
$mantenimieto= $_GET['mantenimiento'];


$Depdiesel= $_GET['Depdiesel'];
$Depcaseta= $_GET['Depcaseta'];
$Depalimen= $_GET['Depalimen'];
$Deptransi= $_GET['Deptransi'];
$Depmanio= $_GET['Depmanio'];
$Depreparto= $_GET['Depreparto'];
$Depestad= $_GET['Depestad'];
$DepManto= $_GET['Depmanto'];

$Foldiesel= $_GET['Foldiesel'];
$Folcaseta= $_GET['Folcaseta'];
$Folalimen= $_GET['Folalimen'];
$Foltransi= $_GET['Foltransi']; 
$Folmanio= $_GET['Folmanio']; 
$Folreparto= $_GET['Folreparto']; 
$Folestad= $_GET['Folestad'];
$Folmanto= $_GET['FolMto']; 
 
$ssqlO="select idViaje from operaciones where viaje='$identA'";
$qryO = mysqli_query($enlace,$ssqlO);
  while($arrD = mysqli_fetch_assoc($qryO))
   {
    $idViaje=$arrD['idViaje'];
   }

$ssqlD="select * from desgloseGastos where id ='$idAux' and status ='Adicional'";
//echo $ssqlD; 
$qryD = mysqli_query($enlace,$ssqlD);
  while($arrD = mysqli_fetch_assoc($qryD))
   {
     $preDie+=$arrD['PREdiesel'];
	 $preCas+=$arrD['PREcasetas'];
	 $preAli+=$arrD['PREviaticos'];
	 $preTra+=$arrD['PREtransito'];
	 $preMan+=$arrD['PREmaniobras'];
	 $preEst+=$arrD['PREestadias'];
	 $preRep+=$arrD['PRErepartos'];
	 $preMto+=$arrD['PREmantenimiento'];
	 $seCobra=$arrD['seCobra'];
	 
/*	$Depdiesel= $arrD['diesel'];
	$Depcaseta= $arrD['casetas'];
	$Depalimen= $arrD['viaticos'];
	$Deptransi= $arrD['transito'];
	$Depmanio= $arrD['maniobras'];
	$Depreparto= $arrD['repartos'];
	$Depestad= $arrD['estadias'];*/
	 
   }
//echo "se cobra-->".$seCobra;
//adicionales
if($Depmanio > 0){
//echo "UPDATE desgloseGastos SET maniobras='".$Depmanio."',pagoMan='".$manio."',folManiobras='".$Folmanio."' , status='Realizado',formaPago='ADICIONAL' WHERE viaje ='$identA' and id ='$idAux'";
 mysqli_query($enlace,"UPDATE desgloseGastos SET maniobras='".$Depmanio."',pagoMan='".$manio."',folManiobras='".$Folmanio."' , status='Realizado',formaPago='ADICIONAL' WHERE viaje ='$identA' and id ='$idAux'");
if($preMan > 0){$aux =",gManiobras=gManiobras+".$preMan;} else {$aux ="";}
//echo "UPDATE operaciones set maniobras  +='".$Depmanio."' $aux   WHERE viaje ='$identA'";
   mysqli_query($enlace,"UPDATE operaciones set maniobras =maniobras  +'".$Depmanio."' $aux   WHERE viaje ='$identA'");
}

if($Depdiesel > 0){
 mysqli_query($enlace,"UPDATE desgloseGastos SET diesel='".$Depdiesel."',pagoDie='".$diesel."',folDiesel='".$Foldiesel."' , status='Realizado',formaPago='ADICIONAL' WHERE viaje ='$identA' and id ='$idAux'");
if($preDie > 0 and $seCobra =='Si'){$aux =",precio=precio+".$preMan;} else {$aux ="";}
 mysqli_query($enlace,"UPDATE operaciones set diesel  =diesel+'".$Depdiesel."'    WHERE viaje ='$identA'");
}
if($Depcaseta > 0){
 mysqli_query($enlace,"UPDATE desgloseGastos SET casetas='".$Depcaseta."',  pagoCas='".$caseta."',folCasetas='".$Folcaseta."' , status='Realizado',formaPago='ADICIONAL' WHERE viaje ='$identA' and id ='$idAux'");
if($preCas > 0 and $seCobra =='Si'){$aux =",precio=precio+".$preCas;} else {$aux ="";}
 mysqli_query($enlace,"UPDATE operaciones set casetas  =casetas+'".$Depcaseta."'    WHERE viaje ='$identA'");
}

if($Deptransi > 0){
//echo "UPDATE desgloseGastos SET transito='".$Deptransi."',pagoTra='".$transi."',folTransito='".$Foltransi."' , status='Realizado',formaPago='ADICIONAL' WHERE viaje ='$identA' and id ='$idAux'";
 mysqli_query($enlace,"UPDATE desgloseGastos SET transito='".$Deptransi."',pagoTra='".$transi."',folTransito='".$Foltransi."' , status='Realizado',formaPago='ADICIONAL' WHERE viaje ='$identA' and id ='$idAux'");
if($preTra > 0 and $seCobra =='Si'){$aux =",precio=precio+".$preTra;} else {$aux ="";}
//echo "UPDATE operaciones set transito  +='".$Deptransi."' $aux   WHERE viaje ='$identA'";
  mysqli_query($enlace,"UPDATE operaciones set transito  =transito+'".$Deptransi."'    WHERE viaje ='$identA'");
}

if($Depreparto > 0){
//	echo "UPDATE desgloseGastos SET repartos='".$Depreparto."',pagoRep='".$reparto."',FolRep='".$Folreparto."' , status='Realizado',formaPago='ADICIONAL' WHERE viaje ='$identA' and id ='$idAux'";
 mysqli_query($enlace,"UPDATE desgloseGastos SET repartos='".$Depreparto."',pagoRep='".$reparto."',FolRep='".$Folreparto."' , status='Realizado',formaPago='ADICIONAL' WHERE viaje ='$identA' and id ='$idAux'");
if($preRep > 0 and $seCobra =='Si'){$aux =",desviacion=desviacion+".$preRep;} else {$aux ="";}
//echo "UPDATE operaciones set efectivo  +='".$Depreparto."' $aux  WHERE viaje ='$identA'";
  mysqli_query($enlace,"UPDATE operaciones set efectivo  =efectivo+'".$Depreparto."' $aux  WHERE viaje ='$identA'");
}
if($Depestad > 0){
// echo "UPDATE desgloseGastos SET estadias='".$Depestad."',pagoEst='".$estadia."',FolEstadias='".$Folestad."' , status='Realizado',formaPago='ADICIONAL' WHERE viaje ='$identA' and id ='$idAux'";
 mysqli_query($enlace,"UPDATE desgloseGastos SET estadias='".$Depestad."',pagoEst='".$estadia."',FolEstadias='".$Folestad."' , status='Realizado',formaPago='ADICIONAL' WHERE viaje ='$identA' and id ='$idAux'");
if($preEst > 0 and $seCobra =='Si'){$aux =",gEstadias=gEstadias+".$preEst;} else {$aux ="";}  
// echo "UPDATE operaciones set estadia  =estadia+'".$Depestad."' $aux WHERE viaje ='$identA'";
 mysqli_query($enlace,"UPDATE operaciones set estadia  =estadia+'".$Depestad."' $aux WHERE viaje ='$identA'");

}
if($Depalimen > 0){
//echo "UPDATE desgloseGastos SET viaticos='".$Depalimen."',pagoVia='".$alimen."',folviaticos='".$Folalimen."' , status='Realizado',formaPago='ADICIONAL' WHERE viaje ='$identA' and id ='$idAux'";	
 mysqli_query($enlace,"UPDATE desgloseGastos SET viaticos='".$Depalimen."',pagoVia='".$alimen."',folVia='".$Folalimen."' , status='Realizado',formaPago='ADICIONAL' WHERE viaje ='$identA' and id ='$idAux'");
 // echo "PREALI-->".$preAli;
  if($preAli > 0 and $seCobra =='Si'){$aux =",Adicionales=Adicionales+".$preAli;} else {$aux ="";}
//  echo "UPDATE operaciones set viaticos  =+'".$Depalimen."' $aux WHERE viaje ='$identA'";
 mysqli_query($enlace,"UPDATE operaciones set viaticos  =viaticos+'".$Depalimen."' $aux  WHERE viaje ='$identA'");

}
if($DepManto > 0){
	$mantenimiento ='Manto';
//echo "UPDATE desgloseGastos SET Mantenimiento='".$DepManto."',pagoMto='".$mantenimieto."',folMante='".$Folmanto."' , status='Realizado',formaPago='ADICIONAL' WHERE viaje ='$identA' and id ='$idAux'";
 mysqli_query($enlace,"UPDATE desgloseGastos SET Mantenimiento='".$DepManto."',pagoMto='".$mantenimieto."',folMante='".$Folmanto."' , status='Realizado',formaPago='ADICIONAL' WHERE viaje ='$identA' and id ='$idAux'");
 // echo "PREALI-->".$preAli;
  //if($preAli > 0 and $seCobra =='Si'){$aux =",Adicionales=Adicionales+".$preAli;} else {$aux ="";}
//  echo "UPDATE operaciones set viaticos  =+'".$Depalimen."' $aux WHERE viaje ='$identA'";
 mysqli_query($enlace,"UPDATE operaciones set mantenimiento  =mantenimiento+'".$DepManto."' $aux  WHERE viaje ='$identA'");

}
   header("Location: desgloseGastos.php");
}//idenAdic 
//**********************************************************************************
if(isset($_GET['viaje'])){
 $viaje= $_GET['viaje'];
 $importe= $_GET['importe'];
 $concep= $_GET['concep'];
 $forma= $_GET['forma'];
 $obs= $_GET['obsev'];
 $cajas= $_GET['cajas'];
 $embarque= $_GET['embar'];
 $correo= $_GET['correo'];
 $correoB= $_GET['correoB'];
 $cobra= $_GET['cobra'];
 $destinatario= $_GET['destina'];
 
// mysqli_connect("db574183143.db.1and1.com", "dbo574183143", "b3lug42015") or die("NO se pudo realizar la conexion");
// mysqli_select_db("db574183143");	
  if($viaje < '2000'){
    $tabla='operacionesM';
  } else{
    $tabla='operaciones';
  }
     $ssqlC="select fecha,destino,operador,cliente from $tabla where viaje='$viaje'";
	 //echo $ssqlC;
     $qryC = mysqli_query($enlace,$ssqlC);
     while($arrC = mysqli_fetch_assoc($qryC))
      {
       $fecUso=$arrC['fecha'];
	   $dest=$arrC['destino'];
	   $Oper=$arrC['operador'];
	   $clien=$arrC['cliente'];
	   $idViaje=$arrC['idViaje'];
      }
  
  $fecUso=$fecha;
   
 if($concep =='alimentos'){
	$subcat=$concep;
	if ($forma =='Edenred'){
	  $insAli="insert into edenredViaticos (fecha,beneficiario,operacion,viaje,alimentos,subcateg)
         values('$fecUso','$Oper','GOperacion','$viaje','$importe','viaticos')";
	//echo $insAli;
  	     mysqli_query($enlace,$insAli);	
  // echo "update $tabla set alimentosE=alimentosE+'$importe' where viaje ='$viaje'";
	  $consA=mysqli_query($enlace,"update $tabla set alimentosE=alimentosE+'$importe',explica ='$obs' where viaje ='$viaje'"); 
	  
	  } //edenred
	  if ($forma =='Efectivo'){
	     $insAli="insert into cajaEfectivo (fecha,beneficiario,operacion,viaje,alimentos,subcateg)
         values('$fecUso','$Oper','GOperacion','$viaje','$importe','viaticos')";
  	     mysqli_query($enlace,$insAli);	
		 $consA=mysqli_query($enlace,"update $tabla set alimentosF=alimentosF+'$importe',explica ='$obs' where viaje ='$viaje'"); 
	  }
     if ($forma =='caja'){
	     $insAli="insert into cajaViaticos (fecha,beneficiario,operacion,viaje,alimentos,subcateg)
         values('$fecUso','$Oper','GOperacion','$viaje','$importe','viaticos)";
	//	 echo $insAli;
  	     mysqli_query($enlace,$insAli);	
	 $consA=mysqli_query($enlace,"update $tabla set alimentos=alimentos+'$importe',explica ='$obs' where viaje ='$viaje'");
	  }
	   $queryInsertar="insert into desgloseGastos (fecha,operador,destino,viaje,alimentos,pagoAli,status,observacion,formaPago,idViaje)
	   value ('$fecUso','$Oper','$dest','$viaje','$importe','$forma','Realizado','$obs','Adicional','$idViaje')";
   // echo $queryInsertar;
	 mysqli_query($enlace,$queryInsertar);

       //$consA=mysqli_query($enlace,"update desgloseGastos set alimentos=alimentos+'$importe' where viaje ='$viaje'");
 }  //alimentos
 //*********************************
 if($concep =='casetas'){
	$subcat=$concep;
	if ($forma =='Edenred'){
	  $insCas="insert into edenredViaticos (fecha,beneficiario,operacion,viaje,casetas,subcateg)
         values('$fecUso','$Oper','GOperacion','$viaje','$importe','viaticos')";
  	     mysqli_query($enlace,$insCas);	
	  } //edenred
	  if ($forma =='Efectivo'){
	     $insCas="insert into cajaEfectivo (fecha,beneficiario,operacion,viaje,casetas,subcateg)
         values('$fecUso','$Oper','GOperacion','$viaje','$importe','viaticos')";
  	     mysqli_query($enlace,$insCas);	
	 }
     if ($forma =='caja'){
	     $insCas="insert into cajaViaticos (fecha,beneficiario,operacion,viaje,casetas,subcateg)
         values('$fecUso','$Oper','GOperacion','$viaje','$importe','viaticos')";
		  
  	     mysqli_query($enlace,$insCas);	
	  }
	  $consC=mysqli_query($enlace,"update $tabla set casetas=casetas+'$importe',explica ='$obs' where viaje ='$viaje'"); 
	  
	   $queryInsertar="insert into desgloseGastos (fecha,operador,destino,viaje,casetas,pagoCas,status,observacion,formaPago,idViaje)
	   value ('$fecUso','$Oper','$dest','$viaje','$importe','$forma','Realizado','$obs','Adicional','$idViaje')"; 
     echo $queryInsertar;
	mysqli_query($enlace,$queryInsertar);
	//$consA=mysqli_query($enlace,"update desgloseGastos set casetas=casetas+'$importe' where viaje ='$viaje'");
 }  //casetas
 //*************************************
 if($concep =='estadias'){
	$subcat=$concep;
	if ($forma =='Edenred'){
	  $insEst="insert into edenredViaticos (fecha,beneficiario,operacion,viaje,estadias,subcateg)
         values('$fecUso','$Oper','GOperacion','$viaje','$importe','viaticos')";
	     mysqli_query($enlace,$insEst);	
		 
	/*	 	if ($cobra =='SI'){
		$cob =",gEstadias ='$importe'";		
		} else {
		$cob='';	
		}*/
		 $cob='';
	  $consE=mysqli_query($enlace,"update $tabla set estadiaE=estadiaE+'$importe',explica ='$obs' $cob where viaje ='$viaje'"); 
	  } //edenred
	  if ($forma =='Efectivo'){
	     $insEst="insert into cajaEfectivo (fecha,beneficiario,operacion,viaje,estadias,subcateg)
         values('$fecUso','$Oper','GOperacion','$viaje','$importe','viaticos')";
  	     mysqli_query($enlace,$insEst);	
		

		/* 	if ($cobra =='SI'){
		$cob =",gEstadias ='$importe'";		
		} else {
		$cob='';	
		}*/
		$cob='';
		$consE=mysqli_query($enlace,"update $tabla set estadiaF=estadiaF+'$importe',explica ='$obs'  where viaje ='$viaje'"); 
	  }
     if ($forma =='caja'){
	     $insEst="insert into cajaViaticos (fecha,beneficiario,operacion,viaje,estadias,subcateg)
         values('$fecUso','$Oper','GOperacion','$viaje','$importe','viaticos')";
  	     mysqli_query($enlace,$insEst);	
		 
		 
		/* 	if ($cobra =='SI'){
		$cob =",gEstadias ='$importe'";		
		} else {
		$cob='';	
		}*/
		$cob='';
		 $consE=mysqli_query($enlace,"update $tabla set estadia=estadia+'$importe' ,explica ='$obs' $cob where viaje ='$viaje'"); 
	  }
	   	
	  
	   $queryInsertar="insert into desgloseGastos (fecha,operador,destino,viaje,estadias,pagoEst,status,observacion,formaPago,idViaje)
	   value ('$fecUso','$Oper','$dest','$viaje','$importe','$forma','Realizado','$obs','Adicional','$idViaje')"; 
 // echo $queryInsertar;
	mysqli_query($enlace,$queryInsertar);

//$consA=mysqli_query($enlace,"update desgloseGastos set estadias=estadias+'$importe' where viaje ='$viaje'");
//******************************************************
$cobra='NO';
if ($cobra =='SI'){
	//+++++++++++++++++++++++++++++++++++++++++++++++++++++
include_once('../Classes/class.phpmailer.php');

$body = '<b>Buen d&iacute;a.<br /><br /> Solicito de su apoyo con la autorizaci&oacute;n de  la siguiente Estad&iacute;a.</b><br /><br />';

//---------------------------------------------------------------
 //Cuerpo del Mensaje
$body .='<table border="1" style="width:1250px;"  colspan="5" cellpadding="2" cellspacing="2">';
$body.='<tr>';
$body.=' <td width="10%"><div align="center"><strong>Viaje</strong></td>';
$body.=' <td width="15%"><div align="center"><strong>Cliente</strong></td>';
$body.=' <td width="20%"><div align="center"><strong>Operador</strong></td>';
$body.=' <td width="20%"><div align="center"><strong>Destino</strong></div></td>';
$body.=' <td width="20%"><div align="center"><strong>Destinatario</strong></div></td>';
$body.=' <td width="10%"><div align="center"><strong>Importe</strong></div></td>';
$body.=' <td width="8%"><div align="center"><strong>Num. Embarque</strong></div></td>';
$body.=' <td width="8%"><div align="center"><strong>Cajas</strong></div></td>';
$body.='</tr>';

//  echo "Viaje===>".$rowProd['viaje'];
  $body.='<tr>';
  
 //echo "SELECT nombre FROM empleados where nombre_ref='$Oper'";
  $resE = mysqli_query($enlace,"SELECT nombre FROM empleados where nombre_ref='$Oper'"); 
        while($rowE=mysqli_fetch_assoc($resE)){
         $nombreE=$rowE['nombre']; 
        }
  
  $body.=' <td align="center">'.$viaje.'</td>';
  $body.=' <td align="center">'.$clien.'</td>';
  $body.=' <td>'.$nombreE.'</td>';
  $body.=' <td align="center">'.$dest.'</td>';
  $body.=' <td align="center">'.$destinatario.'</td>';
  $body.=' <td align="right">'.number_format($importe,2).'</td>';
  $body.=' <td align="right">'.$embarque.'</td>';
  $body.=' <td align="right">'.$cajas.'</td>';
  
  $body.='</tr>';

$body.='</table>';
  $body.='</br></br></br>Atentamente.';
  $body.='</br></br></br>Beluga de M&eacute;xico S.A. de C.V.';
  $body.='</br>53585110';
//  echo $body; 


//----------------------------------------------------
$mail = new PHPMailer();
$mail->Host = "smtp.1and1.com";
$mail->From = "operacion@belugamexico.com.mx";
$mail->Mailer="smtp";
$mail->FromName = "Beluga Logistics";
$mail->SMTPAuth   = true;                // enable SMTP authentication
$mail->Username   = "operacion@belugamexico.com.mx";  // GMAIL username
$mail->Password   = "Beluga123.";
$mail->Subject = "Autorizacion Estadias";
$mail->IsHTML(true);

$mail->AddAddress("francisco@belugamexico.com.mx");
$mail->AddAddress("david@belugamexico.com.mx");
if($correo !=''){
$mail->AddAddress($correo);}
if($correoB !=''){
$mail->AddAddress($correoB);}
//$mail->AddAddress("leon@belugamexico.com.mx");
$mail->AddAddress("cesar@belugamexico.com.mx");
$mail->AddAddress("jesus@belugamexico.com.mx");
$mail->AddAddress("alexis@belugamexico.com.mx");
$mail->AddAddress("nelly@belugamexico.com.mx");
$mail->AddAddress("karen@belugamexico.com.mx");
$mail->AddAddress("omar@belugamexico.com.mx");
//$mail->AddAddress($email");
 $mail->Body=$body;
$mail->Send();
}

 }  //estadias
 //*********************************
  if($concep =='maniobras'){
	$subcat=$concep;
	if ($forma =='Edenred'){
	  $insMan="insert into edenredViaticos (fecha,beneficiario,operacion,viaje,maniobras,subcateg)
         values('$fecUso','$Oper','GOperacion','$viaje','$importe','viaticos')";
//  	  echo $insMan;
	     mysqli_query($enlace,$insMan);	

	/*	 	if ($cobra =='SI'){
		$cob =",gEstadias ='$importe'";		
		} else {
		$cob='';	
		}	*/	

		$cob='';	
	  $consM=mysqli_query($enlace,"update $tabla set maniobrasE=maniobrasE+'$importe',explica ='$obs'  where viaje ='$viaje'"); 
	  } //edenred
	  if ($forma =='Efectivo'){
	     $insMan="insert into cajaEfectivo (fecha,beneficiario,operacion,viaje,maniobras,subcateg)
         values('$fecUso','$Oper','GOperacion','$viaje','$importe','viaticos')";
  	     mysqli_query($enlace,$insMan);	
	
        
		/* 	if ($cobra =='SI'){
		$cob =",gEstadias ='$importe'";		
		} else {
		$cob='';	
		} */
		$cob='';	
	$consM=mysqli_query($enlace,"update $tabla set maniobrasF=maniobrasF+'$importe',explica ='$obs' $cob where viaje ='$viaje'"); 
	  }
     if ($forma =='caja'){
	     $insMan="insert into cajaViaticos (fecha,beneficiario,operacion,viaje,maniobras,subcateg)
         values('$fecUso','$Oper','GOperacion','$viaje','$importe','viaticos')";
  	     mysqli_query($enlace,$insMan);	
		
		/* 	if ($cobra =='SI'){
		$cob =",gEstadias ='$importe'";		
		} else {
		$cob='';	
		}*/
		$cob='';	
		$consM=mysqli_query($enlace,"update $tabla set maniobras=maniobras+'$importe' ,explica ='$obs' $cob where viaje ='$viaje'"); 
	  }
	   $queryInsertar="insert into desgloseGastos (fecha,operador,destino,viaje,maniobras,pagoMan,status,observacion,formaPago,idViaje)
	   value ('$fecUso','$Oper','$dest','$viaje','$importe','$forma','Realizado','$obs','Adicional','$idViaje')"; 
	   
        //   $consA=mysqli_query($enlace,"update desgloseGastos set maniobras=maniobras+'$importe' where viaje ='$viaje'");
  //   echo $queryInsertar;
	mysqli_query($enlace,$queryInsertar);
	
//*********************************************************************************************	
$cobra ='NO';
	if ($cobra =='SI'){
		//+++++++++++++++++++++++++++++++++++++++++++++++++++++
include_once('../Classes/class.phpmailer.php');

$body = '<b><b>Buen d&iacute;a.<br /><br /> Solicito de su apoyo con la autorizaci&oacute;n de la Maniobra.</b><br /><br />';

//---------------------------------------------------------------
 //Cuerpo del Mensaje
$body .='<table border="1" style="width:1250px;"  colspan="5" cellpadding="2" cellspacing="2">';
$body.='<tr>';
$body.=' <td width="10%"><div align="center"><strong>Viaje</strong></td>';
$body.=' <td width="15%"><div align="center"><strong>Cliente</strong></td>';
$body.=' <td width="20%"><div align="center"><strong>Operador</strong></td>';
$body.=' <td width="30%"><div align="center"><strong>Destino</strong></div></td>';
$body.=' <td width="20%"><div align="center"><strong>Destinatario</strong></div></td>';
$body.=' <td width="10%"><div align="center"><strong>Importe</strong></div></td>';
$body.=' <td width="10%"><div align="center"><strong>Num. Embarque</strong></div></td>';
$body.=' <td width="10%"><div align="center"><strong>Cajas</strong></div></td>';
$body.='</tr>';

//  echo "Viaje===>".$rowProd['viaje'];
  $body.='<tr>';
  
 //echo "SELECT nombre FROM empleados where nombre_ref='$Oper'";
  $resE = mysqli_query($enlace,"SELECT nombre FROM empleados where nombre_ref='$Oper'"); 
        while($rowE=mysqli_fetch_assoc($resE)){
         $nombreE=$rowE['nombre']; 
        }
  
  $body.=' <td align="center">'.$viaje.'</td>';
  $body.=' <td align="center">'.$clien.'</td>';
  $body.=' <td>'.$nombreE.'</td>';
  $body.=' <td align="center">'.$dest.'</td>';
    $body.=' <td align="center">'.$destinatario.'</td>';
  $body.=' <td align="right">'.number_format($importe,2).'</td>';
  $body.=' <td align="right">'.$embarque.'</td>';
  $body.=' <td align="right">'.$cajas.'</td>';
  
  $body.='</tr>';

$body.='</table>';
$body.='</br></br></br>Saludos.';
  $body.='</br></br></br>Beluga de M&eacute;xico S.A. de C.V.';
  $body.='</br>53585110';//echo $body; 

//echo $body; 


//----------------------------------------------------
$mail = new PHPMailer();
$mail->Host = "smtp.1and1.com";
$mail->From = "operacion@belugamexico.com.mx";
$mail->Mailer="smtp";
$mail->FromName = "Beluga Logistics";
$mail->SMTPAuth   = true;                // enable SMTP authentication
$mail->Username   = "operacion@belugamexico.com.mx";  // GMAIL username
$mail->Password   = "Beluga123.";
$mail->Subject = "Autorizacion Maniobras";
$mail->IsHTML(true);

$mail->AddAddress("francisco@belugamexico.com.mx");
$mail->AddAddress("david@belugamexico.com.mx");
if($correo !=''){
$mail->AddAddress($correo);}
if($correoB !=''){
$mail->AddAddress($correoB);}
//$mail->AddAddress("leon@belugamexico.com.mx");
$mail->AddAddress("cesar@belugamexico.com.mx");
$mail->AddAddress("jesus@belugamexico.com.mx");
$mail->AddAddress("alexis@belugamexico.com.mx");
$mail->AddAddress("nelly@belugamexico.com.mx");
$mail->AddAddress("karen@belugamexico.com.mx");
$mail->AddAddress("omar@belugamexico.com.mx");
//$mail->AddAddress($email");
 $mail->Body=$body;
$mail->Send();

	}


//*******************************************************
 }  //maniobras
 //*********************************
   if($concep =='transito'){
	$subcat=$concep;
	
	/*	if ($cobra =='SI'){
		$cob =",Adicionales ='$importe'";		
		} else {
		$cob='';	
		}*/
	$cob='';
	if ($forma =='Edenred'){
	 
	  $consT=mysqli_query($enlace,"update $tabla set transitoE=transitoE+'$importe',explica ='$obs' $cob where viaje ='$viaje'"); 
	  } //edenred
	  if ($forma =='Efectivo'){
		  echo "update $tabla set transitoF=transitoF+'$importe',explica ='$obs' $cob where viaje ='$viaje'";
		 $consT=mysqli_query($enlace,"update $tabla set transitoF=transitoF+'$importe',explica ='$obs' $cob where viaje ='$viaje'"); 
	  }
     if ($forma =='caja'){
	     $insTra="insert into cajaViaticos (fecha,beneficiario,operacion,viaje,transito,subcateg)
         values('$fecUso','$Oper','GOperacion','$viaje','$importe','viaticos')";
  	     mysqli_query($enlace,$insTra);	
		 $consT=mysqli_query($enlace,"update $tabla set transito=transito+'$importe' ,explica ='$obs' $cob where viaje ='$viaje'"); 
	  }
	   $queryInsertar="insert into desgloseGastos (fecha,operador,destino,viaje,transito,pagoTra,status,observacion,formaPago,idViaje)
	   value ('$fecUso','$Oper','$dest','$viaje','$importe','$forma','Realizado','$obs','Adicional','$idViaje')"; 
	   
	 //  $consA=mysqli_query($enlace,"update desgloseGastos set transito=transito+'$importe' where viaje ='$viaje'");
    // echo $queryInsertar;
	mysqli_query($enlace,$queryInsertar);
	
	//*********************************************************************************************	
$cobra ='NO';
	if ($cobra =='SI'){
		//+++++++++++++++++++++++++++++++++++++++++++++++++++++
include_once('../Classes/class.phpmailer.php');

$body = '<b><b>Buen d&iacute;a.<br /><br /> Solicito de su apoyo con la autorizaci&oacute;n de la Maniobra.</b><br /><br />';

//---------------------------------------------------------------
 //Cuerpo del Mensaje
$body .='<table border="1" style="width:1250px;"  colspan="5" cellpadding="2" cellspacing="2">';
$body.='<tr>';
$body.=' <td width="10%"><div align="center"><strong>Viaje</strong></td>';
$body.=' <td width="15%"><div align="center"><strong>Cliente</strong></td>';
$body.=' <td width="20%"><div align="center"><strong>Operador</strong></td>';
$body.=' <td width="30%"><div align="center"><strong>Destino</strong></div></td>';
$body.=' <td width="20%"><div align="center"><strong>Destinatario</strong></div></td>';
$body.=' <td width="10%"><div align="center"><strong>Importe</strong></div></td>';
$body.=' <td width="10%"><div align="center"><strong>Num. Embarque</strong></div></td>';
$body.=' <td width="10%"><div align="center"><strong>Cajas</strong></div></td>';
$body.='</tr>';

//  echo "Viaje===>".$rowProd['viaje'];
  $body.='<tr>';
  
 //echo "SELECT nombre FROM empleados where nombre_ref='$Oper'";
  $resE = mysqli_query($enlace,"SELECT nombre FROM empleados where nombre_ref='$Oper'"); 
        while($rowE=mysqli_fetch_assoc($resE)){
         $nombreE=$rowE['nombre']; 
        }
  
  $body.=' <td align="center">'.$viaje.'</td>';
  $body.=' <td align="center">'.$clien.'</td>';
  $body.=' <td>'.$nombreE.'</td>';
  $body.=' <td align="center">'.$dest.'</td>';
    $body.=' <td align="center">'.$destinatario.'</td>';
  $body.=' <td align="right">'.number_format($importe,2).'</td>';
  $body.=' <td align="right">'.$embarque.'</td>';
  $body.=' <td align="right">'.$cajas.'</td>';
  
  $body.='</tr>';

$body.='</table>';
$body.='</br></br></br>Saludos.';
  $body.='</br></br></br>Beluga de M&eacute;xico S.A. de C.V.';
  $body.='</br>53585110';//echo $body; 

//echo $body; 


//----------------------------------------------------
$mail = new PHPMailer();
$mail->Host = "smtp.1and1.com";
$mail->From = "operacion@belugamexico.com.mx";
$mail->Mailer="smtp";
$mail->FromName = "Beluga Logistics";
$mail->SMTPAuth   = true;                // enable SMTP authentication
$mail->Username   = "operacion@belugamexico.com.mx";  // GMAIL username
$mail->Password   = "Beluga123.";
$mail->Subject = "Autorizacion Transito";
$mail->IsHTML(true);

$mail->AddAddress("francisco@belugamexico.com.mx");
$mail->AddAddress("david@belugamexico.com.mx");
if($correo !=''){
$mail->AddAddress($correo);}
if($correoB !=''){
$mail->AddAddress($correoB);}
//$mail->AddAddress("leon@belugamexico.com.mx");
$mail->AddAddress("cesar@belugamexico.com.mx");
$mail->AddAddress("jesus@belugamexico.com.mx");
$mail->AddAddress("alexis@belugamexico.com.mx");
$mail->AddAddress("nelly@belugamexico.com.mx");
$mail->AddAddress("karen@belugamexico.com.mx");
$mail->AddAddress("omar@belugamexico.com.mx");
//$mail->AddAddress($email");
 $mail->Body=$body;
$mail->Send();

	}
	
 }  //transi
 //*********************************
// echo $concep;
   if($concep =='mantenimiento'){
	$subcat=$concep;
	if ($forma =='Edenred'){
	  $insTra="insert into edenredViaticos (fecha,beneficiario,operacion,viaje,transito,subcateg)
         values('$fecUso','$Oper','GOperacion','$viaje','$importe','viaticos')";
  	     mysqli_query($enlace,$insTra);

	  $consT=mysqli_query($enlace,"update $tabla set transitoE=transitoE+'$importe',explica ='$obs' where viaje ='$viaje'");
	  } //edenred
	  if ($forma =='Efectivo'){
	     $insTra="insert into cajaEfectivo (fecha,beneficiario,operacion,viaje,transito,subcateg)
         values('$fecUso','$Oper','GOperacion','$viaje','$importe','viaticos')";
  	     mysqli_query($enlace,$insTra);
		 $consT=mysqli_query($enlace,"update $tabla set transitoF=transitoF+'$importe',explica ='$obs' where viaje ='$viaje'");
	  }
     if ($forma =='caja'){
	     $insTra="insert into cajaViaticos (fecha,beneficiario,operacion,viaje,transito,subcateg)
         values('$fecUso','$Oper','GOperacion','$viaje','$importe','viaticos')";
  	     mysqli_query($enlace,$insTra);
		 $consT=mysqli_query($enlace,"update $tabla set transito=transito+'$importe' ,explica ='$obs' where viaje ='$viaje'");
	  }
	   $queryInsertar="insert into desgloseGastos (fecha,operador,destino,viaje,mantenimiento,pagoMto,status,observacion)
	   value ('$fecUso','$Oper','$dest','$viaje','$importe','$forma','Realizado','$obs')";

	  // $consA=mysqli_query($enlace,"update desgloseGastos set mante=transito+'$importe' where viaje ='$viaje'");
    // echo $queryInsertar;
	mysqli_query($enlace,$queryInsertar);
 }  //diesel
 //*********************************
 if($concep =='diesel'){
	$subcat=$concep;
	if ($forma =='Edenred'){
	  $insDie="insert into edenredViaticos (fecha,beneficiario,operacion,viaje,diesel,subcateg)
         values('$fecUso','$Oper','GOperacion','$viaje','$importe','viaticos')";
  	     mysqli_query($enlace,$insDie);	
	  } //edenred
	  if ($forma =='Efectivo'){
	     $insDie="insert into cajaEfectivo (fecha,beneficiario,operacion,viaje,diesel,subcateg)
         values('$fecUso','$Oper','GOperacion','$viaje','$importe','viaticos')";
  	     mysqli_query($enlace,$insDie);	
	 }
     if ($forma =='caja'){
	     $insDie="insert into cajaViaticos (fecha,beneficiario,operacion,viaje,diesel,subcateg)
         values('$fecUso','$Oper','GOperacion','$viaje','$importe','viaticos')";
  	     mysqli_query($enlace,$insDie);	
	  }
	  $consC=mysqli_query($enlace,"update $tabla set diesel=diesel+'$importe',explica ='$obs' where viaje ='$viaje'"); 
	  
	   $queryInsertar="insert into desgloseGastos (fecha,operador,destino,viaje,diesel,pagoDie,status,observacion,formaPago,idViaje)
	   value ('$fecUso','$Oper','$dest','$viaje','$importe','$forma','Realizado','$obs','Adicional','$idViaje')"; 
     //echo $queryInsertar;
	mysqli_query($enlace,$queryInsertar); 

 $resM = mysqli_query($enlace,"SELECT max(orden)as nO FROM operacionDiesel");
      while($rowO = mysqli_fetch_assoc($resM)){
         $nO = $rowO['nO']+1;
      } 
	  $litros=importe/'18.96';
      $insDie="insert into operacionDiesel (fecha,operador,viaje,retiro,litros,orden,fechaUso)
      values('$fecUso','$Oper','$viaje','$importe','$litros','$nO','$fecUso')";
//	echo $insDie;
	  mysqli_query($enlace,$insDie);		
	
 }  //diesel

/*
    if($concep=='diesel'){
		echo "update $tabla set diesel=diesel+'$importe' where  viaje ='$viaje'";
      $consulta=mysqli_query($enlace,"update $tabla set diesel=diesel+'$importe' where  viaje ='$viaje'");

      $resM = mysqli_query($enlace,"SELECT max(orden)as nO FROM operacionDiesel");
      while($rowO = mysqli_fetch_assoc($resM)){
         $nO = $rowO['nO']+1;
      } 
	  $litros=importe/'18.96';
      $insDie="insert into operacionDiesel (fecha,operador,viaje,retiro,litros,orden,fechaUso)
      values('$fecUso','$Oper','$viaje','$importe','$litros','$nO','$fecUso')";
//	echo $insDie;
	  mysqli_query($enlace,$insDie);		
     $queryInsertar="insert into desgloseGastos (fecha,operador,destino,viaje,diesel,pagoDie,status)
	 value   ('$fecUso','$Oper','$dest','$viaje','$importe','$forma','Realizado')";   
     echo $queryInsertar;
	mysqli_query($enlace,$queryInsertar); 	 
    } //diesel

	 if($concep=='casetas'){
      $consulta=mysqli_query($enlace,"update $tabla set casetas=casetas+'$importe' where  viaje ='$viaje'");

     $queryInsertar="insert into desgloseGastos (fecha,operador,destino,viaje,casetas,pagoCas,status)
	 value   ('$fecUso','$Oper','$dest','$viaje','$importe','$forma','Realizado')";   
     //echo $queryInsertar;
	mysqli_query($enlace,$queryInsertar); 	 
    } //Casetas

	*/
	//+++++++++++++++++++++++++++++++++++++++++++++++++++++
include_once('../Classes/class.phpmailer.php');

$body = '<b>Gasto Adicional</b><br /><br />';

//---------------------------------------------------------------
 //Cuerpo del Mensaje
$body .='<table border="1" style="width:1450px;"  colspan="5" cellpadding="2" cellspacing="2">';
$body.='<tr>';
$body.=' <td width="30%"><div align="center"><strong>Viaje</strong></td>';
$body.=' <td width="30%"><div align="center"><strong>Cliente</strong></td>';
$body.=' <td width="30%"><div align="center"><strong>Operador</strong></td>';
$body.=' <td width="30%"><div align="center"><strong>Destino</strong></div></td>';
$body.=' <td width="20%"><div align="center"><strong>Destinatario</strong></div></td>';
$body.=' <td width="30%"><div align="center"><strong>Concepto</strong></div></td>';
$body.=' <td width="30%"><div align="center"><strong>Importe</strong></div></td>';
$body.=' <td width="20%"><div align="center"><strong>Modo Pago</strong></div></td>';
$body.=' <td width="70%"><div align="center"><strong>Observ.</strong></div></td>';
$body.='</tr>';

//  echo "Viaje===>".$rowProd['viaje'];
  $body.='<tr>';
  
 //echo "SELECT nombre FROM empleados where nombre_ref='$Oper'";
  $resE = mysqli_query($enlace,"SELECT nombre FROM empleados where nombre_ref='$Oper'"); 
        while($rowE=mysqli_fetch_assoc($resE)){
         $nombreE=$rowE['nombre']; 
        }
  
  $body.=' <td align="center">'.$viaje.'</td>';
  $body.=' <td align="center">'.$clien.'</td>';
  $body.=' <td>'.$nombreE.'</td>';
  $body.=' <td align="center">'.$dest.'</td>';
  $body.=' <td align="right">'.$concep.'</td>';
  $body.=' <td align="right">'.number_format($importe,2).'</td>';
  $body.=' <td align="right">'.$forma.'</td>';
  $body.=' <td align="right">'.$obs.'</td>';
  
  $body.='</tr>';

$body.='</table>';

$body.='</br></br></br>Saludos.';
  $body.='</br></br></br>Beluga de M&eacute;xico S.A. de C.V.';
  $body.='</br>53585110';//echo $body; 

//----------------------------------------------------
$mail = new PHPMailer();
$mail->Host = "smtp.1and1.com";
$mail->From = "operacion@belugamexico.com.mx";
$mail->Mailer="smtp";
$mail->FromName = "Beluga Logistics";
$mail->SMTPAuth   = true;                // enable SMTP authentication
$mail->Username   = "operacion@belugamexico.com.mx";  // GMAIL username
$mail->Password   = "Beluga123.";
$mail->Subject = "Gasto Adicional";
$mail->IsHTML(true);

$mail->AddAddress("francisco@belugamexico.com.mx");
$mail->AddAddress("david@belugamexico.com.mx");
//$mail->AddAddress("jesus@belugamexico.com.mx");
//$mail->AddAddress("leon@belugamexico.com.mx");
//este$mail->AddAddress("lourdes@belugamexico.com.mx");
//este$mail->AddAddress("operaciones@belugamexico.com.mx");
//$mail->AddAddress($email");
 $mail->Body=$body;
$mail->Send();
	
 //st header("Location: histDesglose.php");
}

//*****************nuevo************************************

if(isset($_GET['viajeNew'])){
 $viaje= $_GET['viajeNew'];
 $importe= $_GET['importe'];
 $refer= $_GET['refer'];
 $rubro= $_GET['rubro'];
 $forma= $_GET['forma'];
 $cobra= $_GET['cobra'];
 $cajas= $_GET['cajas'];
 $embarque= $_GET['embar'];
 $correo= $_GET['correo'];
  $correoB= $_GET['correoB'];
 $destinatario= $_GET['destina'];
// print_r($_GET);
//echo $rubro;
 
    $tabla='operaciones'; 

 mysqli_connect("db574183143.db.1and1.com", "dbo574183143", "b3lug42015") or die("NO se pudo realizar la conexion");
 mysqli_select_db("db574183143");	
  
 
 $ssqlC="select fecha,destino,operador,cliente from operaciones where viaje='$viaje'";
 //echo $ssqlC;
 $qryC = mysqli_query($enlace,$ssqlC);
  while($arrC = mysqli_fetch_assoc($qryC))
      {
       $fecUso=$arrC['fecha'];
	   $dest=$arrC['destino'];
	   $Oper=$arrC['operador'];
	   $clien=$arrC['cliente'];
      }
  
  $ssqlD="select sum(diesel) as diesel,sum(PREdiesel) as PREdiesel ,sum(casetas) as casetas ,sum(PREcasetas) as PREcasetas ,sum(alimentos) as alimentos,sum(PREalimentos) as PREalimentos,
  sum(comision) as comision,sum(PREcomision) as PREcomision ,sum(estadias) as estadias,sum(PREestadias) as PREestadias ,sum(maniobras) as maniobras,sum(PREmaniobras) as PREmaniobras ,
 sum(transito) as transito,sum(PREtransito) as PREtransito ,sum(mantenimiento) as mantenimiento,sum(PREmantenimiento) as PREmantenimiento   from desgloseGastos  where viaje='$viaje'";
 //echo $ssqlD;
 $qryD = mysqli_query($enlace,$ssqlD);
  while($arrC = mysqli_fetch_assoc($qryD))
      {
       $PreDies=$arrC['PREdiesel'];
	   $DepDies=$arrC['diesel'];
	   $PreCase=$arrC['PREcasetas'];
	   $DepCase=$arrC['casetas'];
	   $PreAlim=$arrC['PREalimentos'];
	   $DepAlim=$arrC['alimentos'];
	   $PreComi=$arrC['PREcomision'];
	   $DepComi=$arrC['comision'];
	   $PreEsta=$arrC['PREestadias'];
	   $DepEsta=$arrC['estadias'];
	   $PreMani=$arrC['PREmaniobras'];
	   $DepMani=$arrC['maniobras'];
	   $PreTran=$arrC['PREtransito'];
	   $DepTran=$arrC['transito'];
	   $PreMant=$arrC['PREmantenimiento'];
	   $DepMant=$arrC['mantenimiento'];
      }

  
  $fecUso=$fecha;
   
 //***********************************  
   if($rubro =='comision'){
	$subcat=$rubro;

	 $consA=mysqli_query($enlace,"update operaciones set compensacion='$importe' where viaje ='$viaje'");

//	   if( $DepComi > $PreComi){
		   $pgo='Compensa';
//	   } else  {
//		   $pgo='';}
	   $queryInsertar="insert into desgloseGastos (fecha,operador,destino,viaje,compensacion,status,formaPago,idViaje)
	   value ('$fecUso','$Oper','$dest','$viaje','$importe','Realizado','$pgo','$idViaje')";
 //   echo $queryInsertar;
	 mysqli_query($enlace,$queryInsertar);
	
	
       //$consA=mysqli_query($enlace,"update desgloseGastos set alimentos=alimentos+'$importe' where viaje ='$viaje'");
 }  //alimentos
   
 //***********************  
 if($rubro =='alimentos'){
	$subcat=$rubro;
	if ($forma =='Edenred'){
	  $insAli="insert into edenredViaticos (fecha,beneficiario,operacion,viaje,alimentos,subcateg)
         values('$fecUso','$Oper','GOperacion','$viaje','$importe','viaticos')";
	//echo $insAli;
  	     mysqli_query($enlace,$insAli);	
  // echo "update $tabla set alimentosE=alimentosE+'$importe' where viaje ='$viaje'";
	  $consA=mysqli_query($enlace,"update operaciones set alimentosE=alimentosE+'$importe' where viaje ='$viaje'"); 
	  
	  } //edenred
	  if ($forma =='Efectivo'){
	     $insAli="insert into cajaEfectivo (fecha,beneficiario,operacion,viaje,alimentos,subcateg)
         values('$fecUso','$Oper','GOperacion','$viaje','$importe','viaticos')";
  	     mysqli_query($enlace,$insAli);	
		 $consA=mysqli_query($enlace,"update operaciones set alimentosF=alimentosF+'$importe' where viaje ='$viaje'"); 
	  }
     if ($forma =='caja' || $forma =='ID' ){
	     $insAli="insert into cajaViaticos (fecha,beneficiario,operacion,viaje,alimentos,subcateg)
         values('$fecUso','$Oper','GOperacion','$viaje','$importe','viaticos)";
	//	 echo $insAli;
  	     mysqli_query($enlace,$insAli);	
	
	  }
	  
	  
	   if( $DepAlim > $PreAlim){
		   $pgo='Adicional';
	   } else  {
		   $pgo='';}
	   $queryInsertar="insert into desgloseGastos (fecha,operador,destino,viaje,alimentos,pagoAli,status,formaPago,folAlimentos,idViaje)
	   value ('$fecUso','$Oper','$dest','$viaje','$importe','$forma','Realizado','$pgo','$refer','$idViaje')";
 //   echo $queryInsertar;
	 mysqli_query($enlace,$queryInsertar);
	
	
       //$consA=mysqli_query($enlace,"update desgloseGastos set alimentos=alimentos+'$importe' where viaje ='$viaje'");
 }  //alimentos
  //***********************  
 if($rubro =='viaticos'){
	$subcat=$rubro;

  // echo "update $tabla set alimentosE=alimentosE+'$importe' where viaje ='$viaje'";
	  $consA=mysqli_query($enlace,"update operaciones set viaticos=viaticos+'$importe' where viaje ='$viaje'"); 
	   $pgo='Adicional';

	   $queryInsertar="insert into desgloseGastos (fecha,operador,destino,viaje,viaticos,pagoVia,status,formaPago,folVia,idViaje)
	   value ('$fecUso','$Oper','$dest','$viaje','$importe','$forma','Realizado','$pgo','$refer','$idViaje')";
 //   echo $queryInsertar;
	 mysqli_query($enlace,$queryInsertar);
 }  //Viaticos

 
 //*********************************
 if($rubro =='casetas'){
	$subcat=$rubro;
	if ($forma =='Edenred'){
	  $insCas="insert into edenredViaticos (fecha,beneficiario,operacion,viaje,casetas,subcateg)
         values('$fecUso','$Oper','GOperacion','$viaje','$importe','viaticos')";
  	     mysqli_query($enlace,$insCas);	
	  } //edenred
	  if ($forma =='Efectivo'){
	     $insCas="insert into cajaEfectivo (fecha,beneficiario,operacion,viaje,casetas,subcateg)
         values('$fecUso','$Oper','GOperacion','$viaje','$importe','viaticos')";
  	     mysqli_query($enlace,$insCas);	
	 }
     if ($forma =='caja'){
	     $insCas="insert into cajaViaticos (fecha,beneficiario,operacion,viaje,casetas,subcateg)
         values('$fecUso','$Oper','GOperacion','$viaje','$importe','viaticos')";
		  
  	     mysqli_query($enlace,$insCas);	
	  }
	  $consC=mysqli_query($enlace,"update $tabla set casetas=casetas+'$importe',explica ='$obs' where viaje ='$viaje'"); 
	  if( $DepCase > $PreCase){
		   $pgo='Adicional';
	   } else  {
		   $pgo='';}
	   $queryInsertar="insert into desgloseGastos (fecha,operador,destino,viaje,casetas,pagoCas,status,formaPago,folCasetas,idViaje)
	   value ('$fecUso','$Oper','$dest','$viaje','$importe','$forma','Realizado','$pgo','$refer','$idViaje')"; 
 //    echo $queryInsertar;
	    	 
	mysqli_query($enlace,$queryInsertar);
	//$consA=mysqli_query($enlace,"update desgloseGastos set casetas=casetas+'$importe' where viaje ='$viaje'");
 }  //casetas
 //*************************************
 if($rubro =='estadias'){
	$subcat=$rubro;
	$cob='';	
	
	if ($forma =='Edenred'){
	  $insEst="insert into edenredViaticos (fecha,beneficiario,operacion,viaje,estadias,subcateg)
         values('$fecUso','$Oper','GOperacion','$viaje','$importe','viaticos')";
	     mysqli_query($enlace,$insEst);	
	 	/*if ($cobra =='SI'){
		$cob =",gEstadias ='$importe'";		
		} else {
		$cob='';	
		}		*/	
	
	  $consE=mysqli_query($enlace,"update $tabla set estadiaE=estadiaE+'$importe' where viaje ='$viaje'"); 	
		
    } 
	  
	  if ($forma =='Efectivo'){
	     $insEst="insert into cajaEfectivo (fecha,beneficiario,operacion,viaje,estadias,subcateg)
         values('$fecUso','$Oper','GOperacion','$viaje','$importe','viaticos')";
  	     mysqli_query($enlace,$insEst);	
		/*  	if ($cobra =='SI'){
		$cob =",gEstadias ='$importe'";		
		} else {
		$cob='';	
		}*/

		 $consE=mysqli_query($enlace,"update $tabla set estadiaF=estadiaF+'$importe' where viaje ='$viaje'"); 
				
	  }
     if ($forma =='caja'){
	     $insEst="insert into cajaViaticos (fecha,beneficiario,operacion,viaje,estadias,subcateg)
         values('$fecUso','$Oper','GOperacion','$viaje','$importe','viaticos')";
  	     mysqli_query($enlace,$insEst);	
		/*if ($cobra =='SI'){
		$cob =",gEstadias ='$importe'";		
		} else {
		$cob='';	
		}*/

		 $consE=mysqli_query($enlace,"update $tabla set estadias=estadias+'$importe'  where viaje ='$viaje'"); 
					
	  }
	  	  if( $DepEsta > $PreEsta){
		   $pgo='Adicional';
	   } else  {
		   $pgo='';}

	   $queryInsertar="insert into desgloseGastos (fecha,operador,destino,viaje,estadias,pagoEst,status,observacion,formaPago,folEstadias,idViaje)
	   value ('$fecUso','$Oper','$dest','$viaje','$importe','$forma','Realizado','$obs','$pgo','$refer','$idViaje')"; 
		 
 // echo $queryInsertar;
	mysqli_query($enlace,$queryInsertar);

//$consA=mysqli_query($enlace,"update desgloseGastos set estadias=estadias+'$importe' where viaje ='$viaje'");
//****************************************************************************************************
$cobra='NO';
if ($cobra =='SI'){
	//+++++++++++++++++++++++++++++++++++++++++++++++++++++
include_once('../Classes/class.phpmailer.php');

$body = '<b><b>Buen d&iacute;a.<br /><br /> Solicito de su apoyo con la autorizaci&oacute;n de  la siguiente Estad&iacute;a.</b><br /><br />';

//---------------------------------------------------------------
 //Cuerpo del Mensaje
$body .='<table border="1" style="width:1250px;"  colspan="5" cellpadding="2" cellspacing="2">';
$body.='<tr>';
$body.=' <td width="10%"><div align="center"><strong>Viaje</strong></td>';
$body.=' <td width="15%"><div align="center"><strong>Cliente</strong></td>';
$body.=' <td width="20%"><div align="center"><strong>Operador</strong></td>';
$body.=' <td width="30%"><div align="center"><strong>Destino</strong></div></td>';
$body.=' <td width="20%"><div align="center"><strong>Destinatario</strong></div></td>';
$body.=' <td width="10%"><div align="center"><strong>Importe</strong></div></td>';
$body.=' <td width="10%"><div align="center"><strong>Num. Embarque</strong></div></td>';
$body.=' <td width="10%"><div align="center"><strong>Cajas</strong></div></td>';
$body.='</tr>';

//  echo "Viaje===>".$rowProd['viaje'];
  $body.='<tr>';
  
 //echo "SELECT nombre FROM empleados where nombre_ref='$Oper'";
  $resE = mysqli_query($enlace,"SELECT nombre FROM empleados where nombre_ref='$Oper'"); 
        while($rowE=mysqli_fetch_assoc($resE)){
         $nombreE=$rowE['nombre']; 
        }
  
  $body.=' <td align="center">'.$viaje.'</td>';
  $body.=' <td align="center">'.$clien.'</td>';
  $body.=' <td>'.$nombreE.'</td>';
  $body.=' <td align="center">'.$dest.'</td>';
  $body.=' <td align="center">'.$destinatario.'</td>';
  $body.=' <td align="right">'.number_format($importe,2).'</td>';
  $body.=' <td align="right">'.$embarque.'</td>';
  $body.=' <td align="right">'.$cajas.'</td>';
  
  $body.='</tr>';
$body.='<tr><td colspan="2">Saludos</td></tr>';
  $body.='<tr><td colspan="3">Beluga de M&eacute;xico S.A. de C.V</td></tr>';
  $body.='<tr><td>53585110</td></tr>';//echo $body; 
$body.='</table>';
//echo $body; 


//----------------------------------------------------
$mail = new PHPMailer();
$mail->Host = "smtp.1and1.com";
$mail->From = "operacion@belugamexico.com.mx";
$mail->Mailer="smtp";
$mail->FromName = "Beluga Logistics";
$mail->SMTPAuth   = true;                // enable SMTP authentication
$mail->Username   = "operacion@belugamexico.com.mx";  // GMAIL username
$mail->Password   = "Beluga123.";
$mail->Subject = "Autorizacion Estadias";
$mail->IsHTML(true);

$mail->AddAddress("francisco@belugamexico.com.mx");
$mail->AddAddress("david@belugamexico.com.mx");
if($correo !=''){
$mail->AddAddress($correo);}
if($correoB !=''){
$mail->AddAddress($correoB);}
//$mail->AddAddress("leon@belugamexico.com.mx");
$mail->AddAddress("cesar@belugamexico.com.mx");
$mail->AddAddress("jesus@belugamexico.com.mx");
$mail->AddAddress("alexis@belugamexico.com.mx");
$mail->AddAddress("nelly@belugamexico.com.mx");
$mail->AddAddress("karen@belugamexico.com.mx");
$mail->AddAddress("omar@belugamexico.com.mx");
//$mail->AddAddress($email");

//echo $body;
 $mail->Body=$body;
//$mail->Send();
}



//*******************************************************
 }  //estadias
 //*********************************
  if($rubro =='maniobras'){
	$subcat=$rubro;
	if ($forma =='Edenred'){
	  $insMan="insert into edenredViaticos (fecha,beneficiario,operacion,viaje,maniobras,subcateg)
         values('$fecUso','$Oper','GOperacion','$viaje','$importe','viaticos')";
//  	  echo $insMan;
	     mysqli_query($enlace,$insMan);	
	/*	  	if ($cobra =='SI'){
		$cob =",gManiobras ='$importe'";		
		} else {
		$cob='';	
		}*/
		$cob='';
	  $consM=mysqli_query($enlace,"update $tabla set maniobrasE=maniobrasE+'$importe' where viaje ='$viaje'"); 
			
	  } //edenred
	  if ($forma =='Efectivo'){
	     $insMan="insert into cajaEfectivo (fecha,beneficiario,operacion,viaje,maniobras,subcateg)
         values('$fecUso','$Oper','GOperacion','$viaje','$importe','viaticos')";
  	     mysqli_query($enlace,$insMan);
		/*  	if ($cobra =='SI'){
		$cob =",gManiobras ='$importe'";		
		} else {
		$cob='';	
		}	 */
		$cob='';
		 $consM=mysqli_query($enlace,"update $tabla set maniobrasF=maniobrasF+'$importe' where viaje ='$viaje'"); 
	
	  }
     if ($forma =='caja' || $forma =='ID'){
	     $insMan="insert into cajaViaticos (fecha,beneficiario,operacion,viaje,maniobras,subcateg)
         values('$fecUso','$Oper','GOperacion','$viaje','$importe','viaticos')";
  	     mysqli_query($enlace,$insMan);	
	/*	if ($cobra =='SI'){
		$cob =",gManiobras ='$importe'";		
		} else {
		$cob='';	
		}*/
		$cob='';
		// $consM=mysqli_query($enlace,"update $tabla set maniobras=maniobras+'$importe' ,explica ='$obs'  $cob where viaje ='$viaje'"); 
		$consM=mysqli_query($enlace,"update $tabla set maniobras=maniobras+'$importe'  where viaje ='$viaje'"); 	
	  }
	  
	   if( $DepMani > $PreMani){
		   $pgo='Adicional';
	   } else  {
		   $pgo='';}
	   $queryInsertar="insert into desgloseGastos (fecha,operador,destino,viaje,maniobras,pagoMan,status,observacion,formaPago,folManiobras,idViaje)
	   value ('$fecUso','$Oper','$dest','$viaje','$importe','$forma','Realizado','$obs','$pgo','$refer','$idViaje')"; 
		   
	   
        //   $consA=mysqli_query($enlace,"update desgloseGastos set maniobras=maniobras+'$importe' where viaje ='$viaje'");
  //   echo $queryInsertar;
	mysqli_query($enlace,$queryInsertar);
	$cobra='';
	if ($cobra =='SI'){
		//+++++++++++++++++++++++++++++++++++++++++++++++++++++
include_once('../Classes/class.phpmailer.php');

$body = '<b><b>Buen d&iacute;a.<br /><br /> Solicito de su apoyo con la autorizaci&oacute;n de  la siguiente Maniobra.</b><br /><br />';

//---------------------------------------------------------------
 //Cuerpo del Mensaje
$body .='<table border="1" style="width:1250px;"  colspan="5" cellpadding="2" cellspacing="2">';
$body.='<tr>';
$body.=' <td width="10%"><div align="center"><strong>Viaje</strong></td>';
$body.=' <td width="15%"><div align="center"><strong>Cliente</strong></td>';
$body.=' <td width="20%"><div align="center"><strong>Operador</strong></td>';
$body.=' <td width="30%"><div align="center"><strong>Destino</strong></div></td>';
$body.=' <td width="30%"><div align="center"><strong>Destinatario</strong></div></td>';
$body.=' <td width="10%"><div align="center"><strong>Importe</strong></div></td>';
$body.=' <td width="10%"><div align="center"><strong>Num. Embarque</strong></div></td>';
$body.=' <td width="10%"><div align="center"><strong>Cajas</strong></div></td>';
$body.='</tr>';

//  echo "Viaje===>".$rowProd['viaje'];
  $body.='<tr>';
  
 //echo "SELECT nombre FROM empleados where nombre_ref='$Oper'";
  $resE = mysqli_query($enlace,"SELECT nombre FROM empleados where nombre_ref='$Oper'"); 
        while($rowE=mysqli_fetch_assoc($resE)){
         $nombreE=$rowE['nombre']; 
        }
  
  $body.=' <td align="center">'.$viaje.'</td>';
  $body.=' <td align="center">'.$clien.'</td>';
  $body.=' <td>'.$nombreE.'</td>';
  $body.=' <td align="center">'.$dest.'</td>';
    $body.=' <td align="center">'.$destinatario.'</td>';
  $body.=' <td align="right">'.number_format($importe,2).'</td>';
  $body.=' <td align="right">'.$embarque.'</td>';
  $body.=' <td align="right">'.$cajas.'</td>';
  
  $body.='</tr>';
$body.='<tr><td colspan="2">Saludos</td></tr>';
  $body.='<tr><td colspan="3">Beluga de M&eacute;xico S.A. de C.V</td></tr>';
  $body.='<tr><td>53585110</td></tr>';//echo $body; 
$body.='</table>';
//echo $body; 



//----------------------------------------------------
$mail = new PHPMailer();
$mail->Host = "smtp.1and1.com";
$mail->From = "operacion@belugamexico.com.mx";
$mail->Mailer="smtp";
$mail->FromName = "Beluga Logistics";
$mail->SMTPAuth   = true;                // enable SMTP authentication
$mail->Username   = "operacion@belugamexico.com.mx";  // GMAIL username
$mail->Password   = "Beluga123.";
$mail->Subject = "Autorizacion Maniobras";
$mail->IsHTML(true);

$mail->AddAddress("francisco@belugamexico.com.mx");
$mail->AddAddress("david@belugamexico.com.mx");
if($correo !=''){
$mail->AddAddress($correo);}
if($correoB !=''){
$mail->AddAddress($correoB);}
//$mail->AddAddress("leon@belugamexico.com.mx");
$mail->AddAddress("cesar@belugamexico.com.mx");
$mail->AddAddress("jesus@belugamexico.com.mx");
$mail->AddAddress("alexis@belugamexico.com.mx");
$mail->AddAddress("nelly@belugamexico.com.mx");
$mail->AddAddress("karen@belugamexico.com.mx");
$mail->AddAddress("omar@belugamexico.com.mx");
//$mail->AddAddress($email");

//echo $body;
 $mail->Body=$body;
$mail->Send();

	}


//*******************************************************
	
	
	
 }  //maniobras
 //*********************************
   if($rubro =='transito'){
	$subcat=$rubro;
		/*	  	if ($cobra =='SI'){
		$cob =",Adicionales ='$importe'";		
		} else {
		$cob='';	
		}*/
		$cob='';
		
	if ($forma =='Edenred'){
	  $insTra="insert into edenredViaticos (fecha,beneficiario,operacion,viaje,transito,subcateg)
         values('$fecUso','$Oper','GOperacion','$viaje','$importe','viaticos')";
  	     mysqli_query($enlace,$insTra);	
		 
	  $consT=mysqli_query($enlace,"update $tabla set transitoE=transitoE+'$importe',explica ='$obs' $cob where viaje ='$viaje'"); 
	  } //edenred
	  if ($forma =='Efectivo'){
	     $insTra="insert into cajaEfectivo (fecha,beneficiario,operacion,viaje,transito,subcateg)
         values('$fecUso','$Oper','GOperacion','$viaje','$importe','viaticos')";
  	     mysqli_query($enlace,$insTra);	
		 $consT=mysqli_query($enlace,"update $tabla set transitoF=transitoF+'$importe',explica ='$obs'  $cob where viaje ='$viaje'"); 
	  }
     if ($forma =='caja'){
	     $insTra="insert into cajaViaticos (fecha,beneficiario,operacion,viaje,transito,subcateg)
         values('$fecUso','$Oper','GOperacion','$viaje','$importe','viaticos')";
  	     mysqli_query($enlace,$insTra);	
		 $consT=mysqli_query($enlace,"update $tabla set transito=transito+'$importe' ,explica ='$obs' $cob where viaje ='$viaje'"); 
	  }
	  
	     if( $DepTran > $PreTran){
		   $pgo='Adicional';
	   } else  {
		   $pgo='';}
	   $queryInsertar="insert into desgloseGastos (fecha,operador,destino,viaje,transito,pagoTra,status,observacion,formaPago,folTransito)
	   value ('$fecUso','$Oper','$dest','$viaje','$importe','$forma','Realizado','$obs','$pgo','$refer')"; 
	    //     $DepDies=$arrC['DepoDies'];
	   
	 //  $consA=mysqli_query($enlace,"update desgloseGastos set transito=transito+'$importe' where viaje ='$viaje'");
    // echo $queryInsertar;
	mysqli_query($enlace,$queryInsertar);
 }  //manteni
 //*********************************
// echo $concep;
   if($rubro =='mantenimiento'){
	$subcat=$rubro;
	if ($forma =='Edenred'){
	  $insTra="insert into edenredViaticos (fecha,beneficiario,operacion,viaje,transito,subcateg)
         values('$fecUso','$Oper','GOperacion','$viaje','$importe','viaticos')";
  	     mysqli_query($enlace,$insTra);

	  $consT=mysqli_query($enlace,"update $tabla set transitoE=transitoE+'$importe',explica ='$obs' where viaje ='$viaje'");
	  } //edenred
	  if ($forma =='Efectivo'){
	     $insTra="insert into cajaEfectivo (fecha,beneficiario,operacion,viaje,transito,subcateg)
         values('$fecUso','$Oper','GOperacion','$viaje','$importe','viaticos')";
  	     mysqli_query($enlace,$insTra);
		 $consT=mysqli_query($enlace,"update $tabla set transitoF=transitoF+'$importe',explica ='$obs' where viaje ='$viaje'");
	  }
     if ($forma =='caja'){
	     $insTra="insert into cajaViaticos (fecha,beneficiario,operacion,viaje,transito,subcateg)
         values('$fecUso','$Oper','GOperacion','$viaje','$importe','viaticos')";
  	     mysqli_query($enlace,$insTra);
		 $consT=mysqli_query($enlace,"update $tabla set efectivo=efectivo+'$importe' ,explica ='$obs' where viaje ='$viaje'");
	  }
	    if( $DepMnto > $PreMant){
		   $pgo='Adicional';
	   } else  {
		   $pgo='';}
		  
	   $queryInsertar="insert into desgloseGastos (fecha,operador,destino,viaje,mantenimiento,pagoMto,status,observacion,formaPago,folMante)
	   value ('$fecUso','$Oper','$dest','$viaje','$importe','$forma','Realizado','$obs','$pgo','$refer')";


	  // $consA=mysqli_query($enlace,"update desgloseGastos set mante=transito+'$importe' where viaje ='$viaje'");
    // echo $queryInsertar;
	mysqli_query($enlace,$queryInsertar);
 }  //
 //***diesel******************************
 if($rubro =='diesel'){
	$subcat=$rubro;
	if ($forma =='Edenred'){
	  $insDie="insert into edenredViaticos (fecha,beneficiario,operacion,viaje,diesel,subcateg)
         values('$fecUso','$Oper','GOperacion','$viaje','$importe','viaticos')";
  	     mysqli_query($enlace,$insDie);	
	  } //edenred
	  if ($forma =='Efectivo'){
	     $insDie="insert into cajaEfectivo (fecha,beneficiario,operacion,viaje,diesel,subcateg)
         values('$fecUso','$Oper','GOperacion','$viaje','$importe','viaticos')";
  	     mysqli_query($enlace,$insDie);	
	 }
     if ($forma =='caja'){
	     $insDie="insert into cajaViaticos (fecha,beneficiario,operacion,viaje,diesel,subcateg)
         values('$fecUso','$Oper','GOperacion','$viaje','$importe','viaticos')";
  	     mysqli_query($enlace,$insDie);	
	  }
	//  echo "update $tabla set diesel=diesel+'$importe',explica ='$obs' where viaje ='$viaje'";
	  $consC=mysqli_query($enlace,"update $tabla set diesel=diesel+'$importe',explica ='$obs' where viaje ='$viaje'"); 
	// echo  "Depostidato-->".$DepDies."Presup-->".$PreDies;
	  	    if( $DepDies > $PreDies){
		   $pgo='Adicional';
	   } else  {
		   $pgo='';}

	   $queryInsertar="insert into desgloseGastos (fecha,operador,destino,viaje,diesel,pagoDie,status,observacion,formaPago,folDiesel)
	   value ('$fecUso','$Oper','$dest','$viaje','$importe','$forma','Realizado','$obs','$pgo','$refer')"; 
   //  echo $queryInsertar;
	mysqli_query($enlace,$queryInsertar); 

 $resM = mysqli_query($enlace,"SELECT max(orden)as nO FROM operacionDiesel");
      while($rowO = mysqli_fetch_assoc($resM)){
         $nO = $rowO['nO']+1;
      } 
	  $litros=importe/'18.96';
      $insDie="insert into operacionDiesel (fecha,operador,viaje,retiro,litros,orden,fechaUso)
      values('$fecUso','$Oper','$viaje','$importe','$litros','$nO','$fecUso')";
//	echo $insDie;
	  mysqli_query($enlace,$insDie);		
	
 }  //diesel

	//+++++++++++++++++++++++++++++++++++++++++++++++++++++
include_once('../Classes/class.phpmailer.php');

$body = '<b>Gasto Adicional</b><br /><br />';

//---------------------------------------------------------------
 //Cuerpo del Mensaje
$body .='<table border="1" style="width:1450px;"  colspan="5" cellpadding="2" cellspacing="2">';
$body.='<tr>';
$body.=' <td width="30%"><div align="center"><strong>Viaje</strong></td>';
$body.=' <td width="30%"><div align="center"><strong>Cliente</strong></td>';
$body.=' <td width="30%"><div align="center"><strong>Operador</strong></td>';
$body.=' <td width="30%"><div align="center"><strong>Destino</strong></div></td>';
$body.=' <td width="30%"><div align="center"><strong>Concepto</strong></div></td>';
$body.=' <td width="30%"><div align="center"><strong>Importe</strong></div></td>';
$body.=' <td width="20%"><div align="center"><strong>Modo Pago</strong></div></td>';
$body.=' <td width="70%"><div align="center"><strong>Observ.</strong></div></td>';
$body.='</tr>';

//  echo "Viaje===>".$rowProd['viaje'];
  $body.='<tr>';
  
 //echo "SELECT nombre FROM empleados where nombre_ref='$Oper'";
  $resE = mysqli_query($enlace,"SELECT nombre FROM empleados where nombre_ref='$Oper'"); 
        while($rowE=mysqli_fetch_assoc($resE)){
         $nombreE=$rowE['nombre']; 
        }
  
  $body.=' <td align="center">'.$viaje.'</td>';
  $body.=' <td align="center">'.$clien.'</td>';
  $body.=' <td>'.$nombreE.'</td>';
  $body.=' <td align="center">'.$dest.'</td>';
  $body.=' <td align="right">'.$concep.'</td>';
  $body.=' <td align="right">'.number_format($importe,2).'</td>';
  $body.=' <td align="right">'.$forma.'</td>';
  $body.=' <td align="right">'.$obs.'</td>';
  
  $body.='</tr>';

$body.='</table>';



//----------------------------------------------------
$mail = new PHPMailer();
$mail->Host = "smtp.1and1.com";
$mail->From = "operacion@belugamexico.com.mx";
$mail->Mailer="smtp";
$mail->FromName = "Beluga Logistics";
$mail->SMTPAuth   = true;                // enable SMTP authentication
$mail->Username   = "operacion@belugamexico.com.mx";  // GMAIL username
$mail->Password   = "Beluga123.";
$mail->Subject = "Gasto Adicional";
$mail->IsHTML(true);

$mail->AddAddress("francisco@belugamexico.com.mx");
$mail->AddAddress("david@belugamexico.com.mx");
//$mail->AddAddress("jesus@belugamexico.com.mx");
//$mail->AddAddress("leon@belugamexico.com.mx");
//este$mail->AddAddress("lourdes@belugamexico.com.mx");
//este$mail->AddAddress("operaciones@belugamexico.com.mx");
//$mail->AddAddress($email");
 $mail->Body=$body;
//$mail->Send();
	
// header("Location: histDesglose.php");
}
 
//*****************nuevo************************************

if(isset($_GET['RegId'])){
 $redid= $_GET['RegId'];
 $refer= $_GET['refer'];
// print_r($_GET);
//echo $rubro;
 
 mysqli_connect("db574183143.db.1and1.com", "dbo574183143", "b3lug42015") or die("NO se pudo realizar la conexion");
 mysqli_select_db("db574183143");	
  
   $consA=mysqli_query($enlace,"update desgloseGastos set folDiesel ='$refer' where id ='$redid'");
 	  
 	
 // header("Location: histDesglose.php");
}

//*****************************************************

if(isset($_GET['comision'])){
 mysqli_connect("db574183143.db.1and1.com", "dbo574183143", "b3lug42015") or die("NO se pudo realizar la conexion");
 mysqli_select_db("db574183143");	

$impEden =$_GET['Edenred']; 
//=>$impEden= 4150 ;
$impEfec =$_GET['Efectivo'];
// => $impEfec =5000;
$impGto  =$_GET['[Gasto']; 
//=>$impGto  = 2000 ;

$fec1=date("Y-m-d");
$nuevafecha = strtotime ( '-3 day' , strtotime ( $fec1 ) ) ;
$fechaCom = date ( 'Y-m-j' , $nuevafecha );
$fechaCom = '2018-10-04';
$cons_seman = "SELECT * FROM semanas where fecha_inicial <= '$fechaCom' and anio = year(curdate()) order by semana";
//echo $cons_seman;
$rs_semana = mysqli_query($enlace,$cons_seman);
while($rowS=mysqli_fetch_assoc($rs_semana)){
  $fecini=$rowS['fecha_inicial'];
  $fecfin=$rowS['fecha_final'];
  $desde=$rowS['fecha_inicial'];
  $hasta=$rowS['fecha_final'];
  $semana=$rowS['semana'];
  $sema=str_replace("*","",$semana);
   $semana = substr($sema, 2, 2);
}

 $ComSem = mysqli_query($enlace,"SELECT  sum(comision) as monto  FROM  desgloseGastos where fecha  BETWEEN '$desde' AND '$hasta' ");
 //echo "SELECT  sum(comision) as monto  FROM  desgloseGastosResp where fecha  BETWEEN '$desde' AND '$hasta' and pagoCom =''";
while($rowC=mysqli_fetch_assoc($ComSem)){
  $montoCom=$rowC['monto'];
}

//S "Monto Com-->".$montoCom;
$mtocomisEf=0;
$mtocomisEd=0;
$mtocomisCC=0;
     $ssqlC="SELECT * FROM desgloseGastos where comision > 0 and fecha  BETWEEN  '$desde' AND '$hasta'  and pagoCom =''";
	// echo $ssqlC;
     $qryC = mysqli_query($enlace,$ssqlC);
     while($arrC = mysqli_fetch_assoc($qryC))
      {
		  $comision=$arrC['comision'];
           $viaje=$arrC['viaje'];
		if  ($mtocomisEf <=$impEfec){   
		// echo "Viaje---:".$viaje."comision---:".$comision." Efectivo";	
		 $mtocomisEf+=$arrC['comision'];
	  } elseif  ($mtocomisEd <=$impEden){
		// echo "Viaje---:".$viaje."comision---:".$comision." Edenred";	
		   $mtocomisEd+=$arrC['comision'];
	  } else{
		 //  echo "Viaje---:".$viaje."comision---:".$comision." Gtos";	
		  
	  }
    

	$comision=$arrC['comision'];
	 if ($arrC['comision'] > 0)
	      {$pagoCom=$arrC['pagoCom'];
	  } else {$pagoCom='';}

  }

   
	 $consA=mysqli_query($enlace,"update desgloseGastos set correo='Enviado' where correo =''");
    // echo $queryIns	ertar;
//	mysqli_query($enlace,$queryInsertar);

header("Location: histDesglose.php");
}

/**************************AQUI*************************** */
if(isset($_GET['confir'])){
$fechaE=date("Y-m-d");
//print_r($_GET);
include_once('../Classes/class.phpmailer.php');

$body = '<b>Desglose  Gastos</b><br /><br />';

//---------------------------------------------------------------
 //Cuerpo del Mensaje
$body .='<table border="1" style="width:450px;"  colspan="5" cellpadding="2" cellspacing="2">';
$body.='<tr>';
$body.=' <td width="30%"><div align="center"><strong>Viaje</strong></td>';
$body.=' <td width="30%"><div align="center"><strong>Operador</strong></td>';
$body.=' <td width="30%"><div align="center"><strong>Destino</strong></div></td>';
$body.=' <td width="30%"><div align="center"><strong>Diesel</strong></div></td>';
$body.=' <td width="20%"><div align="center"><strong>Modo Pago</strong></div></td>';
$body.=' <td width="30%"><div align="center"><strong>Casetas</strong></div></td>';
$body.=' <td width="20%"><div align="center"><strong>Modo Pago</strong></div></td>';
$body.=' <td width="30%"><div align="center"><strong>Alimentos</strong></div></td>';
$body.=' <td width="20%"><div align="center"><strong>Modo Pago</strong></div></td>';
$body.=' <td width="30%"><div align="center"><strong>Comision</strong></div></td>';
$body.=' <td width="20%"><div align="center"><strong>Modo Pago</strong></div></td>';
$body.=' <td width="30%"><div align="center"><strong>Transito</strong></div></td>';
$body.=' <td width="20%"><div align="center"><strong>Modo Pago</strong></div></td>';

$body.='</tr>';

$mysqli = mysqli_init();
$mysqli->options(mysqlI_OPT_CONNECT_TIMEOUT, 5);
$mysqli->set_charset("utf8");
$enlace = mysqli_connect("db574183143.db.1and1.com", "dbo574183143", "b3lug42015", "db574183143");
 
     $ssqlC="select * from desgloseGastos  where correo ='' and status ='Realizado'";
	 //echo $ssqlC;
     $qryC = mysqli_query($enlace,$ssqlC);
     while($arrC = mysqli_fetch_assoc($qryC))
      {
       $fecUso=$arrC['fecha'];
	   $dest=$arrC['destino'];
	   $Oper=$arrC['operador'];
	   $viaje=$arrC['viaje'];
       $diesel=$arrC['diesel'];
	   if ($arrC['diesel'] > 0) {$pagoDie=$arrC['pagoDie'];
	  } else {$pagoDie='';}
	  
      $casetas=$arrC['casetas'];
	  if ($arrC['casetas'] > 0)
	      {$pagoCas=$arrC['pagoCas'];;
	  } else {$pagoCas='';}
	  
		
	  
		$alimentos=$arrC['alimentos'];
		 if ($arrC['alimentos'] > 0)
	      {$pagoAli=$arrC['pagoAli'];
	  } else {$pagoAli='';}

	$comision=$arrC['comision'];
	 if ($arrC['comision'] > 0)
	      {$pagoCom=$arrC['pagoCom'];
	  } else {$pagoCom='';}

			

		$transito=$arrC['transito'];
		 if ($arrC['transito'] > 0)
	      {$pagoCom=$arrC['pagoTra'];
	  } else {$pagoTra='';}

		 
	 $body.='<tr>';
  
 //echo "SELECT nombre FROM empleados where nombre_ref='$Oper'";
  $resE = mysqli_query($enlace,"SELECT nombre FROM empleados where nombre_ref='$Oper'"); 
        while($rowE=mysqli_fetch_assoc($resE)){
         $nombreE=$rowE['nombre']; 
        }
  
  $body.=' <td align="center">'.$viaje.'</td>';
  $body.=' <td>'.$nombreE.'</td>';
  $body.=' <td align="center">'.$dest.'</td>';
  $body.=' <td align="right">'.number_format($diesel,2).'</td>';
  $body.=' <td align="right">'.$pagoDie.'</td>';
  $body.=' <td align="right">'.number_format($casetas,2).'</td>';
  $body.=' <td align="right">'.$pagoCas.'</td>';
  $body.=' <td align="right">'.number_format($alimentos,2).'</td>';
  $body.=' <td align="right">'.$pagoAli.'</td>';
  $body.=' <td align="right">'.number_format($comision,2).'</td>';
  $body.=' <td align="right">'.$pagoCom.'</td>';
  $body.=' <td align="right">'.number_format($transito,2).'</td>';
  $body.=' <td align="right">'.$pagotra.'</td>'; 
  
  $body.='</tr>';

  }
$body.='</table>';
 //echo  $body;   
	 $consA=mysqli_query($enlace,"update desgloseGastos set correo='Enviado' where correo =''");
   //este  echo $queryIns	ertar;
//ESTE	mysqli_query($enlace,$queryInsertar);



//----------------------------------------------------
$mail = new PHPMailer();
$mail->Host = "smtp.1and1.com";
$mail->From = "operacion@belugamexico.com.mx";
$mail->Mailer="smtp";
$mail->FromName = "Beluga Logistics";
$mail->SMTPAuth   = true;                // enable SMTP authentication
$mail->Username   = "operacion@belugamexico.com.mx";  // GMAIL username
$mail->Password   = "Beluga123.";
$mail->Subject = "Desglose Gastos";
$mail->IsHTML(true);

$mail->AddAddress("francisco@belugamexico.com.mx");

$mail->AddAddress("david@belugamexico.com.mx");
//$mail->AddAddress("jesus@belugamexico.com.mx");
//$mail->AddAddress("leon@belugamexico.com.mx");
$mail->AddAddress("lourdes@belugamexico.com.mx");
$mail->AddAddress("operaciones@belugamexico.com.mx");
//$mail->AddAddress($email");
 $mail->Body=$body;
$mail->Send();

	
	
	
header("Location: histDesglose.php");
}


//*****************************************************
if(isset($_GET['caset'])){
	$caseta=$_GET['caset'];
	$idReg=$_GET['idReg'];
//	print_r($_GET);
 mysqli_connect("db574183143.db.1and1.com", "dbo574183143", "b3lug42015") or die("NO se pudo realizar la conexion");
 mysqli_select_db("db574183143");	

 //echo "update desgloseGastos set casetas ='$caseta' where id ='$idReg'  ";
   
	 $consA=mysqli_query($enlace,"update desgloseGastos set casetas ='$caseta' where id ='$idReg'");
	 
     $resE = mysqli_query($enlace,"SELECT viaje  FROM desgloseGastos where id ='$idReg'"); 
        while($rowE=mysqli_fetch_assoc($resE)){
         $viajeCas=$rowE['viaje']; 
        }

    $resS = mysqli_query($enlace,"SELECT sum(casetas) as caseta   FROM desgloseGastos where viaje  ='$viajeCas'"); 
        while($rowE=mysqli_fetch_assoc($resS)){
         $montoCas=$rowE['caseta']; 
        }

//echo "update operaciones set casetas =' $montoCas' where viaje ='$viajeCas'";
	 $consO=mysqli_query($enlace,"update operaciones set casetas =' $montoCas' where viaje ='$viajeCas'");
    // echo $queryIns	ertar;
//	mysqli_query($enlace,$consA);
 //header("Location: histDesglose.php");
}






//}
?>
