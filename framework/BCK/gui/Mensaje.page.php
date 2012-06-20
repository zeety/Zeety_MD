<?php
/**
 * Página para mostrar mensajes de error fatales dentro de la aplicación. Se ejecuta completa pues se espera que solo se
 * muestre en casos extremos.
 *
 * @author	Paulo Cesar Coronado
 * @version	0.0.0.2, 25/03/2012
 * @package 	framework:BCK:instalacion
 * @copyright Universidad Distrital F.J.C
 * @license	GPL Version 3.0 o posterior
 *
 */

if(!$this->miConfigurador->getVariableConfiguracion("estilo")){
	
	$this->miConfigurador->setVariableConfiguracion("estilo","basico");
}

if(!$this->miConfigurador->getVariableConfiguracion("idioma")){
	$this->miConfigurador->setVariableConfiguracion("idioma", "es_es");
}

/**
 * I18n
 */

$miIdioma=$this->miConfigurador->getVariableConfiguracion("raizDocumento")."/framework/idioma/";
$miIdioma.=$this->miConfigurador->getVariableConfiguracion("estilo");
$miIdioma.="/".$this->miConfigurador->getVariableConfiguracion("idioma")."/Mensaje.page.php";

include_once $miIdioma;

$indice=strpos($_SERVER["REQUEST_URI"], "/index.php");

if($indice===false){
	$indice=strpos($_SERVER["REQUEST_URI"], "/",1);

}
$sitio=substr($_SERVER["REQUEST_URI"],0,$indice);
?>
<html>
<head>
<title></title>
<script>
<?php include_once $this->miConfigurador->getVariableConfiguracion("raizDocumento")."/framework/javascript/jquery.js"?>
</script>
<script>
$(window).load(function() {
	$("#mensaje").fadeIn(1000);
	
});

	  
	
</script>
<style type="text/css">
<?php include_once $this->miConfigurador->getVariableConfiguracion("raizDocumento")."/framework/estilo/".$this->miConfigurador->getVariableConfiguracion("estilo")."/Estilo.php"?>
</style>
<meta content="text/html;" http-equiv="content-type" charset="utf-8">
</head>
<body>
	<div id="mensaje" class="warning shadow ocultar" ><?php 
		echo $idioma["mensaje"];
	?></div>
</body>
</html>
