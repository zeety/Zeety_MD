<?php
/**
 * Página final del proceso de instalación
 *
 * @author	Paulo Cesar Coronado
 * @version	0.0.0.2, 25/03/2012
 * @package 	framework:BCK:instalacion
 * @copyright Universidad Distrital F.J.C
 * @license	GPL Version 3.0 o posterior
 *
 */
?>
<html>
<head>
<title>Instalaci&oacute;n Exitosa</title>
<meta content="text/html;" http-equiv="content-type" charset="utf-8">
<style type="text/css">
body {
	font-family: "Lucida Grande", "Lucida Sans Unicode", Verdana, Arial,
		Helvetica, sans-serif;
	font-size: 12px;
	width: 600px;
	margin:0 auto;
}

.clean-gray{
border:solid 1px #DEDEDE;
background:#EFEFEF;
color:#222222;
padding:4px;
text-align:center;
}

.clean-green{
border:solid 1px #1B8D44;
background:#CEE6C3;
color:#1B8D44;
padding:4px;
text-align:center;
}

.spacer {
	clear: both;
	height: 10px;
}
</style>
</head>
<body>	
	<div class="spacer">
	</div>
	<div class="clean-green">
	<h1>El aplicativo ha sido intalado con éxito!!!</h1>
	<p>Antes de continuar por favor cambie los permisos del archivo <b>config.inc.php</b></p>
	<p><a href="<?php $_REQUEST["host"].$_REQUEST["site"]?>">Continuar</a></p>
	</div>
	<div class="spacer">
	</div>	
	<div class="clean-gray">
	<p><img src="<?php echo $_REQUEST["host"].$_REQUEST["site"]."/framework/grafico/logo100free.png"?>"></p>
	<p style="text-align:justify"><b>Software libre</b> es una cuestión de libertad, no de precio. El software libre brinda la libertad a los usuarios de ejecutar, copiar, 
	distribuir, estudiar, cambiar y mejorar el software.</p>
	<p style="text-align:justify">Los autores de este programa le otorgan las cuatro libertades esenciales:
	<ul>
	<li>Libertad de ejecutar el programa, para cualquier propósito (libertad 0).</li>
	<li>Libertad de estudiar cómo trabaja el programa, y cambiarlo para que haga lo que usted quiera (libertad 1).</li>
	<li>Libertad de redistribuir copias para que pueda ayudar al prójimo (libertad 2).</li>
	<li>Libertad de distribuir copias de sus versiones modificadas a terceros (libertad 3). 
	Si lo hace, puede dar a toda la comunidad una oportunidad de beneficiarse de sus cambios.</li>
	</ul>
	</p>
	<p style="text-align:justify">Lo anterior amparado por los términos de la Licencia Publica General GNU como es publicada por la Fundacion de Software Libre; 
	en la 3ra versión de la licencia, o (a su decisión) cualquier versión posterior.</p>
	<p><b>Este programa es distribuido con la esperanza de que sea útil, pero SIN GARANTÍA ALGUNA. Sin siquiera la garantía implícita de VALOR COMERCIAL
	o DE CONFORMIDAD PARA UN PROPÓSITO EN PARTICULAR.</b>
	</p>
	</div>	
</body>
</html>
