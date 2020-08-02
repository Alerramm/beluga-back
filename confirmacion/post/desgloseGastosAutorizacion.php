<?php

session_start();
//include('validaDepositos.php'); 
$userL = "dtejeda";
//echo "Usuario-->".$userL;
$mysqli = mysqli_init();
$mysqli->options(mysqlI_OPT_CONNECT_TIMEOUT, 5);
$mysqli->set_charset("utf8");

$enlace = mysqli_connect("db574183143.db.1and1.com", "dbo574183143", "b3lug42015", "db574183143");
//**** QA
$enlace = mysqli_connect("db5000311817.hosting-data.io", "dbu13503", "belugaQA20.", "dbs304381");

//-*** comision
$fec1=date("Y-m-d");
$nuevafecha = strtotime ( '-1 day' , strtotime ( $fec1 ) ) ;
$fechaCom = date ( 'Y-m-j' , $nuevafecha );
//and anio = year(curdate())

$meses=array("Ene","Feb","Mzo","Abr","May","Jun","Jul","Ago","Sep","Oct","Nov","Dic"); 
$dias=array("01","02","03","04","05","06","07","08","09","10","11","12","13","14","15","16","17","18","19","20","21","22","23","24","25","26","27","28","29","30","31"); 

$cons_seman = "SELECT * FROM semanas where fecha_inicial <= '$fechaCom'  order by semana";
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
    $fchdes=explode("-",$desde);
	$dddes=$fchdes[2];
	$mesdes=$fchdes[1];
	$anides=$fchdes[0];
	$mesLetI=$meses[abs($mesdes)-1];
	
	$fchhas=explode("-",$hasta);
	$ddhas=$fchhas[2];
	$meshas=$fchhas[1];
	$anihas=$fchhas[0];
	$mesLetF=$meses[abs($meshas)-1];
	
   $fini =$dddes." ".$mesLetI; 
   $ffin =$ddhas." ".$mesLetF;

$statusaux = " fecha >='2019-12-01'" ;

$tot=0;
//echo "RANGO-->".$rango;
//echo "SELECT  min(viaje) Viamin, max(viaje) Viamax FROM operaciones where $statusaux $rango and status <> 'Carga* and viaje < 90000'";
$solicQry1 = mysqli_query($enlace,"SELECT  min(viaje) Viamin, max(viaje) Viamax FROM operaciones where  viaje < '99000' and $statusaux $rango and status not in ('27','Carga*') ");
while($reg=mysqli_fetch_assoc($solicQry1)){
  $Viamin=$reg['Viamin'];
  $Viamax=$reg['Viamax'];
  $Viamax='13099';
}
$rango = " and abs(viaje) BETWEEN '$Viamin' AND '$Viamax' ";


?>

<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">

<html>
  <head>
   <a style="margin-left:-1100px; margin-top:-261px"  href="../imagenes/./"> <img  width='90' height='60' src="../imagenes/Logo_beluga.png"/> </a> <!-- Logo -->
    <meta charset="utf-8">
    <meta http-equiv="X-UA-Compatible" content="chrome=1">
    <title>Gastos - Autorizacíon</title>


    <link rel="stylesheet" href="EstiloCtas.css">

	<script src="file:///C|/wamp/www/giant/js/jquery.effects.core.js"></script>
<script src="file:///C|/wamp/www/giant/js/jquery.scrollTo-1.4.3.1.js"></script>
<script type="ext/javascript" src="http://jzaefferer.github.com/jquery-validation/jquery.validate.js"></script>

<link href="../css/custom-theme/jqry-ui-1.9.0.custom.css" rel="stylesheet">
<script src="../js/jquery-1.8.2.js"></script>
<script src="../js/jquery-ui-1.9.0.custom.js"></script>
 
		<script src="https://unpkg.com/sweetalert/dist/sweetalert.min.js"></script>	
	<script>
/*-----------FUNCION DE CALENDARIO-----------*/
$(function() {
	$( ".calendario" ).datepicker({dateFormat:'yy-mm-dd', showAnim: 'fold'});
});
</script>
<script>
function abrir_dialog() {
      $( "#dialog" ).dialog({
          modal: true
      });
    };
 </script>
 <script>
function actGpo() {
	//alert("actpen");
      $( "#dialogGpo" ).dialog({
         modal: true,
		  top:130,
		  width:450
      });
    };
 </script>
<script>
function actPen() {
      $( "#dialogPen" ).dialog({
         modal: true,
		  top:130,
		  width:230
      });
    };
 </script>
<script>
function actCom() {
	//alert("actcom");
      $( "#dialogCom" ).dialog({
         modal: true,
		  top:230,
		  width:300
      });
    };
 </script>
 
<script type="text/javascript">
function mostrar(){
	document.getElementById('dial').style.display='block';
}
function ocultar(){

	document.getElementById('dial').style.display='none';
}
</script>
 <script>

function MM_jumpMenu(targ,selObj,restore){ //v3.0
  eval(targ+".location='"+selObj.options[selObj.selectedIndex].value+"'");
  if (restore) selObj.selectedIndex=0;
}
</script>
<style type="text/css">
       {
          color:#7F7F7F;
          font-family:Arial,sans-serif;
          font-size:12px;
          font-weight:normal;
      }    
      .config .title{
          font-weight: bold;
          text-align: center;
      }
      .config .barcode2D,
      #miscCanvas{
        display: none;
      }
      #submit{
          clear: both;
      }
      #barcodeTarget,
      #canvasTarget{
        margin-top: 20px;
      }        
	  h3 {
	text-align:center;
	font-family:"Arial";
	font-size:25px;
	color:white;
	line-height:5px;
	text-shadow:rgba(0,0,0,0.3) 3px 3px 7px;
	-webkit-font-smoothing: antialiased;
	-webkit-font-smoothing: subpixel-antialiased;
}

a.back{
            width:350px;
            height:73px;
            position:fixed;
            bottom:15px;
            right:15px;
            background:#fff url(file:///C|/wamp/www/beluga/Finanzas/codrops_back.png) no-repeat top left;
            z-index:1;
            cursor:pointer;
        }
    	
        a.activator{
            width:auto;
            height:auto;
            position:absolute;
			left:63%;
            /*left:5px;*/
            cursor:pointer;
        }

        /* Style for overlay and box */
        .overlay{
            background:transparent url(file:///C|/wamp/www/beluga/Finanzas/images/overlay.png) repeat top left;
            position:fixed;
            top:0px;
            bottom:0px;
            left:0px;
            right:0px;  
            z-index:100;
        }

		    .box{
            position:fixed;
            top:-1000px;
            left:20%;
            right:20%;
            background-color:#fff;
            color:#7F7F7F;
            padding:20px;
            border:2px solid #ccc;
            -moz-border-radius: 20px;
            -webkit-border-radius:20px;
            -khtml-border-radius:20px;
            -moz-box-shadow: 0 1px 5px #333;
            -webkit-box-shadow: 0 1px 5px #333;
            z-index:101;
        }

       .box h1{
            border-bottom: 1px dashed #7F7F7F;
            margin:-20px -20px 0px -20px;
            padding:10px;
			background-color:#c1e0ff;
            color:#FFF;
            /*background-color:#FFEFEF;
            color:#EF7777;*/
            -moz-border-radius:20px 20px 0px 0px;
            -webkit-border-top-left-radius: 20px;
            -webkit-border-top-right-radius: 20px;
            -khtml-border-top-left-radius: 20px;
            -khtml-border-top-right-radius: 20px;
        }
  
        a.boxclose{
            float:right;
            width:26px;
            height:26px;
            background:transparent url(../images/cancel.gif) repeat top left;
            margin-top:-30px;
            margin-right:-30px;
            cursor:pointer;
        }

    </style>	
	<style type="text/css">
body
{
	font-family: arial, helvetica, sans-serif;
}
body{width:100%;}
body {
height: 100%;
margin: 0;
padding: 0;
background-repeat: no-repeat;
background: #fff;
/*background: #afb1b4; /* Old browsers 
background: -moz-linear-gradient(top, black 0%, white 100%) no-repeat; /* FF3.6+ */
/*background: -webkit-gradient(linear, left top, left bottom, color-stop(0%,#afb1b4), color-stop(100%,#696a6d)); /* Chrome,Safari4+ */
/*background: -webkit-linear-gradient(top, #141414 30%, white 100%); /* Chrome10+,Safari5.1+ */
/*background: -o-linear-gradient(top, black 0%,white 100%); /* Opera11.10+ */*/s
/*background: -ms-linear-gradient(top, black 0%,white 100%); /* IE10+ */
filter: progid:DXImageTransform.Microsoft.gradient( startColorstr='black', endColorstr='white',GradientType=0 ); /* IE6-9 */
background: linear-gradient(top, #afb1b4 0%,#696a6d 100%); /* W3C */
}


table
{
	border-collapse: collapse;
	margin-bottom: 3em;
	font-size: 95%;
	line-height: 1.1;
	width:95%;
	
}

tr:hover, td.start:hover, td.end:hover
{
	
}

th, td
{
	padding: .3em .5em;
}

th
{
	font-weight: normal;
	text-align: left;
	background: url(file:///C|/wamp/www/giant/editablegrid/tabletree-arrow.gif) no-repeat 2px 50%;
	padding-left: 15px;
}

th.name { width: 12em; }
th.location { width: 12em; }
th.color { width: 10em; }

thead th
{
	background: #c6ceda;
	border-color: #fff #fff #888 #fff;
	border-style: solid;
	border-width: 1px 1px 2px 1px;
	padding-left: .5em;
}

tbody th.start
{
	background: url(file:///G|/giant/editablegrid/tabletree-dots.gif) 18px 54% no-repeat;
	padding-left: 26px;
}

tbody th.end
{
	background: url(file:///G|/giant/editablegrid/tabletree-dots2.gif) 18px 54% no-repeat;
	padding-left: 26px;
}

hr.linea {
	height: 1px;
border:3px dotted #ccc;
color: #cc0000;
background-color: #333333;
width: 100%;
	}

.msjSI{
	color:#0C0;
	text-align:center;
	font-size:9px;
	}
	
.msjNO{
	color:#0C0;
	text-align:center;
	font-size:9px;
	}
</style>
    <meta name="viewport" content="width=device-width, initial-scale=1, user-scalable=no">
    <!--[if lt IE 9]>
    <script src="//html5shiv.googlecode.com/svn/trunk/html5.js"></script>
    <![endif]-->
  </head>
  <body>
  <div id="TopHeader" class="TopHeaderBar Center">
  </div>
      

	<div class="wrapper">

<img src='../imagenes/actualizar.png' title ='Tabla Grupos' border='0' onClick='actGpo();'  onMouseOver='style.cursor=cursor'>
      <h1>&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;
	  <a>Gastos </a> Autorizacíon<span style='font-size:14px; font-weight:bold; color:#0c4a6e;'>
	 	  <table   border="2"  align="left"  style=" width:155px; margin-top:-10px;margin-left:70px;" >
	  <!--<tr><td colspan=3>GASTOS POR APLICAR</td></tr>-->

<?
//echo "SELECT viaje ,sum(PREdiesel) pres, sum(diesel) gtos FROM desgloseGastos where 1 $rango group by viaje";
$solQryAdic = mysqli_query($enlace,"SELECT viaje ,sum(PREdiesel) pres, sum(diesel) gtos FROM desgloseGastos where  1 $rango group by viaje");
$d=0;
while($regR=mysqli_fetch_assoc($solQryAdic)){

	if ($regR['gtos']  < $regR['pres']){
			$d++;
     $AplDie +=($regR['pres']-  $regR['gtos']);
	 $DieArr[$d]=$regR['viaje'];
	 $DieArr1[$d]=$regR['pres']-  $regR['gtos'];
   }
}

$solQryAdic = mysqli_query($enlace,"SELECT viaje ,sum(PREcasetas) pres, sum(casetas) gtos FROM desgloseGastos where 1 $rango group by viaje");
$c=0;
while($regR=mysqli_fetch_assoc($solQryAdic)){
	if ($regR['gtos']  < $regR['pres']){
		$c++;
       $AplCas +=($regR['pres']-  $regR['gtos']);
	   $CasArr[$c]=$regR['viaje'];
	   $CasArr1[$c]=$regR['pres']-  $regR['gtos'];
	}
}

$solQryAdic = mysqli_query($enlace,"SELECT viaje ,sum(PREalimentos) pres, sum(alimentos) gtos FROM desgloseGastos where 1 $rango group by viaje");
$a=0;
while($regR=mysqli_fetch_assoc($solQryAdic)){
	if ($regR['gtos']  < $regR['pres']){
		$AplAli +=($regR['pres']-  $regR['gtos']);
		$a++;
		$AliArr[$c]=$regR['viaje'];
	    $AliArr1[$c]=$regR['pres']-  $regR['gtos'];}
}

$solQryAdic = mysqli_query($enlace,"SELECT viaje ,sum(PREestadias) pres, sum(estadias) gtos FROM desgloseGastosAut where 1 $rango group by viaje");
$e=0;
while($regR=mysqli_fetch_assoc($solQryAdic)){
	if ($regR['gtos']  < $regR['pres']){
      $AplEst +=($regR['pres']-  $regR['gtos']);
	   $e++;
		$EstArr[$c]=$regR['viaje'];
	    $EstArr1[$c]=$regR['pres']-  $regR['gtos'];}
}

$solQryAdic = mysqli_query($enlace,"SELECT viaje ,sum(PREcomision) pres, sum(comision) gtos FROM desgloseGastosAut where 1 $rango group by viaje");
$cO=0;
while($regR=mysqli_fetch_assoc($solQryAdic)){
	if ($regR['gtos']  < $regR['pres']){
       $AplCom +=($regR['pres']-  $regR['gtos']);
        $cO++;
		$ComArr[$cO]=$regR['viaje'];
	    $ComArr1[$cO]=$regR['pres']-  $regR['gtos'];}
}

$solQryAdic = mysqli_query($enlace,"SELECT viaje ,sum(PREmaniobras) pres, sum(maniobras) gtos FROM desgloseGastosAut where 1 $rango group by viaje");
$m=0;
while($regR=mysqli_fetch_assoc($solQryAdic)){
	if ($regR['gtos']  < $regR['pres']){
     $AplMan +=($regR['pres']-  $regR['gtos']);
	 $m++;
		$ManArr[$m]=$regR['viaje'];
	    $ManArr1[$m]=$regR['pres']-  $regR['gtos'];}
}

$solQryAdic = mysqli_query($enlace,"SELECT viaje ,sum(PREtransito) pres, sum(transito) gtos FROM desgloseGastosAut where 1 $rango group by viaje");
$t=0;
while($regR=mysqli_fetch_assoc($solQryAdic)){
	if ($regR['gtos']  < $regR['pres']){
		$AplTra +=($regR['pres']-  $regR['gtos']);
		$t++;
		$TraArr[$t]=$regR['viaje'];
	    $TraArr1[$t]=$regR['pres']-  $regR['gtos'];}
}

$solQryAdic = mysqli_query($enlace,"SELECT viaje ,sum(PREmantenimiento) pres, sum(mantenimiento) gtos FROM desgloseGastosAut where 1 $rango group by viaje");
$mt=0;
while($regR=mysqli_fetch_assoc($solQryAdic)){
	if ($regR['gtos']  < $regR['pres']){
	  $AplMto +=($regR['pres']-  $regR['gtos']);
      $mt++;
	  $MtoArr[$mt]=$regR['viaje'];
	  $MtoArr1[$mt]=$regR['pres']-  $regR['gtos'];}
}

$solQryAdic = mysqli_query($enlace,"SELECT viaje ,sum(PRErepartos) pres, sum(repartos) gtos FROM desgloseGastosAut where 1 $rango group by viaje");
$re=0;
while($regR=mysqli_fetch_assoc($solQryAdic)){
	if ($regR['gtos']  < $regR['pres']){
	  $AplRep +=($regR['pres']-  $regR['gtos']);
      $re++;
	  $RepArr[$re]=$regR['viaje'];
	  $RepArr1[$re]=$regR['pres']-  $regR['gtos'];}
}

$solQryAdic = mysqli_query($enlace,"SELECT viaje ,sum(PREviaticos) pres, sum(viaticos) gtos FROM desgloseGastosAut where 1 $rango group by viaje");
$vi=0;
while($regR=mysqli_fetch_assoc($solQryAdic)){
	if ($regR['gtos']  < $regR['pres']){
	  $AplVia +=($regR['pres']-  $regR['gtos']);
      $vi++;
	  $ViaArr[$vi]=$regR['viaje'];
	  $ViaArr1[$vi]=$regR['pres']-  $regR['gtos'];}
}

	     if ($AplDie > 0){ 
	        $totGtosPen+=$AplDie;
	   }
	    if ($AplCas > 0){
            $totGtosPen+=$AplCas; }
	    if ($AplAli > 0){ 
		    $totGtosPen+=$AplAli ;}
	    if ($AplCom > 0){ 
			$totGtosPen+=$AplCom; }
	    if ($AplEst > 0){ 
			$totGtosPen+=$AplEst; }
	    if ($AplMan > 0){
            $totGtosPen+=$AplMan;}
	    if ($AplTra > 0){ 
			 $totGtosPen+=$AplTra;}
	    if ($AplMto > 0){ 
			$totGtosPen+=$AplMto ; }
		if ($AplVia > 0){ 
			$totGtosPen+=$AplVia ; }
	
	    $totR=$d+$c+$a+$cO+$e+$m+$t+$mt+$re+$vi;?> 
	        
	      <tr>
			<td  align="center"><span style="font-size:12px;">GASTOS POR APLICAR</td>
			<td  align="center"><? echo "<span style='font-size:16px; font-weight:bold; '>".number_format($totR,0)."</span>";?> </td>
		    <td   onClick='actPen();' align="center"><? echo "<span style='font-size:16px; font-weight:bold; '>".number_format(abs($totGtosPen),2)."</span>";?> </td>
		   </tr>
	</table> 
	  <?
for ($i=1;$i<=35;$i++){
?>
  &nbsp;
    <? }

//echo  "SELECT  sum(PREcomision) as montoP,sum(comision) as montoC  FROM  desgloseGastos where  pagoComp=''";
 $ComSem = mysqli_query($enlace,"SELECT  sum(PREcomision) as montoP,sum(comision) as montoC  FROM  desgloseGastosAut where 1");
while($rowC=mysqli_fetch_assoc($ComSem)){
	
  $montoCom=$rowC['montoP']-$rowC['montoC'];
}
?>
<table   border="1"  align="left"  style=" width:250px; margin-left:820px;" >
   <tr>
  <!-- <td colspan='2'><?echo  " Semana del ". $fini." al ".$ffin ;?> </td>-->
   </tr>
    <tr>
      <td onClick='actCom();'><?echo  "Comisiones Acumuladas:$". number_format($montoCom,2);?></td>
   </tr>
 </table>
 <?  for ($i=1;$i<355;$i++) { ?> &nbsp  <? } ?>
 <!-- function confirma()-->
<button value="Confirma"><a href="ac_desglose.php?confir=Envia">Confirma</a></button>

<?php 
$mysqli = mysqli_init();
$mysqli->options(MYSQLI_OPT_CONNECT_TIMEOUT, 5);
$mysqli->set_charset("utf8");
$enlace = mysqli_connect("db574183143.db.1and1.com", "dbo574183143", "b3lug42015", "db574183143");
//**** QA
$enlace = mysqli_connect("db5000311817.hosting-data.io", "dbu13503", "belugaQA20.", "dbs304381");


$sub = 0;
$subtot=0;
$i=0;
$totorden=0;
?>
<div id="table-scroll">


<div id="cuerpoDatos">

<div id="nofixedX">
<table border ='1'>
<thead>
    <tr > 
		<th style="color: blue;width:90px;"><b>Viaje<b></th>
		<th style="color: blue;width:90px;"><b>Cliente<b></th>
		<th style="color: blue ;width:300px;"><b>Operador<b></th>
		<th style="color: blue ;width:300px;"><b>Unidad<b></th>
		<th style="color: blue;width:190px;"><b>Destino<b></th>
		<th style="color: blue;width:90px;"><b>Costo<b></th>
		<th style="color: blue;width:90px;"><b>Dias<b></th>
		<th style="color: blue;width:70px;"><b>Kms-Categ<b></th>
		<th style="color: blue;width:70px;"><b>Rend.<b></th>
		<th style="color: blue;width:150px;"><b>Fecha<b></th>
		<th style="color: blue;width:150px;"><b>Hra<b></th>
		<th style="color: blue;width:160px;"><b>Diesel<b></th>
		<th style="color: blue;width:160px;"><b>Casetas<b></th>
		<th style="color: blue;width:160px;"><b>Alimentos<b></th>
		<th style="color: blue;width:160px;"><b>Transito<b></th>
		<th style="color: blue;width:160px;"><b>Maniobras<b></th>
        <th style="color: blue"><b>Comision<b></th>
		<th style="color: blue"><b>Solicita<b></th>
		<th style="color: blue"><b>Actual<b></th>
	</tr>
	   </thead>
<tbody>
	  
		  <?
				$totdie =0;
			$totcas =0;
			$totali =0;
			$totcom =0;
			$tottra=0;

			$consul="SELECT *,DATE_FORMAT(fecha,'%d/%m/%Y') fecha  from desgloseGastosAut where status in ('','Incompleto')  order by viaje asc";
  		   	$res = mysqli_query($enlace,$consul);
		    while($row2=mysqli_fetch_assoc($res)){
        			$nomaux=$row2['operador'];
        			$viaaux=$row2['viaje'];
                    $totdie +=$row2['PREdiesel']-$row2['diesel'];
					$totcas +=$row2['PREcasetas']-$row2['casetas'];
        			$totali +=$row2['PREalimentos']-$row2['alimentos'];
        			$totcom +=$row2['PREcomision']-$row2['comision'];
        			$tottra +=$row2['PREtransito']-$row2['transito'];	
                    $totman +=$row2['PREmaniobras']-$row2['maniobras'];
					$Stat=$row2['status'];
                    $nombreE =$nomaux;
		    $resE = mysqli_query($enlace,"SELECT nombre FROM empleados where nombre_ref='$nomaux'"); 
            while($rowE=mysqli_fetch_assoc($resE)){
             $nombreE=$rowE['nombre']; 
             }	
			 $resO = mysqli_query($enlace,"SELECT date_format(fecha,'%H:%i') as Hra, kms,cliente,precio,unidad ,dias FROM operaciones where viaje='$viaaux'"); 
            while($rowO=mysqli_fetch_assoc($resO)){
             $cliente=$rowO['cliente'];
             $precio=$rowO['precio'];			 
			 $kms=$rowO['kms']; 
             $unid=$rowO['unidad'];
             $hra=$rowO['Hra'];			 
			 $numDias=$rowO['dias'];
             }

			  $cons_mysql= "SELECT descripcion,numDias FROM  tipoViajeAct where '$kms' between kmsIni and kmsFin";
				$resCon=mysqli_query($enlace,$cons_mysql);
				while($edo=mysqli_fetch_assoc($resCon)){
					$pgpo=$edo['descripcion'];
				//	$numDias=$edo['numDias'];
				 }
				 $die_mysql= "SELECT precio FROM  DieselPrecio where 1";
				$resDie=mysqli_query($enlace,$die_mysql);
				while($die=mysqli_fetch_assoc($resDie)){
					$preDie=$die['precio'];
				 }

			  $uni_mysql= "SELECT  rendimientoLocal,rendimientoForaneo FROM  unidades where camion ='$unid' ";
				$resUni=mysqli_query($enlace,$uni_mysql);
				while($uni=mysqli_fetch_assoc($resUni)){
					$renLoc=$uni['rendimientoLocal'];
                    $renFor=$uni['rendimientoForaneo'];
				 }

            if ($pgpo =='Local A'){
                $rendUnidad =$renLoc;
            } else {
                $rendUnidad =$renFor;
              }
			  if($Stat=='Incompleto'){$ren='3';} else{$ren='2';}
?> 

  		  	<!--<tr class="treegrid-<?// echo $i;?> treegrid-parent-<?//echo $j;?>">-->
			<tr>
			 <?
          $viajeC = $row2['viaje'];
          if ($viajeC=='99999'){
            $viajeC='-----';
          }else{
            $viajeC = $row2['viaje'];
          }
        ?>
			 <td rowspan=<? echo $ren; ?>><b><? echo $viajeC; ?></b></td>
			 <td rowspan=<? echo $ren; ?>><b><? echo $cliente; ?></b></td>
			 <td rowspan=<? echo $ren; ?>><b><? echo $nombreE; ?></b></td>
			  <td rowspan=<? echo $ren; ?>><b><? echo $unid; ?></b></td>
	    	 <td rowspan=<? echo $ren; ?>><b><? echo $row2['destino']; ?></b></td>
			 <td rowspan=<? echo $ren; ?>><b><? echo number_format($precio,2); ?></b></td>
			 <td rowspan=<? echo $ren; ?>><b><? echo $numDias; ?></b></td>
          <?        $Ltos = $row2['PREdiesel']/$preDie;
		     $rend = ($kms/$Ltos);

            if ( $rend >= $rendUnidad){
              $colorU='color:#F73308';
				} else {
				$colorU='color:#70A025 ';
              }
 ?>
			 <td rowspan=<? echo $ren; ?>><b><? echo $kms."-".$pgpo; ?></b></td>
			 <td style='width:80px; <? echo $colorU;?>' rowspan=<? echo $ren; ?>><b><? echo number_format($rend,2); ?></b></td>
			 <td rowspan=<? echo $ren; ?>><b><? echo $row2['fecha']; ?></b></td>
			 <td rowspan=<? echo $ren; ?>><b><? echo $hra; ?></b></td>
			 <td ><b><? echo number_format($row2['PREdiesel'],2); ?></b></td>
			 <td ><b><? echo number_format($row2['PREcasetas'],2); ?></b></td> 
			 <td ><b><? echo number_format($row2['PREalimentos'],2); ?></b></td>
             <td ><b><? echo number_format($row2['PREtransito'],2); ?></b></td>
			 <td ><b><? echo number_format($row2['PREmaniobras'],2); ?></b></td>
		     <td ><b><? echo number_format($row2['PREcomision'],2); ?></b></td>
			 <td rowspan=<? echo $ren; ?>><b><? echo strtoupper($row2['solicita']); ?></b></td>
			  </tr>
			 <!-- ****DEPOSITOS RENGLON ***   --> 

          <? if($Stat=='Incompleto'){
				$colorU='color:#70A025';			  
		  ?>
		  <tr>
			 <td style=' <? echo $colorU;?>' ><b><? echo number_format($row2['diesel'],2); ?></b></td>
			 <td style=' <? echo $colorU;?>'><b><? echo number_format($row2['casetas'],2); ?></b></td> 
			 <td style=' <? echo $colorU;?>'><b><? echo number_format($row2['alimentos'],2); ?></b></td>
             <td style=' <? echo $colorU;?>'><b><? echo number_format($row2['transito'],2); ?></b></td>
			 <td style=' <? echo $colorU;?>'><b><? echo number_format($row2['maniobras'],2); ?></b></td>
		     <td style=' <? echo $colorU;?>' ><b><? echo number_format($row2['comision'],2); ?></b></td>

			  </tr>			
           
		  <?}?>
			
			<!-- ****SEGUNDO RENGLON ***   -->
			<tr>
			  <td  style ='width:160px;' ><input type='number' min='1' onChange='valida(<? echo $row2['viaje'];?>,<? echo $row2['PREdiesel'];?>,1)' id='tImpDie<? echo $row2['viaje'];?>'  style ='width:50px;' value =''></td> 
			  <td style ='width:160px;'><input type='number' min='1' onChange='valida(<? echo $row2['viaje'];?>,<? echo $row2['PREcasetas'];?>,2)' id='tImpCas<? echo $row2['viaje'];?>'  style ='width:50px;' value =''/>	</td> 
			  <td style ='width:160px;' ><input type='number' min='1' onChange='valida(<? echo $row2['viaje'];?>,<? echo $row2['PREalimentos'];?>,3)' id='tImpAli<? echo $row2['viaje'];?>'  style ='width:50px;' value =''/></td>
              <td style ='width:160px;'><input type='number' min='1' onChange='valida(<? echo $row2['viaje'];?>,<? echo $row2['PREtransito'];?>,4)' id='tImpTra<? echo $row2['viaje'];?>'  style ='width:50px;'/></td>
		   	  <td style ='width:160px;'><input type='number' min='1' onChange='valida(<? echo $row2['viaje'];?>,<? echo $row2['PREmaniobras'];?>,5)' id='tImpMan<? echo $row2['viaje'];?>'  style ='width:50px;'/></td> 
			  <td style ='width:160px;'><input type='number' min='1' onChange='valida(<? echo $row2['viaje'];?>,<? echo $row2['PREcomision'];?>,6)' id='tImpCom<? echo $row2['viaje'];?>'  style ='width:50px;'/></td> 
<td>
<!--***************-->
<?
$GtosAcum=$row2['PREdiesel']+$row2['PREcasetas']+$row2['PREalimentos']+$row2['PREtransito']+$row2['PREmaniobras']+$row2['PREcomision'];
$cons_mysql= "SELECT porcGto FROM  tipoViajeAct where '$kms' between kmsIni and kmsFin";
  $sqlGto=mysqli_query($enlace,$cons_mysql);
	while($Gto=mysqli_fetch_assoc($sqlGto)){
  	  $porcGto= $Gto['porcGto'];
	}

  $GtosAcum=number_format(($GtosAcum/1.16),2,'.','');
 $porGto =number_format(($GtosAcum/$precio)*100,2);
  $utilAc=number_format((($precio-$GtosAcum)/$precio)*100,2,'.','');
//echo "utilAc-->".$utilAc; 
//echo "porGtoGpo-->".$porGtoG////".$precio.",".$GtosAcum.",".$porGto."


if ($porGto > $porcGto) {
		 ?><img src='../img/notasRed.png' width='17' height='17' hspace='5' onclick="muestUtiM(<?echo $precio;?>,<?echo $GtosAcum;?>,<?echo$porGto;?>)"/>  <?
} else{
	?><img  src='../img/notasGreen.png' width='17' height='17' hspace='5'  onclick="muestUti(<?echo $precio;?>,<?echo $GtosAcum;?>,<?echo$porGto;?>)"/><?
	    
	}
?>
			 <?if($userL =='dtejeda' || $userL =='rperez' || $userL =='jesus' ){?>
		      <input type='checkbox' title ='Actual' value='".$row2['viaje']."' id='viaje<? echo $row2['viaje'];?>' id='viaje<? echo $row2['viaje'];?>' onclick="cambRefer('<? echo $row2['viaje'];?>','<? echo $row2['id'];?>');" style ='margin-top: 4px;    margin-bottom: 6px;'   /></td>
			  
			 <?}?>
			</tr>
		
		<? 
				 ?>
 <?  }?>
 	<tr>
		<td colspan='11'></td>
		<td style="color:blue" ><b><? echo number_format($totdie,2); ?></b></td>
		<td style="color:blue"><b><? echo number_format($totcas,2); ?></b></td>
		<td style="color:blue"><b><? echo number_format($totali,2); ?></b></td>
		<td style="color:blue"><b><? echo number_format($tottra,2); ?></b></td>	
		<td style="color:blue"><b><? echo number_format($totman,2); ?></b></td>	
	  <td style="color:blue"><b><? echo number_format($totcom,2); ?></b></td> 
				</tr>

  </tbody>
</table>
</div>
<hr align='left' noshade='noshade' size='2' width='98%' style='color: #0056b2;margin-bottom:10px;margin-top:10px;height:10px;' />	
	<!---**************************************************-->  
	<div class="wrapper">

      <h1><a>Desglose </a> Adicionales<span style='font-size:14px; font-weight:bold; color:#0c4a6e;'><?

?>
<div id="table-scroll">
 <div id="fixedY">
</div>

<div id="cuerpoDatos">
<div id="nofixedX">
<table border ='1'>
 <thead>
    <tr>
	  <th style="color: blue;width:90px;"><b>Viaje<b></th>
	  <th style="color: blue;width:90px;"><b>Cliente<b></th>
	  <th style="color: blue ;width:250px;"><b>Operador<b></th>
	  <th style="color: blue;width:230px;"><b>Destino<b></th>
	  <th style="color: blue;width:130px;"><b>Fecha<b></th>
	  <th style="color: blue;width:160px;"><b>Diesel<b></th>
	  <th style="color: blue;width:160px;"><b>Casetas<b></th>
	  <th style="color: blue;width:160px;"><b>Viaticos<b></th>
	  <th style="color: blue;width:160px;"><b>Transito<b></th>
	  <th style="color: blue;width:160px;"><b>Maniobras<b></th>
	  <th style="color: blue;width:160px;"><b>Estadias<b></th>
	  <th style="color: blue;width:160px;"><b>Repartos<b></th>
	  <th style="color: blue;width:160px;"><b>Mantenimiento<b></th>
	  <th style="color: blue"><b>Comision<b></th>
	  <th style="color: blue"><b>Solicita<b></th>
	  <th style="color: blue"><b>Actual<b></th>
	</tr>
 </thead>
<tbody>
		  <?
		    $totdie =0;
			$totcas =0;
			$totali =0;
			$totcom =0;
			$tottra=0;
			$totrep=0;
			$totest=0;
			$totman=0;
			$totmto=0;
			$consul="SELECT id,DATE_FORMAT(fecha,'%d/%m/%Y') fecha ,operador,destino,viaje,
			PREdiesel,diesel,PREcasetas,casetas,PREviaticos,viaticos,PREcomision,comision,PREtransito,transito,PREmaniobras,maniobras
			,PREestadias,estadias,PRErepartos,repartos,PREmantenimiento,mantenimiento,id,solicita,observacion from desgloseGastosAut where status='Adicional'  order by viaje asc";
  		   	$res = mysqli_query($enlace,$consul);
		    while($row2=mysqli_fetch_assoc($res)){
        		$nomaux=$row2['operador'];
        		$viaaux=$row2['viaje'];
        		$obser=$row2['observacion'];
        		$totdie +=$row2['PREdiesel'];
        		$totcas +=$row2['PREcasetas'];
        		$totvia +=$row2['PREviaticos'];
        		$totcom +=$row2['PREcomision'];
        		$tottra +=$row2['PREtransito'];	
                $totman +=$row2['PREmaniobras'];
				$totmto +=$row2['PREmantenimiento'];
				$totest +=$row2['PREestadias'];
				$totrep +=$row2['PRErepartos'];					
			$nombreE =$nomaux;
		    $resE = mysqli_query($enlace,"SELECT nombre FROM empleados where nombre_ref='$nomaux'"); 
            while($rowE=mysqli_fetch_assoc($resE)){
             $nombreE=$rowE['nombre']; 
             }	
			 $resO = mysqli_query($enlace,"SELECT cliente,destino,entregar FROM operaciones where viaje='$viaaux'"); 
            while($rowO=mysqli_fetch_assoc($resO)){
             $destino=$rowO['destino']; 
			 $cliente=$rowO['cliente']; 
			 $entregar=$rowO['entregar']; 
             }
			 $impMan=0;
			 if($row2['maniobras'] > 0){
			 $consDes="SELECT fecha,pagoMan,maniobras from desgloseGastos where status='Realizado' and viaje='$viaaux' and maniobras > 0";
			// echo $consDes; 
  		   	$resDes = mysqli_query($enlace,$consDes);
		    while($rowAD=mysqli_fetch_assoc($resDes)){
			//	 echo "MMMan->".$rowAD['maniobras'];
        	 		$fecdes=$rowAD['fecha'];
        			$pagoDes=$rowAD['pagoMan'];
                    $impMan =$rowAD['maniobras'];
			 }
		//	 echo "IMPMANnn-->".$impMan;
			 }
			 /*** **/
?> 
			<tr>
			  <?
          $viajeC = $row2['viaje'];
          if ($viajeC=='99999'){
            $viajeC='-----';
          }else{
            $viajeC = $row2['viaje'];
          }
		   if($row2['destino'] =='Multidestino'){
			 $dest =  $row2['destino']."-".$entregar;
		   } else {$dest =  $row2['destino'];}
        ?>
			<td rowspan=3><b><? echo $viajeC; ?></b></td>
			<td style="width:250px;" rowspan=2><b><? echo $cliente; ?></b></td>
			<td style="width:250px;" rowspan=2><b><? echo $nombreE; ?></b></td>
	    	<td style="width:200px;" rowspan=2><b><? echo $dest; ?></b></td>
			<td style="width:100px;" rowspan=2><b><? echo $row2['fecha']; ?></b></td>
			<td style="width:100px;"><b><? echo number_format($row2['PREdiesel'],2); ?></b></td>
			<td style="width:100px;"><b><? echo number_format($row2['PREcasetas'],2); ?></b></td> 
			<td style="width:100px;"><b><? echo number_format($row2['PREviaticos'],2); ?></b></td>
            <td style="width:100px;"><b><? echo number_format($row2['PREtransito'],2); ?></b></td>
			<td style="width:100px;"><b><? echo number_format($row2['PREmaniobras'],2); ?></b></td>
		    <td style="width:100px;"><b><? echo number_format($row2['PREestadias'],2); ?></b></td>
            <td style="width:100px;"><b><? echo number_format($row2['PRErepartos'],2); ?></b></td>
			<td style="width:100px;"><b><? echo number_format($row2['PREmantenimiento'],2); ?></b></td>
			<td style="width:100px;"><b><? echo number_format($row2['PREcomision'],2); ?></b></td>
			  
			  <td rowspan=2><b><? 
			  echo strtoupper($row2['solicita']); ?></b></td>			  
			</tr>
			<!-- ****SEGUNDO RENGLON ***  
			<tr class="treegrid-<?//echo $i;?> treegrid-parent-<?//echo $j;?>"> -->
			<tr>
			  <td  style ='width:160px;' ><input type='number' min='1'  id='tImpDie<? echo $row2['viaje'];?>' on style ='width:50px;' value ='<? echo abs($row2['diesel']);?>'></td>
			  <td style ='width:160px;'><input type='number' min='1' id='tImpCas<? echo $row2['viaje'];?>'  style ='width:50px;' value ='<? echo abs($row2['casetas']);?>'/></td> 
			  <td style ='width:160px;' ><input type='number' min='1' id='tImpAli<? echo $row2['viaje'];?>'  style ='width:50px;' value ='<? echo abs($row2['viaticos']);?>'/></td>
              <td style ='width:160px;'><input type='number' min='1' id='tImpTra<? echo $row2['viaje'];?>'  style ='width:50px;' value ='<? echo abs($row2['transito']);?>'/></td>
		   	  <td style ='width:160px;'><input type='number' min='1' id='tImpMan<? echo $row2['viaje'];?>'  style ='width:50px;' value ='<? echo abs($row2['maniobras']);?>'/></td>   
		 	  <td style ='width:160px;'><input type='number' min='1' id='tImpEst<? echo $row2['viaje'];?>'  style ='width:50px;' value ='<? echo abs($row2['estadias']);?>'/></td>		
			  <td style ='width:160px;'><input type='number' min='1' id='tImpRep<? echo $row2['viaje'];?>'  style ='width:50px;' value ='<? echo abs($row2['repartos']);?>'/></td>		
			  <td style ='width:160px;'><input type='number' min='1' id='tImpMto<? echo $row2['viaje'];?>'  style ='width:50px;' value ='<? echo abs($row2['mantenimiento']);?>'/></td>		
			  <td style ='width:160px;'><input type='number' min='1' id='tImpCom<? echo $row2['viaje'];?>'  style ='width:50px;' value ='<? echo abs($row2['comision']);?>'/></td>		
           <?//if($userL =='dtejeda' || $userL =='rperez' || $userL =='jesus'){?>			 
			 <td><input type='checkbox' title ='Actual' value='"<?echo $row2['viaje'];?>"' id='viaje<? echo $row2['viaje'];?>' id='viaje<? echo $row2['viaje'];?>' onclick="cambReferAdic('<? echo $row2['viaje'];?>','<? echo $row2['id'];?>');" style ='margin-top: 4px;    margin-bottom: 6px;'   />
		     <img src='../imagenes/delete_down.png' width='25' height='17' hspace='5' title='Elimina' onclick="elimina('<? echo $row2['id'];?>')"</td>
		<?//}?>
			</tr>
			<tr>
			  <td  colspan='4' style ='width:160px;' ><b><? echo strtoupper($row2['observacion']);?></b></td>
			 <!-- <?//  if ($impMan >0){ ?>
			  <td style="color:#DEE508;">Fecha</td><td style="color:#DEE508;"><?echo $fecdes;?></td>
        	  <td style="color:#DEE508;">Pago</td><td style="color:#DEE508;"><?echo $pagoDes?></td>
              <td style="color:#DEE508;">Deposito</td><td style="color:#DEE508;"><?echo $impMan?></td -->
			  <?//}?>
		</tr>
		<? 
				 ?>	
 <?  }?>
 	<tr>
		<td colspan='5'></td>
		<td style="color:blue" ><b><? echo number_format($totdie,2); ?></b></td>
		<td style="color:blue"><b><? echo number_format($totcas,2); ?></b></td>
		<td style="color:blue"><b><? echo number_format($totvia,2); ?></b></td>
		<td style="color:blue"><b><? echo number_format($tottra,2); ?></b></td>	
		<td style="color:blue"><b><? echo number_format($totman,2); ?></b></td>	
		<td style="color:blue"><b><? echo number_format($totest,2); ?></b></td>	
		<td style="color:blue"><b><? echo number_format($totrep,2); ?></b></td>
        <td style="color:blue"><b><? echo number_format($totmto,2); ?></b></td>		
		<td style="color:blue"><b><? echo number_format($totcom,2); ?></b></td>	
	</tr>
  </tbody>
</table>
</div>

<hr align='left' noshade='noshade' size='2' width='98%' style='color: #0056b2;margin-bottom:10px;margin-top:10px;height:10px;' />	
	<!---**************************************************-->  
 <?for ($i=1;$i<=55;$i++){?>
  &nbsp;
    <? }?>
    <!--function histo1-->
	<button value="Gastos " ><a href="HistoricoGasto.php">Gastos</a></button>
 <?  for ($i=1;$i<=55;$i++) { ?> &nbsp  <? } ?>
<!--function confirma1-->
<button value="Confirma"><a href="desgloseGastos.php">Confirma</a></button> 
<?
$mysqli = mysqli_init();
$mysqli->options(MYSQLI_OPT_CONNECT_TIMEOUT, 5);
$mysqli->set_charset("utf8");
$enlace = mysqli_connect("db574183143.db.1and1.com", "dbo574183143", "b3lug42015", "db574183143");
//**** QA
$enlace = mysqli_connect("db5000311817.hosting-data.io", "dbu13503", "belugaQA20.", "dbs304381");

$resSdo = mysqli_query($enlace,"Select distinct categoria from  catalSubGastos  where 1  order by categoria");
 while($row3 = mysqli_fetch_assoc($resSdo)){
    $categ= $row3['categoria'];
	$totcat=0;
	$consul="SELECT * from relacionGastos where  status='PorAutoriz' and categoria ='$categ'";
	$queryT = mysqli_query($enlace,$consul);
	if(mysqli_num_rows($queryT)>0){
?>
<!--//******************************************************   -->
<div class="wrapper">
  <h1><a>Desglose </a> <?echo strtoupper($categ);?><span style='font-size:14px; font-weight:bold; color:#0c4a6e;'><?
?>
<?php 

$sub = 0;
$subtot=0;
$i=0;
$totorden=0;
?>
<div id="table-scroll">
				
<div id="cuerpoDatos">

<div id="nofixedX">
<table border ='1' style='width:95%'>
  <thead>
		   <th style="color: blue ;width:50px;"><b>Ident<b></th>
		   <th style="color: blue;width:100px;"><b>Fecha<b></th>
		   <th style="color: blue;width:120px;"><b>Subcateg<b></th>
		   <th style="color: blue;width:110px;"><b>Beneficiario<b></th>
		   <th style="color: blue;width:140px;"><b>Importe<b></th>
		 <? if($categ == 'Mantenimiento') { ?>
		   <th style="color: blue;width:50px;"><b>Unidad<b></th>
		   <th style="color: blue;width:50px;"><b># Pedido<b></th>
			  
		 <? } ?>
		   <th style="color: blue;width:450px;"><b>Observaciones<b></th>
           <th style="color: blue;width:200px;"><b>Solicita<b></th>
		   <th style="color: blue;width:200px;"><b>Comprobado<b></th>
		 <!--  <th style="color: blue;width:150px;"><b>Factura<b></th>
		   <th style="color: blue"><b>Comision<b></th>   Referencia	Factura	-->
		   <th style="color: blue"><b>Actual<b></th>
		</tr>
	   </thead>
<tbody>
		  <?
	$consul="SELECT id,DATE_FORMAT(fecha,'%d/%m/%Y') fecha ,subcateg,cliente,importe,observaciones,solicita,pedido from relacionGastos where  status='PorAutoriz' and categoria ='$categ' order by id asc";
 			
			$res = mysqli_query($enlace,$consul);
		    while($row2=mysqli_fetch_assoc($res)){
			$nomaux=$row2['operador'];
			
			$totcat +=$row2['importe'];
		?> 
  		  	<!--<tr class="treegrid-<?echo $row2['id'];?> treegrid-parent-<?echo $row2['id'];?>">-->
             <tr>			 
			 <td rowspan=1><b><? echo $row2['id']; ?></b></td>
			   <td rowspan=1><b><? echo $row2['fecha']; ?></b></td>
	    	  <td rowspan=1><b><? echo $row2['subcateg']; ?></b></td>
			  <td rowspan=1><b><? echo $row2['cliente']; ?></b></td>
			  <td ><b><? echo number_format($row2['importe'],2); ?></b></td>
			  <? if($categ=='Mantenimiento'){
				  $pedi=$row2['pedido'];
				  
				  $impo=$row2['importe'];
			       $conPed="SELECT unidad from pedidoDetalle where noPedido='$pedi' and (precioU*cantidad) ='$impo' and unidad <> '$uniaux' ";
 			     //  echo $conPed; 
				   $resP = mysqli_query($enlace,$conPed);
					while($rowP=mysqli_fetch_assoc($resP)){
					$uniaux=$rowP['unidad']; 
					}
				   
				   ?>
				  <td ><b><? echo $uniaux; ?></b></td>
				  <td ><b><? echo $row2['pedido']; ?></b></td>
			  <? }?>
			  <td ><b><? echo $row2['observaciones']; ?></b></td> 
				  <td rowspan=1><b><?
			  echo strtoupper($row2['solicita']); ?></b></td>		
			  <td rowspan=1><b><? echo strtoupper($row2['comprobado']); ?></b></td>		
           <?//if($userL =='dtejeda' || $userL =='rperez' || $userL =='jesus'){	   ?>
			  <td><input type='checkbox' title ='Actual' value='<? echo$row2['id'];?>' id='ren<? echo $row2['id'];?>' id='ren<? echo $row2['id'];?>' onclick="cambReferCat('<? echo $row2['id'];?>');" style ='margin-top: 4px;    margin-bottom: 6px;'   /></td>
			  	<? if($categ!='Mantenimiento'){ ?>
				<td><img src='../imagenes/delete_down.png' width='25' height='17' hspace='5' title='Elimina' onclick="eliminaCat('<? echo $row2['id'];?>');"</td>
		   <? }
		   //}?>
			</tr>		
 <?  }?>
 	<tr>
		<td colspan='4'></td>
		<td style="color:blue" ><b><? echo number_format($totcat,2); ?></b></td>
    </tr>

  </tbody>
</table>
</div>
			<?} }?>
	<!----****************** Pendientes ****-->
<div id="dialogPen" title="Gastos x Aplicar " style="display:none;">

<? 
$mysqli = mysqli_init();
$mysqli->options(mysqlI_OPT_CONNECT_TIMEOUT, 5);
$mysqli->set_charset("utf8");
$enlace = mysqli_connect("db574183143.db.1and1.com", "dbo574183143", "b3lug42015", "db574183143");
//**** QA
$enlace = mysqli_connect("db5000311817.hosting-data.io", "dbu13503", "belugaQA20.", "dbs304381");

//echo "SELECT viaje ,sum(PREdiesel) pres, sum(diesel) gtos FROM desgloseGastos where 1 $rango group by viaje";
$solQryAdic = mysqli_query($enlace,"SELECT viaje ,sum(PREdiesel) pres, sum(diesel) gtos FROM desgloseGastos where 1 $rango group by viaje");
$d=0;
$AplDie=0;
$AplCas=0;
$AplAli=0;
$AplEst=0;
$AplMan=0;
$AplTra=0;
$AplMto=0;
$AplRep=0;
$AplVia=0;
$totGtosPen=0;
while($regR=mysqli_fetch_assoc($solQryAdic)){
 
	if ($regR['gtos']  < $regR['pres']){
			$d++;
     $AplDie +=($regR['pres']-  $regR['gtos']);
	 $DieArr[$d]=$regR['viaje'];
	 $DieArr1[$d]=$regR['pres']-  $regR['gtos'];
   }
}
$solQryAdic = mysqli_query($enlace,"SELECT viaje ,sum(PREcasetas) pres, sum(casetas) gtos FROM desgloseGastos where 1 $rango group by viaje");
$c=0;
while($regR=mysqli_fetch_assoc($solQryAdic)){
	if ($regR['gtos']  < $regR['pres']){
		$c++;
       $AplCas +=($regR['pres']-  $regR['gtos']);
	   $CasArr[$c]=$regR['viaje'];
	   $CasArr1[$c]=$regR['pres']-  $regR['gtos'];
	}
}

$solQryAdic = mysqli_query($enlace,"SELECT viaje ,sum(PREalimentos) pres, sum(alimentos) gtos FROM desgloseGastos where 1 $rango group by viaje");
$c=0;
while($regR=mysqli_fetch_assoc($solQryAdic)){
	if ($regR['gtos']  < $regR['pres']){
		$AplAli +=($regR['pres']-  $regR['gtos']);
		$c++;
		$AliArr[$c]=$regR['viaje'];
	    $AliArr1[$c]=$regR['pres']-  $regR['gtos'];}
}
//echo "SELECT (sum(PREestadias) - sum(diesel) ) as adic FROM desgloseGastos where 1 $rango ";
$solQryAdic = mysqli_query($enlace,"SELECT viaje ,sum(PREestadias) pres, sum(estadias) gtos FROM desgloseGastos where 1 $rango group by viaje");
$c=0;
while($regR=mysqli_fetch_assoc($solQryAdic)){
	if ($regR['gtos']  < $regR['pres']){
      $AplEst +=($regR['pres']-  $regR['gtos']);
	   $c++;
		$EstArr[$c]=$regR['viaje'];
	    $EstArr1[$c]=$regR['pres']-  $regR['gtos'];}
}
/*//echo "SELECT (sum(PREcomision) - sum(diesel) ) as adic FROM desgloseGastos where 1 $rango ";
$solQryAdic = mysqli_query($enlace,"SELECT viaje ,sum(PREcomision) pres, sum(comision) gtos FROM desgloseGastos where 1 $rango group by viaje");
$d=0;
while($regR=mysqli_fetch_assoc($solQryAdic)){
	if ($regR['gtos']  < $regR['pres']){
       $AplCom +=($regR['pres']-  $regR['gtos']);
        $c++;
		$ComArr[$d]=$regR['viaje'];
	    $ComArr1[$d]=$regR['pres']-  $regR['gtos'];}
}*/

//echo "SELECT (sum(PREdiesel) - sum(diesel) ) as adic FROM desgloseGastos where 1 $rango ";
$solQryAdic = mysqli_query($enlace,"SELECT viaje ,sum(PREmaniobras) pres, sum(maniobras) gtos FROM desgloseGastos where 1 $rango group by viaje");
$d=0;
while($regR=mysqli_fetch_assoc($solQryAdic)){
	if ($regR['gtos']  < $regR['pres']){
     $AplMan +=($regR['pres']-  $regR['gtos']);
	 $c++;
		$ManArr[$d]=$regR['viaje'];
	    $ManArr1[$d]=$regR['pres']-  $regR['gtos'];}
}
//echo "SELECT (sum(PREdiesel) - sum(diesel) ) as adic FROM desgloseGastos where 1 $rango ";
$solQryAdic = mysqli_query($enlace,"SELECT viaje ,sum(PREtransito) pres, sum(transito) gtos FROM desgloseGastos where 1 $rango group by viaje");
$d=0;
while($regR=mysqli_fetch_assoc($solQryAdic)){
	if ($regR['gtos']  < $regR['pres']){
		$AplTra +=($regR['pres']-  $regR['gtos']);
		$c++;
		$TraArr[$d]=$regR['viaje'];
	    $TraArr1[$d]=$regR['pres']-  $regR['gtos'];}
}
//echo "SELECT (sum(PREdiesel) - sum(diesel) ) as adic FROM desgloseGastos where 1 $rango ";
$solQryAdic = mysqli_query($enlace,"SELECT viaje ,sum(PREmantenimiento) pres, sum(mantenimiento) gtos FROM desgloseGastos where 1 $rango group by viaje");
$d=0;
while($regR=mysqli_fetch_assoc($solQryAdic)){
	if ($regR['gtos']  < $regR['pres']){
	  $AplMto +=($regR['pres']-  $regR['gtos']);
      $c++;
	  $MtoArr[$d]=$regR['viaje'];
	  $MtoArr1[$d]=$regR['pres']-  $regR['gtos'];}
}
//echo "SELECT (sum(PREdiesel) - sum(diesel) ) as adic FROM desgloseGastos where 1 $rango ";
$solQryAdic = mysqli_query($enlace,"SELECT viaje ,sum(PRErepartos) pres, sum(repartos) gtos FROM desgloseGastos where 1 $rango group by viaje");
$d=0;
while($regR=mysqli_fetch_assoc($solQryAdic)){
	if ($regR['gtos']  < $regR['pres']){
	  $AplRep +=($regR['pres']-  $regR['gtos']);
      $c++;
	  $RepArr[$d]=$regR['viaje'];
	  $RepArr1[$d]=$regR['pres']-  $regR['gtos'];}
}
//echo "SELECT viaje ,sum(PREviaticos) pres, sum(viaticos) gtos FROM desgloseGastos where 1 $rango group by viaje";
$solQryAdic = mysqli_query($enlace,"SELECT viaje ,sum(PREviaticos) pres, sum(viaticos) gtos FROM desgloseGastos where 1 $rango group by viaje");
$d=0;
while($regR=mysqli_fetch_assoc($solQryAdic)){
	if ($regR['gtos']  < $regR['pres']){
	  $AplVia +=($regR['pres']-  $regR['gtos']);
      $c++;
	  $ViaArr[$d]=$regR['viaje'];
	  $ViaArr1[$d]=$regR['pres']-  $regR['gtos'];}
}
?>
	  <table   border="1"  align="left"  style=" width:25px; margin-top:-1px" >
    <?   if ($AplDie > 0){ 
	        $totGtosPen+=$AplDie?>
	      <tr>
			<td  align="center"><span style="font-size:13px; font-weight:bold;color:blue">Diesel</td>
			 <td  align="center"><? echo "<span style='font-size:13px; font-weight:bold; '>".count($DieArr)."</span>";?> </td>
		    <td  align="center"><? echo "<span style='font-size:13px; font-weight:bold; '>".number_format(abs($AplDie),2)."</span>";?> </td>
		   </tr>
	<?   }
	          if ($AplCas > 0){
          $totGtosPen+=$AplCas	  ?>
	      <tr>
			<td  align="center"><span style="font-size:13px; font-weight:bold;color:blue">Casetas</td>
			 <td  align="center"><? echo "<span style='font-size:13px; font-weight:bold; '>".count($CasArr)."</span>";?> </td>
		    <td  align="center"><? echo "<span style='font-size:13px; font-weight:bold; '>".number_format(abs($AplCas),2)."</span>";?> </td>
		   </tr>
	 <?  }
      if ($AplAli > 0){ 
			  $totGtosPen+=$AplAli?>
	      <tr>
			<td  align="center"><span style="font-size:13px; font-weight:bold;color:blue">Alimentos</td>
			<td  align="center"><? echo "<span style='font-size:13px; font-weight:bold; '>".count($AliArr)."</span>";?> </td>
		    <td  align="center"><? echo "<span style='font-size:13px; font-weight:bold; '>".number_format(abs($AplAli),2)."</span>";?> </td>
		   </tr>
	 <?  }
	          if ($AplCom > 0){ 
			  $totGtosPen+=$AplCom?>
	      <tr>
			<td  align="center"><span style="font-size:13px; font-weight:bold;color:blue">Comision</td>
			<td  align="center"><? echo "<span style='font-size:13px; font-weight:bold; '>".count($ComArr)."</span>";?> </td>
		    <td  align="center"><? echo "<span style='font-size:13px; font-weight:bold; '>".number_format(abs($AplCom),2)."</span>";?> </td>
		   </tr>
	 <?  }
	          if ($AplEst > 0){ 
			  $totGtosPen+=$AplEst?>
	      <tr>
			<td  align="center"><span style="font-size:13px; font-weight:bold;color:blue">Estadias</td>
			<td  align="center"><? echo "<span style='font-size:13px; font-weight:bold; '>".count($EstArr)."</span>";?> </td>
		    <td  align="center"><? echo "<span style='font-size:13px; font-weight:bold; '>".number_format(abs($AplEst),2)."</span>";?> </td>
		   </tr>
	 <?  }
	          if ($AplMan > 0){
           $totGtosPen+=$AplMan  ?>
	      <tr>
			<td  align="center"><span style="font-size:13px; font-weight:bold;color:blue">Maniobras</td> 
			<td  align="center"><? echo "<span style='font-size:13px; font-weight:bold; '>".count($ManArr)."</span>";?> </td>
		    <td  align="center"><?echo "<span style='font-size:13px; font-weight:bold; '>" .number_format(abs($AplMan),2)."</span>";?> </td>
		   </tr>
	  <? }
	          if ($AplTra > 0){ 
			 $totGtosPen+=$AplTra ?>
	      <tr>
			<td  align="center"><span style="font-size:13px; font-weight:bold;color:blue">Transito</td>
			<td  align="center"><? echo "<span style='font-size:13px; font-weight:bold; '>".count($TraArr)."</span>";?> </td>
		    <td  align="center"><? echo "<span style='font-size:13px; font-weight:bold; '>".number_format(abs($AplTra),2)."</span>";?> </td>
		   </tr>
	<?   }
	          if ($AplMto > 0){ 
			  $totGtosPen+=$AplMto?>
	      <tr>
			<td  align="center"><span style="font-size:13px; font-weight:bold;color:blue">Mantenimiento</td>
			<td  align="center"><? echo "<span style='font-size:13px; font-weight:bold; '>".count($MtoArr)."</span>";?> </td>
		    <td  align="center"><? echo "<span style='font-size:10px; font-weight:bold; '>".number_format(abs($AplMto),2)."</span>";?> </td>
		   </tr>
	 <?  }
     if ($AplRep > 0){ 
			  $totGtosPen+=$AplRep?>
	      <tr>
			<td  align="center"><span style="font-size:13px; font-weight:bold;color:blue">Repartos</td>
			<td  align="center"><? echo "<span style='font-size:13px; font-weight:bold; '>".count($RepArr)."</span>";?> </td>
		    <td  align="center"><? echo "<span style='font-size:10px; font-weight:bold; '>".number_format(abs($AplRep),2)."</span>";?> </td>
		   </tr>
	 <?  } 
     if ($AplVia > 0){ 
			  $totGtosPen+=$AplVia?>
	      <tr>
			<td  align="center"><span style="font-size:13px; font-weight:bold;color:blue">Viaticos</td>
			<td  align="center"><? echo "<span style='font-size:13px; font-weight:bold; '>".count($ViaArr)."</span>";?> </td>
		    <td  align="center"><? echo "<span style='font-size:10px; font-weight:bold; '>".number_format(abs($AplVia),2)."</span>";?> </td>
		   </tr>
	 <?  }	 
	 
	   $TotPen =count($DieArr)+count($CasArr)+count($AliArr)+count($ManArr)+count($TraArr)+count($MtoArr)+count($ComArr)+count($RepArr)+count($ViaArr);
	 ?>
	        
	      <tr>
			<td  align="center"><span style="color:blue"></td>
			<td  align="center"><? echo "<span style='font-size:13px; font-weight:bold; '>".$TotPen."</span>";?> </td>
		    <td  align="center"><? echo "<span style='font-size:12px; font-weight:bold; '>".number_format(abs($totGtosPen),2)."</span>";?> </td>
		   </tr>
	</table>
	
	  
	  <?if( count($DieArr) > 0){?>
	    <table>
		  <tr><td align='center' colspan ='2' style='font-size:14px; font-weight:bold; '><b><u>Diesel</u></b></td></tr>
	 <? for ($i=1;$i<=count($DieArr);$i++){  ?> 

	    <tr>
	      <td align='center' style='font-size:14px; font-weight:bold; '><?echo $DieArr[$i];?></td>
		  <td align='center' style='font-size:14px; font-weight:bold; '><?echo number_format($DieArr1[$i],2);?></td>
		  <td><input type='checkbox' id='viaje<? echo $DieArr[$i];?>' onclick="activaSdo('<? echo $DieArr[$i];?>',1);" style ='margin-top: 4px;    margin-bottom: 6px;'   /></td>
	 
	 </tr> 
	  <? }
	?></table>
	<hr style='border:5px;'>
	<?}?> 

	  <?  if( $CasArr >0){?>
   	   <table> 
		   <tr>  <td align='center' colspan ='2' style='font-size:14px; font-weight:bold; '><b><u>Casetas</u></b></td></tr>
	  <?for ($i=1;$i<=count($CasArr);$i++){  ?> 
        <tr>
	      <td align='center' style='font-size:14px; font-weight:bold; '><?echo $CasArr[$i];?></td>
		  <td align='center' style='font-size:14px; font-weight:bold; '><?echo number_format($CasArr1[$i],2);?></td>
          <td><input type='checkbox' id='viaje<? echo $CasArr[$i];?>' onclick="activaSdo('<? echo $CasArr[$i];?>',2);" style ='margin-top: 4px;    margin-bottom: 6px;'   /></td>	  
	  </tr> 
	  <? }
	?></table>
	<hr style='border:5px;'>
	<?  }?> 

	 
	  <?if( count($AliArr)> 0){?>
	  <table>
		  <tr><td align='center' colspan ='2' style='font-size:14px; font-weight:bold; '><b><u>Alimentos</u></b></td></tr>
	  <?for ($i=1;$i<=count($AliArr);$i++){  ?> 

	    <tr>
	      <td align='center' style='font-size:14px; font-weight:bold; '><?echo $AliArr[$i];?></td>
		  <td align='center' style='font-size:14px; font-weight:bold; '><?echo number_format($AliArr1[$i],2);?></td>
          <td><input type='checkbox'  id='viaje<? echo $AliArr[$i];?>' onclick="activaSdo('<? echo $AliArr[$i];?>',3);" style ='margin-top: 4px;    margin-bottom: 6px;'   /></td>	  	 
	 </tr> 
	  <? }
    ?></table>
	  <hr style='border:5px;'>

	  <?}?> 
		 
	  <? if( count($TraArr) >0){?>
	  <table>
		   <tr><td align='center' colspan ='2' style='font-size:14px; font-weight:bold; '><b><u>Transito</u></b></td></tr>
		<?  for ($i=1;$i<=count($TraArr);$i++){ ?>
	 

	    <tr>
	      <td><?echo $TraArr[$i];?></td>
		  <td><?echo number_format($TraArr1[$i],2);?></td>
		  <td><input type='checkbox' id='viaje<? echo $TraArr[$i];?>' onclick="activaSdo('<? echo $TraArr[$i];?>',4);" style ='margin-top: 4px;  margin-bottom: 6px;'   /></td>	  
	  </tr> 
	  <? }  
	?></table>
	<hr style='border:5px;'>
	    <?}?> 
	 
	  <?if( count($MtoArr)  >0){?>
	    <table> 
		<tr> <td align='center' colspan ='2'style='font-size:14px; font-weight:bold; '><b><u>Mantenimiento</u></b></td></tr>
	  <?for ($i=1;$i<=count($MtoArr);$i++){ 	  ?> 

	    <tr>
	      <td style='font-size:14px; font-weight:bold; '><?echo $MtoArr[$i];?></td>
		  <td style='font-size:14px; font-weight:bold; '><?echo number_format($MtoArr1[$i],2);?></td>
		  <td><input type='checkbox' id='viaje<? echo $MtoArr[$i];?>' onclick="activaSdo('<? echo $MtoArr[$i];?>',5);" style ='margin-top: 4px;  margin-bottom: 6px;' /></td>	  
	  </tr> 
	  <? }
	?></table>
	<hr style='border:5px;'>
	  <?}?> 

	  <?if( count($ComArr)>0){?>
	 <table>
      <tr><td align='center' colspan ='2'style='font-size:14px; font-weight:bold; '><b><u>Comision</u></b></td></tr>
	  <?for ($i=1;$i<= count($ComArr);$i++){ ?> 
	    <tr>
	      <td style='font-size:14px; font-weight:bold; '><?echo $ComArr[$i];?></td>
		  <td style='font-size:14px; font-weight:bold; '><?echo number_format($ComArr1[$i],2);?></td>
	     <td><input type='checkbox' id='viaje<? echo $ComArr[$i];?>' onclick="activaSdo('<? echo $ComArr[$i];?>',6);" style ='margin-top: 4px;  margin-bottom: 6px;'   /></td>	  
	  </tr> 
	  <? }
	?></table>
	<hr style='border:5px;'>
	 <? }?> 
	  <?if( count($EstArr) > 0){?>
	 <table>
       <tr><td align='center' colspan ='2'style='font-size:14px; font-weight:bold; '><b><u>Estadias</u></b></td></tr>
	  <?for ($i=1;$i<=count($EstArr);$i++){  ?> 
         <tr>
	      <td style='font-size:14px; font-weight:bold; '><?echo $EstArr[$i];?></td>
		  <td style='font-size:14px; font-weight:bold; '><?echo number_format($EstArr1[$i],2);?></td>
		  <td><input type='checkbox' id='viaje<? echo $EstArr[$i];?>' onclick="activaSdo('<? echo $EstArr[$i];?>',7);" style ='margin-top: 4px;  margin-bottom: 6px;'   /></td>	  	 
	 </tr> 
	  <? }
	?></table>
	<hr style='border:5px;'>
	 <? }?> 
	 
	  <? if( count($ManArr) >0){ ?>
	 <table> 
	  <tr> <td align='center' colspan ='2'style='font-size:14px; font-weight:bold; '>Maniobras</td></tr>
	  <?for ($i=1;$i<=count($ManArr);$i++){ ?> 

	    <tr>
	      <td style='font-size:14px; font-weight:bold; '><?echo $ManArr[$i];?></td>
		  <td style='font-size:14px; font-weight:bold; '><?echo number_format($ManArr1[$i],2);?></td>
	  	  <td><input type='checkbox' id='viaje<? echo $ManArr[$i];?>' onclick="activaSdo('<? echo $ManArr[$i];?>',8);" style ='margin-top: 4px;  margin-bottom: 6px;'   /></td>	  
	  </tr> 
	  <? }
    ?></table>
	<hr style='border:5px;'>
	 <?   }?> 
	 <? if( count($RepArr) >0){ ?>
	  <table>

	  <tr> <td align='center' colspan ='2'style='font-size:14px; font-weight:bold; '>Repartos</td></tr>
	  <?for ($i=1;$i<=count($RepArr);$i++){ ?> 

	    <tr>
	      <td style='font-size:14px; font-weight:bold; '><?echo $RepArr[$i];?></td>
		  <td style='font-size:14px; font-weight:bold; '><?echo number_format($RepArr1[$i],2);?></td>
	  	  <td><input type='checkbox' id='viaje<? echo $RepArr[$i];?>' onclick="activaSdo('<? echo $RepArr[$i];?>',9);" style ='margin-top: 4px;  margin-bottom: 6px;'   /></td>	  
	  </tr> 
	  <? }
	?></table>
	<hr style='border:5px;'>
	  
	  <?}?> 
	 
	  <? if( count($ViaArr) >0){ ?>
	  <table>
	  <tr> <td align='center' colspan ='2'style='font-size:14px; font-weight:bold; '>Viaticos</td></tr>
	  <?for ($i=1;$i<=count($ViaArr);$i++){ ?> 

	    <tr>
	      <td style='font-size:14px; font-weight:bold; '><?echo $ViaArr[$i];?></td>
		  <td style='font-size:14px; font-weight:bold; '><?echo number_format($ViaArr1[$i],2);?></td>
		  <td><input type='checkbox' id='viaje<? echo $ViaArr[$i];?>' onclick="activaSdo('<? echo $ViaArr[$i];?>',9);" style ='margin-top: 4px;  margin-bottom: 6px;'   /></td>	  	 
	 </tr> 
	  <? } 
		?></table> 
	  <<?}?> 
</div>
		
<!----****************** TIPO GASTO ****-->
<div id="dialogGpo" title="Grupos " style="display:none;">

<? mysqli_connect("db574183143.db.1and1.com", "dbo574183143", "b3lug42015") or die("NO se pudo realizar la conexion");
mysqli_select_db("db574183143");
//$rango="and abs(viaje) BETWEEN '8500' AND '10325'";
//echo "Aplicar -SELECT viaje ,sum(PREdiesel) pres, sum(diesel) gtos FROM desgloseGastos where 1 $rango group by viaje";
?>
 <table   border="2"  align="left"  style=" width:450px; margin-top:-1px" >
  <tr>
	<td align="center"><span style="font-size:13px; font-weight:bold;color:blue">Grupo</td>
	<td align="center"><span style="font-size:13px; font-weight:bold;color:blue">Kms Ini</td>
	<td align="center"><span style="font-size:13px; font-weight:bold;color:blue">Kms Fin</td>
	<td align="center"><span style="font-size:13px; font-weight:bold;color:blue"># Dias</td>
	<td align="center"><span style="font-size:13px; font-weight:bold;color:blue">$ Viaticos</td>
	<td align="center"><span style="font-size:13px; font-weight:bold;color:blue">% Gastos</td>
  </tr>
	<?   
$solQryAdic = mysqli_query($enlace," select *  FROM tipoViajeAct where 1");
while($regR=mysqli_fetch_assoc($solQryAdic)){ ?>
<tr>
	    <td><b> <?echo $regR['descripcion'];?></b></td> 
		<td><b> <? echo number_format($regR['kmsIni'],0);?></b> </td>
		<td><b> <? echo number_format($regR['kmsFin'],0);?></b> </td>
		<td><b> <? echo $regR['numDias'];?></b> </td>
		<td><b> <? echo number_format($regR['viaticos'],2);?></b> </td>
		<td><b> <? echo $regR['porcGto'];?></b> </td>
</tr>
<?
}

?>
</table>
	<hr style='border:5px;'>	
</div>
<!----****************** COMIS ****-->
<div id="dialogCom" title="Comisiones " style="display:none;">
<form id="formaCom" name="formaCom" method='get' action='actuaCom.php'>
<? mysqli_connect("db574183143.db.1and1.com", "dbo574183143", "b3lug42015") or die("NO se pudo realizar la conexion");
mysqli_select_db("db574183143");

//$enlace = mysqli_connect("db5000311817.hosting-data.io", "dbu13503", "belugaQA20.", "dbs304381");

//echo "SELECT  sum(comision) as monto  FROM  desgloseGastos where fecha  BETWEEN '$fecini' AND '$fecfin'  ";
   $ComSem = mysqli_query($enlace,"SELECT  sum(PREcomision-comision) as monto  FROM  desgloseGastosAut where 1 ");
while($rowC=mysqli_fetch_assoc($ComSem)){
  $mtoCom=$rowC['monto'];
}

?> 
<p> Comisiones x Autorizar $: <span style="color:blue"> <?echo number_format($mtoCom,2); ?></span> </p>  

  <table   border="0"  align="left"  style=" width:250px; margin-top:-1px" >
  <tr>
     <td><span style="color:blue">Operador</span></td>
	 <td><span style="color:blue">Monto</span></td>
 </tr>
  
<?
//echo "SELECT operador,sum(PREcomision-comision) as monto FROM desgloseGastosAut where 1 and  PREcomision <> 0  group by operador";
 $ComSem1 = mysqli_query($enlace,"SELECT operador,sum(PREcomision-comision) as monto FROM desgloseGastosAut where 1 and  PREcomision <> 0  group by operador");

 while($arr_asoc = mysqli_fetch_assoc($ComSem1))
     {
     if ($arr_asoc['monto'] > 0) {			   
	   ?>
	   
		  <tr>
			<td><strong><? echo $arr_asoc['operador']; ?></strong></td>	
			<td><strong><? echo number_format($arr_asoc['monto'],2); ?></strong></td>	
			  </tr>
	 <?} 
		} ?>
  </tr>
</table>
</div>				
	<!---**************************************************-->  

	<script>
function muestUti(a,b,c)
{ 
  swal("Precio:"+a+" /Gastos:"+b,"% Gastos:"+c, "success");  
}
</script>
<script>
function muestUtiM(a,b,c)
{ 
/*alert(a);
var num = a.replace(/\./g,'');
alert(num);
if(!isNaN(num)){
num = num.toString().split('').reverse().join('').replace(/(?=\d*\.?)(\d{3})/g,'$1.');
num = num.split('').reverse().join('').replace(/^[\.]/,'');
a.value = num;
*/
//}
//alert(a);

 swal("Precio:"+a+" / Gastos:"+b, "% Gasto:"+c, "warning");  
}
</script>
<script language="JavaScript">
<!--
var era;
var previo=null;
function uncheckRadio(rbutton){
if(previo &&previo!=rbutton){previo.era=false;}
if(rbutton.checked==true && rbutton.era==true){rbutton.checked=false;}
rbutton.era=rbutton.checked;
previo=rbutton;
}
//-->
</script>	

<script>
function cambRefer(a,b) 
{
//	alert("cambrefere");
var sol =a;
var id =b;
 var check='viaje'+sol;

 var inpDie='tImpDie'+sol;
 var inpCas='tImpCas'+sol;
 var inpAli='tImpAli'+sol;
 var inpTra='tImpTra'+sol;
 var inpMan='tImpMan'+sol;
 var inpCom='tImpCom'+sol;
 

// if (document.getElementById(inpDie).value =='' && document.getElementById(inpCas).value =='' 
// && document.getElementById(inpAli).value ==''  && document.getElementById(inpTra).value ==''
// && document.getElementById(inpMan).value ==''){
//	 document.getElementById(check).checked=0 ;
//   alert("La Autorizacion  de los Gastos no Puede in en Cero!!!!!");	
//return false;
 //}
//if (document.getElementById(inpDie).value ==0 && document.getElementById(inpCas).value ==0
//&& document.getElementById(inpAli).value ==0 && document.getElementById(inpTra).value ==0 
//&& document.getElementById(inpMan).value ==0){
//   alert("La Autorizacion   de los Gastos no Puede in en Cero!!!!!");	
//return false; 
 //} 
 
 var depDie=document.getElementById(inpDie).value;
 var depCas=document.getElementById(inpCas).value;
 var depAli=document.getElementById(inpAli).value;

 var depTra=document.getElementById(inpTra).value;
 var depMan=document.getElementById(inpMan).value;
  var depCom=document.getElementById(inpCom).value;
 //alert("voy a ejecutar");
//alert('ac_desglose2612.php?ident='+sol+'&diesel='+Opdie+'&caseta='+Opcas+'&alimen='+Opali+'&transi='+Optra+'&manio='+Opman
//+'&comis='+Opcom+'&reparto='+Oprep+'&estadia='+Opest+'&Depdiesel='+depDie+'&Depcaseta='+depCas
//+'&Depalimen='+depAli+'&Deptransi='+depTra+'&Depmanio='+depMan
//+'&Depreparto='+depRep+'&Depestad='+depEst+'&Foldiesel='+folDie+'&Folcaseta='+folCas
//+'&Folalimen='+folAli+'&Foltransi='+folTra+'&Folmanio='+folMan+'&Folreparto='+folRep+'&Folestad='+folEst); 
 window.location.href ='ac_desglose.php?identAut='+sol+'&Depdiesel='+depDie+'&Depcaseta='+depCas
+'&Depalimen='+depAli+'&Deptransi='+depTra+'&Depmanio='+depMan+'&Depcomis='+depCom+'&IDAUX='+id 

 
 if (window.XMLHttpRequest) {
            // code for IE7+, Firefox, Chrome, Opera, Safari
            xmlhttp = new XMLHttpRequest();
        } else {
            // code for IE6, IE5
            xmlhttp = new ActiveXObject("Microsoft.XMLHTTP");
        }
        xmlhttp.onreadystatechange = function() {
            if (this.readyState == 4 && this.status == 200) {
                document.getElementById("txtHint").innerHTML = this.responseText;
            }
  };
  xmlhttp.open("GET",'ac_desglose2612.php?ident='+sol+'&diesel='+Opdie+'&caseta='+Opcas+'&alimen='+Opali+'&transi='+Optra+'&manio='+Opman
+'&comis='+Opcom+'&Depdiesel='+depDie+'&Depcaseta='+depCas+'&Depalimen='+depAli+'&Deptransi='+depTra
+'&Depmanio='+depMan+'&Foldiesel='+folDie+'&Folcaseta='+folCas+'&Folalimen='+folAli+'&Foltransi='+folTra+'&Folmanio='+folMan ,true);
    //   xmlhttp.send();
	
}
</script>    
<script>
function elimina(a)
{  
var	numreg =a;
window.location.href = 'ac_desglose.php?nreg='+numreg;
}
</script> 
<script>
function eliminaCat(a)
{  
var	numreg =a;
window.location.href = 'ac_desglose.php?nregCat='+numreg;
}
</script> 
<!--*********** -->
<script>
function cambReferAdic(a,b) 
{

var sol =a;
var idAux =b;
//***********
 var check='viaje'+sol;

 var inpDie='tImpDie'+sol;
 var inpCas='tImpCas'+sol;
 var inpAli='tImpAli'+sol;
 var inpTra='tImpTra'+sol;
 var inpMan='tImpMan'+sol;
 var inpMto='tImpMto'+sol;
 var inpRep='tImpRep'+sol;
 var inpEst='tImpEst'+sol;
 var inpCom='tImpCom'+sol;

 if (document.getElementById(inpDie).value =='' && document.getElementById(inpCas).value =='' && document.getElementById(inpAli).value ==''  
 && document.getElementById(inpTra).value ==''  && document.getElementById(inpRep).value ==''  && document.getElementById(inpMan).value ==''
 && document.getElementById(inpEst).value =='' && document.getElementById(inpMto).value =='' && document.getElementById(inpCom).value ==''){
  document.getElementById(check).checked=0 ;
  alert("La Autorizacion  de los Gastos no Puede in en Cero!!!!!");	
return false;
 }

if (document.getElementById(inpDie).value ==0 && document.getElementById(inpCas).value ==0 && document.getElementById(inpAli).value ==0  
 && document.getElementById(inpTra).value ==0  && document.getElementById(inpRep).value ==0  && document.getElementById(inpMan).value ==0
 && document.getElementById(inpEst).value ==0 && document.getElementById(inpMto).value ==0 && document.getElementById(inpCom).value ==0){
  document.getElementById(check).checked=0 ;
  alert("La Autorizacion  de los Gastos no Puede in en Cero!!!!!");	
return false;
 }

 var depDie=document.getElementById(inpDie).value;
 var depCas=document.getElementById(inpCas).value;
 var depAli=document.getElementById(inpAli).value;
 var depTra=document.getElementById(inpTra).value;
 var depEst=document.getElementById(inpEst).value;
 var depRep=document.getElementById(inpRep).value;
 var depMan=document.getElementById(inpMan).value;

var depMto=document.getElementById(inpMto).value;
 var depCom=document.getElementById(inpCom).value;

 window.location.href ='ac_desglose.php?identAdicAut='+sol+'&Depdiesel='+depDie+'&Depcaseta='+depCas+'&DepCom='+depCom
+'&Depalimen='+depAli+'&Deptransi='+depTra+'&Depmanio='+depMan+'&Depreparto='+depRep+'&Depestad='+depEst+'&Depmanto='+depMto+'&idAux='+idAux; 

 if (window.XMLHttpRequest) {
            // code for IE7+, Firefox, Chrome, Opera, Safari
            xmlhttp = new XMLHttpRequest();
        } else {
            // code for IE6, IE5
            xmlhttp = new ActiveXObject("Microsoft.XMLHTTP");
        }
        xmlhttp.onreadystatechange = function() {
            if (this.readyState == 4 && this.status == 200) {
                document.getElementById("txtHint").innerHTML = this.responseText;
            }
  };
  xmlhttp.open("GET",'ac_desglose2612.php?identAdic='+sol+'&diesel='+Opdie+'&caseta='+Opcas+
  '&alimen='+Opali+'&transi='+Optra+'&manio='+Opman+'&comis='+Opcom+'&reparto='+Oprep+'&estadia='+OpEst+'&Depdiesel='+depDie+
  '&Depcaseta='+depCas+'&Depalimen='+depAli+'&Deptransi='+depTra+'&Depmanio='+depMan+
  '&Depreparto='+depRep+'&Depestad='+depEst+'&Foldiesel='+folDie+'&Folcaseta='+folCas+
  '&Folalimen='+folAli+'&Foltransi='+folTra+'&Folmanio='+folMan+'&Folreparto='+folRep+'&Folestad='+folEst,true);
    //   xmlhttp.send();*/
}
</script>    
<script>
function activaSdo(a,b) 
{
var viaje =a;
var id =b
if (id ==1) {
    var tipo='diesel';
}
if (id ==2) { 
    var tipo='casetas';
}
if (id ==3) {
    var tipo='alimentos';
}
 if (id ==4) {
    var tipo='transito';
 } 
if (id ==5) {
    var tipo='mantenimiento';
}	
if (id ==6) {
    var tipo='comision';
}
if (id ==7) {
    var tipo='estadias';
}
if (id ==8) {
    $tipo='maniobras';
}
if (id ==9) {
  var tipo='repartos';
  }

 window.location.href =  'ac_desglose.php?viaje='+viaje+'&tipo='+tipo;

}
</script>    

<!--*********** -->
<script>
function valida(a,b,c) 
{
var presup =b;
var sol =a;
var id =c

 var inpDie='tImpDie'+sol;
 var inpCas='tImpCas'+sol;
 var inpAli='tImpAli'+sol;
 var inpTra='tImpTra'+sol;
 var inpMan='tImpMan'+sol;
 var inpRep='tImpRep'+sol;
 var inpEst='tImpEst'+sol;
 var inpCom='tImpCom'+sol;

  if(document.getElementById(inpDie).value > 0 && id==1){
	  if (document.getElementById(inpDie).value > presup ){
	     alert("El Monto no puede ser Mayor al Presupuesto ");
		 document.getElementById(inpDie).value ='';
		 return false;
	  }	  
  }
 
  if(document.getElementById(inpCas).value > 0 && id==2){
	  if (document.getElementById(inpCas).value > presup ){
	     alert("El Monto no puede ser Mayor al Presupuesto ");
		 document.getElementById(inpCas).value='';
		 return false;
	  }	  
  }
  if(document.getElementById(inpAli).value > 0 && id==3){
	  if (document.getElementById(inpAli).value > presup ){
	     alert("El Monto no puede ser Mayor al Presupuesto ");
		 document.getElementById(inpAli).value='';
		 return false;
	  }	  
  }
 if(document.getElementById(inpTra).value > 0 && id==4){
	  if (document.getElementById(inpTra).value > presup ){
	     alert("El Monto no puede ser Mayor al Presupuesto ");
		 document.getElementById(inpTra).value='';
		 return false;
	  }	  
  }
 if(document.getElementById(inpMan).value > 0 && id==5){
	  if (document.getElementById(inpMan).value > presup ){
	     alert("El Monto no puede ser Mayor al Presupuesto ");
		 document.getElementById(inpMan).value='';
		 return false;
	  }	  
  }
 if(document.getElementById(inpCom).value > 0 && id==6){
	  if (document.getElementById(inpCom).value > presup ){
	     alert("El Monto no puede ser Mayor al Presupuesto ");
		 document.getElementById(inpCom).value='';
		 return false;
	  }	  
  }
}
</script>    
<!--//***************************************************************** -->
<!--*********** -->
<script>
function cambReferCat(a) 
{

var sol =a;

 window.location.href =  'ac_desglose.php?catIdAut='+sol;
/*
 if (window.XMLHttpRequest) {
            // code for IE7+, Firefox, Chrome, Opera, Safari
            xmlhttp = new XMLHttpRequest();
        } else {
            // code for IE6, IE5
            xmlhttp = new ActiveXObject("Microsoft.XMLHTTP");
        }
        xmlhttp.onreadystatechange = function() {
            if (this.readyState == 4 && this.status == 200) {
                document.getElementById("txtHint").innerHTML = this.responseText;
            }
  };
 //alert("ac_desglose.php?ident='+sol+'&diesel='+Opdie+'&caseta='+Opcas+'&alimen='+Opali+'&transi='+Optra+'&comis='+Opcom+'&Depdiesel='+depDie+'&Depcaseta='+depCas+'&Depalimen='+depAli+'&Deptransi='+depTra+'&Foldiesel='+folDie+'&Folcaseta='+folCas+'&FOLalimen='+folAli+'&Foltransi='+folTra	);
  xmlhttp.open("GET",'ac_desglose.php?catId='+sol+'&tipoCat='+tipo+'&refeCat='+refCat+'&factCat='+facCat,true);
       xmlhttp.send();
 */
}
</script>    



<div id="dialog" title="Solicitud" style="display:none;">

<? $mysqli = mysqli_init();
$mysqli->options(mysqlI_OPT_CONNECT_TIMEOUT, 5);
$mysqli->set_charset("utf8");
$enlace = mysqli_connect("db574183143.db.1and1.com", "dbo574183143", "b3lug42015", "db574183143");
//**** QA
$enlace = mysqli_connect("db5000311817.hosting-data.io", "dbu13503", "belugaQA20.", "dbs304381");

  $ssql="select * from empleados  where puesto <> 'Administrativo' and activo ='Si' order by nombre_ref";
        $query = mysql_query($ssql);
        $i=0;
        while($arr_asoc = mysql_fetch_array($query))
           {
            $emp1[]=$arr_asoc['nombre_ref'];
            $emp2[]=$arr_asoc['id'];
            $i=$i+1;
           }

?>
	<p> Operador:<select style=' border:solid 1px #582b9a; border-radius:5px;' name='conductor' id='conductor'> 
			   <option selected value=''></option>
               <?  for ($x=0; $x<$i; $x++){
		            echo" <option value='$emp1[$x]'>$emp1[$x]</option>";
                    }
?>			
	</select>
	
	</p>
	<p> Importe:
	 <input type ='number' name='importe' id='importe'> 
</p>  
	<p> Observaciones :
	<textarea name='obser' id='obser' cols='30' rows='2'></textarea></p>
	
   <!--// <input type="hidden"  name="modUniR" id="modUniR" value="<?=$idCamion;?>" />
	 <input type="hidden"  name="numsolR" id="numsolR" value="<?=$numsol;?>" />-->
	<input type="button"  value="Guardar" onClick="graba();"/>
</div>

<div  id="dial"  style="display:none;border:groove;width:700px;margin_top:300px; " >
<div style="background-color:#3399FF" class="title" align="center"><font color='white' size= '4'  face='Tahoma'>Solicitud Nueva</font>&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;
<img src="../imagenes/btn_close.png" border="1" align="rigth" onClick="ocultar()" ></div>
<form name="formS" id="formS" method='get' action='ac_actSolicitud.php'>

<? 
$fecha=date("Y-m-d");
$mysqli = mysqli_init();
$mysqli->options(mysqlI_OPT_CONNECT_TIMEOUT, 5);
$mysqli->set_charset("utf8");
$enlace = mysqli_connect("db574183143.db.1and1.com", "dbo574183143", "b3lug42015", "db574183143");
//**** QA
$enlace = mysqli_connect("db5000311817.hosting-data.io", "dbu13503", "belugaQA20.", "dbs304381");

  $ssql="select * from catalSubcateg where Pagar ='S' order by descripcion";
        $query = mysql_query($ssql);
        $i=0;
        while($arr_asoc = mysql_fetch_array($query))
           {
            $cat1[]=$arr_asoc['descripcion'];
            $cat2[]=$arr_asoc['valor'];
			$cat3[]=$arr_asoc['categoria'];
            $i=$i+1;
           }
		   
		   
	  $ssql="select * from empleados  where puesto <> 'Administrativo' and activo ='Si' order by nombre_ref";
        $query = mysql_query($ssql);
        $i=0;
        while($arr_asoc = mysql_fetch_array($query))
           {
            $emp1[]=$arr_asoc['nombre_ref'];
            $emp2[]=$arr_asoc['id'];
            $i=$i+1;
           }	   
		   

?>
	<p> Motivo :&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;<select style=' border:solid 1px #582b9a; border-radius:5px;' name='categoria' id='categoria'> 
			   <option selected value=''></option>
               <?  for ($x=0; $x<$i; $x++){
		            echo" <option value='$cat2[$x]'><b>$cat3[$x]</b>/$cat1[$x]</option>";
                    }
?>			
	</select>	</p>

    <p> Beneficiario :&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;
	 <input type ='text' name='benef' id='benef'> </p>  
	 
	
 <p> Num: Factura :&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;
	 <input type ='text' name='factura' id='factura'> </p>  
		
	<!-- <p> Operador :<select style=' border:solid 1px #582b9a; border-radius:5px;' name='conductor' id='conductor'> 
			   <option selected value=''></option>
               <?  for ($x=0; $x<$i; $x++){
		            echo" <option value='$emp1[$x]'>$emp1[$x]</option>";
                    }
?>			
	</select></p>   -->
 <!--<p> Vencimiento :&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;
       <input name="vencim" class="calendario"  type="text" value='<?echo $fecha; ?>' style='border:solid 1px #8bb437; border-radius:5px; text-transform:uppercase;' />
      </p>  
-->
	
	<p> Importe :&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;
	 <input type ='number' name='importe' id='importe'> </p>  
	<p> Observaciones :&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;
	<textarea name='obser' id='obser' cols='50' rows='2'></textarea></p>
	
   <!--// <input type="hidden"  name="modUniR" id="modUniR" value="<?=$idCamion;?>" />
	 <input type="hidden"  name="numsolR" id="numsolR" value="<?=$numsol;?>" />-->
	<input type="submit" name="Submit" value="Guardar" />
</form>	
</div>

  </body>
</html>

<script language="javascript">
function confirma()
{
//	alert("confir");
 window.location.href = 'ac_desglose.php?confir=Envia';

//document.form1.submit();
}
</script>

<script language="javascript">
function histo()
{
 
 window.location.href = 'histDesglose.php';

//document.form1.submit();
}

</script>
<script language="javascript">
function histo1()
{
 window.location.href = 'HistoricoGasto.php';

//document.form1.submit();
}
</script>
<script language="javascript">
function confirma1()
{
//	alert("confir");
 window.location.href = 'desgloseGastos.php';

//document.form1.submit();
}
</script>

<script>
function acumFact(a,b) 
{

var oper =a;
var r=b;
var fec='fechaI'+r;
var clie='clieI'+r;
var uni='UniI'+r;
var dest='destI'+r;
var operI='operI'+r;
var fechaE='fechaEI'+r;
var entre='entI'+r;
var obs='obsI'+r;
var car='carI'+r;

var Tfec=document.getElementById(fec).value;
var Tclie=document.getElementById(clie).value;
var Tuni=document.getElementById(uni).value;
var Tdest=document.getElementById(dest).value;
var Toper=document.getElementById(operI).value;
var TfechaE=document.getElementById(fechaE).value;
var Tentre=document.getElementById(entre).value;
var Tobs=document.getElementById(obs).value;
var Tcar=document.getElementById(car).value;

window.location.href = 'ac_Opera.php?opera='+oper+'&fec='+Tfec+'&clie='+Tclie+'&uni='+Tuni+'&dest='+Tdest+'&conduc='+Toper+'&fecE='+TfechaE+'&ent='+Tentre+'&obs='+Tobs+'&car='+Tcar;

}
</script>   
