<?php



	$mapa="colores.map";
	if(!extension_loaded("MapScript"))
	{dl('php_mapscript.'.PHP_SHLIB_SUFFIX);
	}
	$capas=$_POST['capas'];
	$mapObject=ms_newMapObj($mapa);
	
	$p=0;

	$Jcapas=$mapObject->getAllLayerNames();

		foreach($Jcapas as $idx=>$layer){
			$var[$p]=$layer;
			//echo $var[$p];
			$p++;
		}
		//echo $p."<br>";


		for ($i=0; $i <count($capas) ; $i++) { 
			$layerObject=$mapObject->getLayerByName($capas[$i]);	
			$layerObject->set("status",MS_OFF);
		}
		$mapImage=$mapObject->draw();
		
		$urlImage=$mapImage->saveWebImage();

?>
<html>
	<head>
		<title>CARGAR MAPA</title>
	</head>
	<body>
		<input type=IMAGE src="<?php echo $urlImage;?>" border=1>
		<br><label>SELECCIONE LA CAPA A VISUALIZAR</label><br><br>
		<form method="post" action="#">
			<?php

				for($i=0;$i<$p;$i++)
				{
					echo "<input type='checkbox' name='capas[]' value=".$var[$i].">$var[$i]<br>";
				}
			?>
		<input type="hidden" name="mapa" value="<?php echo $mapa; ?>">
		<input type="submit" value="Enviar">

		</form>

	</body>
</html>
