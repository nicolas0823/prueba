<!DOCTYPE html>
<html>
<head>
	<meta charset="UTF-8">
        <title>GeoPortal</title>
        <link type="text/css" rel="stylesheet" href="web/css/bootstrap.min.css">
        <script type="text/javascript" src="web/js/jquery.min.js"></script>
        <script type="text/javascript" src="web/js/bootstrap.min.js"></script>

	 
</head>
<style type="text/css">
	.mapa{
		max-width: 100%;
	}
	div{
		max-width: 80%;
	}
	.capas{
		margin-left : 3%;
	}
	.boton{
		margin-top: 2%;
		margin-left: 20%;
	}
</style>
<body>
	<div id="archivo">
		<div class="form-group row col-lg-12 center-block mapa">
		<form method="POST" action="#">
			
				<br><label>Seleccione el mapa</label><br>
				<input type="file" name="mapa" id="mapa">
				<input class="btn btn-primary" type="submit" name="enviar" value="Cargar">
			
		</form>
		</div>

</body>
</html>
<?php
if ((isset($_POST['mapa'])) || (isset($_POST['mapa2'])) ) {
	if (!$_POST['mapa']==null) {
		$mapa_load=$_POST['mapa'];
		
	}
	else{
		$mapa_load=$_POST['mapa2'];

	}



//echo $mapa_load."este ese elmapa";
 if(!extension_loaded("MapScript")){
	 dl('php_mapscript.'.PHP_SHLIB_SUFFIX);
 }


 $val_zsize=1;
 $check_pan="CHECKED";
 $map_path="/var/www/html/ms/map_files/";
 $map_file="colombia.map";
 //echo $mapa_load;
 $map= ms_newMapObj($mapa_load);
 

 if ( isset($_POST["mapa_x"]) && isset($_POST["mapa_y"])
 && !isset($_POST["full"]) ) {

	 $extent_to_set = explode(" ",$_POST["extent"]);

	 $map->setextent($extent_to_set[0],$extent_to_set[1],
	 $extent_to_set[2],$extent_to_set[3]);

	 $my_point = ms_newpointObj();
	 $my_point->setXY($_POST["mapa_x"],$_POST["mapa_y"]);

	 $my_extent = ms_newrectObj();

	 $my_extent->setextent($extent_to_set[0],$extent_to_set[1],
	 $extent_to_set[2],$extent_to_set[3]);

	 $zoom_factor = $_POST["zoom"]*$_POST["zsize"];
	 if ($zoom_factor == 0) {
		 $zoom_factor = 1;
		 $check_pan = "CHECKED";
		 $check_zout = "";$check_zin = "";
	 } else if ($zoom_factor < 0) {
		 $check_pan = "";
		 $check_zout = "CHECKED";
		 $check_zin = "";
	 } else {
		 $check_pan = "";
		 $check_zout = "";
		 $check_zin = "CHECKED";
	 }

	 $val_zsize = abs($zoom_factor);

	 $map->zoompoint($zoom_factor,$my_point,$map->width,$map->height,$my_extent);

 }
 	$capas=$_POST['capas'];
 	
 	if(isset($_POST['rojo']) || isset($_POST['verde']) || isset($_POST['azul'])){

 		if($_POST['rojo']==null && $_POST['verde']==null && $_POST['azul']==null){
 			$rojo=$_POST['rojo2'];
 			$verde=$_POST['verde2'];
 			$azul=$_POST['azul2'];
 		}else{
 			$rojo=$_POST['rojo'];
 			$verde=$_POST['verde'];
 			$azul=$_POST['azul'];
 		}
 	}
 

	$Jcapas=$map->getAllLayerNames();
	$p=0;

 		foreach($Jcapas as $idx=>$layer){
			$var[$p]=$layer;
			//echo $var[$p];
			$p++;
		}

		for ($i=0; $i <count($capas) ; $i++) { 
			$layerObject=$map->getLayerByName($capas[$i]);	
			$layerObject->set("status",MS_OFF);
		}

		$color=$_POST['color'];

		for($i=0;$i<count($color);$i++){
			$Jcolor=$map->getLayerByName($color[$i])->getClass(0)->getStyle(0)->color;
			$Jcolor->setRGB($rojo,$verde,$azul);
		}



 @$image=$map->draw();
 @$image_url=$image->saveWebImage();
 $mapaLegend=$map->drawLegend();
 $urlLegend=$mapaLegend->saveWebImage();

 $extent_to_html = $map->extent->minx." ".$map->extent->miny." "
 .$map->extent->maxx." ".$map->extent->maxy;

 ?>
 <HTML>
	 <HEAD>
		<TITLE>Mapa Zoom</TITLE>
	 </HEAD>
 <BODY>
	 
	 <FORM METHOD=POST ACTION=<?php echo $HTTP_SERVER_VARS['PHP_SELF']?>>
		<div class="row col-lg-3 center-block">
			<label>ROJO &nbsp;&nbsp;&nbsp;</label><input  class="form-control" type="number" name="rojo" min="0" max="255"><br>
		 	<label>VERDE </label><input  class="form-control" type="number" name="verde" min="0" max="255"><br>
		 	<label>AZUL &nbsp;&nbsp;&nbsp;</label><input class="form-control" type="number" name="azul" min="0" max="255"><br>
	 	</div>
	 	
	 	<div class="row col-lg-4 capas">
	 		<center><label >Seleccione las capas a Colorear</label></center><br>	
	 		<?php
	 		$selected="";
	 			for($j=0;$j<count($var);$j++)
	 			{
					for($i=0;$i<count($color);$i++)
					{
						if($color[$i]==$var[$j]){
							$selected="checked";
						}
						
					}
					echo "<input $selected type='checkbox' name='color[]' value='".$var[$j]."'>".$var[$j]."<br>";
					$selected="";
				}
	 		?>

		<br>
	 	</div>
	 	
	 	<div class="row col-lg-4 capas">
	 		<center><label >Seleccione las capas a Apagar</label></center><br>	
	 		<?php
	 		$selected="";
	 			for($j=0;$j<count($var);$j++)
	 			{
					for($i=0;$i<count($capas);$i++)
					{
						if($capas[$i]==$var[$j]){
							$selected="checked";
						}
						
					}
					echo "<input $selected type='checkbox' name='capas[]' value='".$var[$j]."'>".$var[$j]."<br>";
					$selected="";
				}
	 		?>

		<br>
	 	</div>
	 	<div class="col-md-5 boton"><input class="btn btn-success" type="SUBMIT" name="enviar" value="Enviar"></div>
	 	<CENTER>
	 	
	 		<TABLE class="table table-striped" border=1 align=center>
			 <TR>
				 <TD colspan="2">
				 	<img src="<?php echo $urlLegend ?>" alt="leyenda" border="0">

				 	<INPUT TYPE=IMAGE NAME="mapa" border="1" SRC="<?php echo $image_url?>">
				 	
				 	
				 </TD>
			 </TR>
			 <TR>
				 <TD>
				 Pan
				 </TD>
				 <TD>
				 <INPUT TYPE=RADIO NAME="zoom" VALUE=0 <?php echo $check_pan?>>
				 </TD>
			 </TR>
				 <TR>
				 <TD>
				 Zoom In
				 </TD>
				 <TD>
				 <INPUT TYPE=RADIO NAME="zoom" VALUE=1 <?php echo $check_zin?>>
				 </TD>
			 </TR>
			 <TR>
				 <TD>
				 Zoom Out
				 </TD>
				 <TD>
				 <INPUT TYPE=RADIO NAME="zoom" VALUE=-1 <?php echo $check_zout?>>
				 </TD>
			 </TR>
			 <TR>
				 <TD>
				 Zoom Size
				 </TD>
				 <TD>
				 <INPUT TYPE=TEXT NAME="zsize" VALUE="<?php echo $val_zsize?>"
				 SIZE=2>
				 </TD>
			 </TR>
			 <TR>
				 <TD>
					Full Extent
				 </TD>
				 <TD>
					 <INPUT class="btn btn-success" TYPE=SUBMIT NAME="full" VALUE="Go"
					 SIZE=1>
				 </TD>
			</TR>	 
		 </TABLE>
		
	 <INPUT TYPE=HIDDEN NAME="extent" VALUE="<?php echo $extent_to_html?>">
	 <input type="HIDDEN" name="mapa2" value="<?php echo $mapa_load ?>">
	 <input type="HIDDEN" name="capa2[]" value="<?php echo $capas ?>">
	 <input type="HIDDEN" name="rojo2" value="<?php echo $rojo ?> ">
	 <input type="HIDDEN" name="verde2" value="<?php echo $verde ?> ">
	 <input type="HIDDEN" name="azul2" value="<?php echo $azul ?> ">
	 </FORM>
	 </CENTER>
 </BODY>
 </HMTL>
 <?php
	}
	echo "<pre>";
	echo print_r($_POST);
 ?>