<?php

	$parametros="?1=1";

	foreach ($_POST as $campo => $valor)
	{
		$$campo = $valor;
		$parametros.="&".$campo."=".urlencode($valor);
	}
	
	$url = "https://www.reservas.cargestion365.com/api/carplus/acciones.php";
	
	$resultado=file_get_contents($url.$parametros);
	
	echo $resultado;

?>

