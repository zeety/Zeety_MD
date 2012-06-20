<?php
$indice=0;
$estilo[$indice++]="general.css";
$estilo[$indice++]="estiloCuadrosMensaje.css";
$estilo[$indice++]="estiloTexto.css";

foreach ($estilo as $nombre){
	include_once($nombre);
}
?>