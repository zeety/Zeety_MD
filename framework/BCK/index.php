<?
/**
 * index.php
 *
 * Trata de capturar accesos no autorizados a la aplicacion.
 *
 */
// Listado de posibles fuentes para la dirección IP, en orden de prioridad
$fuentes_ip = array(
            	"HTTP_X_FORWARDED_FOR",
            	"HTTP_X_FORWARDED",
            	"HTTP_FORWARDED_FOR",
            	"HTTP_FORWARDED",
            	"HTTP_X_COMING_FROM",
            	"HTTP_COMING_FROM",
            	"REMOTE_ADDR",
);

foreach ($fuentes_ip as $fuentes_ip) {
	// Si la fuente existe captura la IP
	if (isset($_SERVER[$fuentes_ip])) {
		$proxy_ip = $_SERVER[$fuentes_ip];
		break;
	}
}

$proxy_ip = (isset($proxy_ip)) ? $proxy_ip : @getenv("REMOTE_ADDR");
error_log("Acceso no autorizado el ".date('l jS \del Y h:i:s A')." desde: ".$proxy_ip." usando ".$_SERVER['HTTP_USER_AGENT'],0);

?>
<html>
<head>
<title>Acceso no autorizado.</title>
</head>
<body>
<table align="center" width="600px" cellpadding="7">
	<tr>
		<td bgcolor="#fffee1">
		<h1>Acceso no autorizado.</h1>
		</td>
	</tr>
	<tr>
		<td>
		<h3>Se ha creado un registro de acceso ilegal desde la
		direcci&oacute;n: <b><? echo $proxy_ip ?></b>.</h3>
		<b>Fecha: </b><?php echo date('l jS Y h:i:s A')?> <br>
		<b>Posible IP:(*)</b> <?php echo $proxy_ip?><br>
		<b>Navegador/Plataforma:</b> <?php echo $_SERVER['HTTP_USER_AGENT']?>.
		</td>
	</tr>
	<tr>
		<td>Si considera que esto es un error por favor comuniquese con el
		administrador del sistema.</td>
	</tr>
	<tr>
		<td><b>(*)Descargos de responsabilidad:</b><br>
		El acceso a esta secci&oacute;n del aplicativo supone un intento de fraude.<br>Por pol&iacute;ticas de seguridad, los datos asociados a direcciones IP
		de open proxies o servicios de anonimato ser&aacute;n reportadas mensualmente a los organismos de seguridad correspondientes.</td>
	</tr>
</table>
</body>
<html>