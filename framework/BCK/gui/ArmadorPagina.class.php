<?php

class ArmadorPagina{

	function armarHTML(){
		if(!isset($_REQUEST["cache"])) {
			//Por defecto las paginas del aplicativo no tienen cache
			header("Cache-Control: cache");
			// header("Expires: Sat, 20 Jun 1974 10:00:00 GMT");
		}
		$this->html_pagina='<!DOCTYPE HTML PUBLIC "-//W3C//DTD HTML 4.01 Transitional//EN" "http://www.w3.org/TR/html4/loose.dtd"> ';
		$this->html_pagina.="\n<html lang='es'>\n";
		$this->html_pagina.="<head>\n";
		$this->html_pagina.="<title>".$this->miConfigurador->configuracion['titulo']."</title>\n";
		$this->html_pagina.="<meta http-equiv='Content-Type' content='text/html; charset=utf-8' >\n";
		$this->html_pagina.="<link rel='shortcut icon' href='".$this->miConfigurador->configuracion["host"].$this->miConfigurador->configuracion["site"]."/"."favicon.ico' >\n";
		$this->html_pagina.="<link rel='stylesheet' type='text/css' href='".$this->miConfigurador->configuracion["host"].$this->miConfigurador->configuracion["site"].$this->miConfigurador->configuracion["estilo"]."/".$this->estilo."/estiloGeneral.css' >\n";
		//A partir de la version 3 se inserta un css independiente para los formularios
		$this->html_pagina.="<link rel='stylesheet' type='text/css' href='".$this->miConfigurador->configuracion["host"].$this->miConfigurador->configuracion["site"].$this->miConfigurador->configuracion["estilo"]."/".$this->estilo."/estiloFormulario.css' >\n";

		if(isset($_REQUEST["conCalendario"])) {
			//Para el manejo de Calendarios
			//Script por Mihai Bazon, <mihai_bazon@yahoo.com>
			$this->html_pagina.="<link rel='stylesheet' type='text/css' href='".$this->miConfigurador->configuracion["host"].$this->miConfigurador->configuracion["site"].$this->miConfigurador->configuracion["javascript"]."/jscalendar/calendar-system.css'>";
			$this->html_pagina.="<script type='text/javascript' src='".$this->miConfigurador->configuracion["host"].$this->miConfigurador->configuracion["site"].$this->miConfigurador->configuracion["javascript"]."/jscalendar/calendar.js'></script>";
			$this->html_pagina.="<script type='text/javascript' src='".$this->miConfigurador->configuracion["host"].$this->miConfigurador->configuracion["site"].$this->miConfigurador->configuracion["javascript"]."/jscalendar/lang/calendar-en.js'></script>";
			$this->html_pagina.="<script type='text/javascript' src='".$this->miConfigurador->configuracion["host"].$this->miConfigurador->configuracion["site"].$this->miConfigurador->configuracion["javascript"]."/jscalendar/calendar-setup.js'></script>";
		}

		$this->html_pagina.="<script type='text/javascript' src='".$this->miConfigurador->configuracion["host"].$this->miConfigurador->configuracion["site"].$this->miConfigurador->configuracion["javascript"]."/funciones.js'></script>\n";
		$this->html_pagina.="<script type='text/javascript' src='".$this->miConfigurador->configuracion["host"].$this->miConfigurador->configuracion["site"].$this->miConfigurador->configuracion["javascript"]."/textarea.js"."'></script>\n";
		$this->html_pagina.="<script type='text/javascript' src='".$this->miConfigurador->configuracion["host"].$this->miConfigurador->configuracion["site"].$this->miConfigurador->configuracion["javascript"]."/md5.js' ></script>\n";

		//Para las paginas que tienen georeferenciacion
		if(isset($_REQUEST["googlemaps"])) {

			//Enlazar el archivo js correspondiente a la pagina
			include_once($this->miConfigurador->configuracion["raiz_documento"].$this->miConfigurador->configuracion["clases"]."/googlemaps.class.php");

			$this->googlemaps=new googlemaps($this->miConfigurador->configuracion, $this->id_pagina);
			$scriptGooglemaps=$this->googlemaps->getScript();
			$this->html_pagina.="<script type='text/javascript' src='".$scriptGooglemaps."'></script>";

			$this->html_pagina.="<script src='http://maps.google.com/maps/api/js?sensor=false' type='text/javascript'></script>";
			$this->html_pagina.="<script type='text/javascript' src='".$this->miConfigurador->configuracion["host"].$this->miConfigurador->configuracion["site"].$this->miConfigurador->configuracion["javascript"]."/googlemaps/apisupport.js"."'></script>";

			//$this->html_pagina.="<script type='text/javascript' src='".$this->miConfigurador->configuracion["host"].$this->miConfigurador->configuracion["site"].$this->miConfigurador->configuracion["javascript"]."/googlemaps.js"."'></script>";

			//V2
			//$this->html_pagina.="<script src='http://maps.google.com/maps?file=api&amp;v=2&amp;key=".$this->miConfigurador->configuracion["googlemaps"]."' type='text/javascript'></script>";
		}
		$this->html_pagina.="</head>\n";
		$this->html_pagina.="<body ";
		if(isset($_REQUEST["googlemaps"])) {
			$this->html_pagina.="onload='load()' ";
		}
		$this->html_pagina.=">\n";

		if($this->id_pagina=='index' || $this->id_pagina=='registro_exito'||$this->id_pagina=='logout_exito'||$this->id_pagina=='index_no_usuario') {
			$this->html_pagina.="<div id='marcoGeneralSimple'>\n";
		}
		else {
			$this->html_pagina.="<div id='marcoGeneral'>\n";
		}
		$secciones=$this->ancho_seccion($this->cadena_sql);

		$areas=array("A","B","C","D","E");

		foreach($areas as $valor){
			$this->html_pagina.=$this->armar_seccion($valor,$this->cadena_sql,$GLOBALS["fila"],$GLOBALS["tab"],$secciones);
		}

		$this->html_pagina.="</div>\n";


		$this->html_pagina.="<script language='JavaScript' type='text/javascript' src='".$this->miConfigurador->configuracion["host"].$this->miConfigurador->configuracion["site"].$this->miConfigurador->configuracion["javascript"]."/tooltip.js'></script>";
		if(isset($_REQUEST["script_calendario"])) {
			$scripts=explode("|",$_REQUEST["script_calendario"]);
			if(is_array($scripts)) {
				foreach($scripts as $clave=>$valor) {
					$this->html_pagina.="<script type='text/javascript' src='".$this->miConfigurador->configuracion["host"].$this->miConfigurador->configuracion["site"].$this->miConfigurador->configuracion["javascript"]."/jscalendar/calendario/".$valor.".js'></script>";
				}
			}
		}
		$this->html_pagina.="</div>\n";
		$this->html_pagina.="</body>\n";
		$this->html_pagina.="</html>\n";
		echo $this->html_pagina;
	}

	function ancho_seccion($cadena) {
		$secciones=array("B","C","D");

		$la_seccion=array();
		foreach ($secciones as $key => $value) {
			$this->la_cadena=$cadena." ";
			$this->la_cadena.="AND ";
			$this->la_cadena.=$this->miConfigurador->configuracion["prefijo"]."bloque_pagina.seccion='".$value."' ";
			$this->la_cadena.="LIMIT 1 ";
			//echo $this->la_cadena;
			$this->base->registro_db($this->la_cadena,0);
			$this->armar_registro=$this->base->obtener_registro_db();
			$this->total=$this->base->obtener_conteo_db();
			if($this->total>0) {
				$la_seccion[$value]=1;

			}
		}
		return $la_seccion;
	}

	function armar_seccion($seccion,$cadena,$fila,$tab,$secciones) {
		$this->la_cadena=$cadena.' AND '.$this->miConfigurador->configuracion["prefijo"].'bloque_pagina.seccion="'.$seccion.'" ORDER BY '.$this->miConfigurador->configuracion["prefijo"].'bloque_pagina.posicion ASC';
		$this->base->registro_db($this->la_cadena,0);
		$this->armar_registro=$this->base->obtener_registro_db();
		$this->total=$this->base->obtener_conteo_db();

		$cadenaSeccion="";

		if($this->total>0 && is_array($this->armar_registro)) {

			$ancho="";

			switch($seccion){

				case "A":
				case "E":
					$cadenaSeccion.="<div id='seccion_".$seccion."'>\n";
					break;

				case "B":
					if(!isset($secciones["C"]) && !isset($secciones["D"])) {
						$cadenaSeccion.="<div class='seccion_ampliadaTotal'>\n";
					}
					else {
						if(!isset($secciones["C"]) && isset($secciones["D"])) {
							$cadenaSeccion.="<div class='seccion_ampliada'>\n";
						}else{
							if(isset($secciones["C"]) && !isset($secciones["D"])) {
								$cadenaSeccion.="<div class='seccion_semiampliada'>\n";
							}else{
								$cadenaSeccion.= "<div id='seccion_".$seccion."'>\n";
							}
						}
					}
					break;

				case "C":
					if(isset($secciones["B"]) && isset($secciones["D"])) {
						$cadenaSeccion.= "<div id='seccion_C'>\n";
					}
					else {
						if(isset($secciones["B"]) || isset($secciones["D"])) {
							$cadenaSeccion.= "<div id='seccionC_ampliada'>\n";
						}else{
							$cadenaSeccion.= "<div class='seccion_ampliadaTotal'>\n";
						}
					}
					break;

				case "D":
					if(!isset($secciones["C"]) && !isset($secciones["B"])) {
						$cadenaSeccion.="<div class='seccion_ampliadaTotal'>\n";
					}
					else {
						if(!isset($secciones["C"]) && isset($secciones["B"])) {
							$cadenaSeccion.="<div class='seccion_ampliada'>\n";
						}else{
							if(isset($secciones["C"]) && !isset($secciones["B"])) {
								$cadenaSeccion.="<div class='seccion_semiampliada'>\n";
							}else{
								$cadenaSeccion.= "<div id='seccion_".$seccion."'>\n";
							}
						}
					}
					break;

			}




			if(isset($this->miConfigurador->configuracion["lenguaje"])) {
				$archivoLenguaje=$this->miConfigurador->configuracion["raiz_documento"].$this->miConfigurador->configuracion["estilo"]."/lenguaje/".$this->miConfigurador->configuracion["lenguaje"]."/lenguaje.php";

			}else {
				$archivoLenguaje=$this->miConfigurador->configuracion["raiz_documento"].$this->miConfigurador->configuracion["estilo"]."/lenguaje/es_es/lenguaje.php";
			}

			if(file_exists($archivoLenguaje)) {
				include($archivoLenguaje);
			}

			if (!class_exists('encriptar')) {

				include_once($this->miConfigurador->configuracion["raiz_documento"].$this->miConfigurador->configuracion["clases"]."/encriptar.class.php");
			}

			for($this->contador=0;$this->contador<$this->total;$this->contador++) {

				//$this->id_bloque=$this->armar_registro[$this->contador][0];
				if($this->armar_registro[$this->contador][4]!="") {
					$this->incluir=$this->armar_registro[$this->contador][4];
					if(isset($this->miConfigurador->configuracion["lenguaje"])) {
						$archivoLenguaje=$this->miConfigurador->configuracion["raiz_documento"].$this->miConfigurador->configuracion["estilo"]."/lenguaje/".$this->miConfigurador->configuracion["lenguaje"]."/".$this->incluir.".php";

					}else {
						$archivoLenguaje=$this->miConfigurador->configuracion["raiz_documento"].$this->miConfigurador->configuracion["estilo"]."/lenguaje/es_es/".$this->incluir.".php";
					}

					if(file_exists($archivoLenguaje)) {
						include($archivoLenguaje);
					}else {
						// error_log("TTG Error - Unable to load language file: ".$this->miConfigurador->configuracion["raiz_documento"].$this->miConfigurador->configuracion["estilo"]."/lenguaje/es_es/".$this->incluir.".php");
					}

					include($this->miConfigurador->configuracion["raiz_documento"].$this->miConfigurador->configuracion["bloques"]."/".$this->incluir."/bloque.php");



				}else {

					$cadenaSeccion.="Bloque no disponible";
				}


			}

			$cadenaSeccion.="</div>\n";
		}
		$GLOBALS["fila"]=$fila;
		$GLOBALS["tab"]=$tab;
		return $cadenaSeccion;

	}
	
	function armar_no_pagina($seccion,$cadena) {
		$this->la_cadena=$cadena.' AND '.$this->miConfigurador->configuracion["prefijo"].'bloque_pagina.seccion="'.$seccion.'" ORDER BY '.$this->miConfigurador->configuracion["prefijo"].'bloque_pagina.posicion ASC';
		$this->base->registro_db($this->la_cadena,0);
		$this->armar_registro=$this->base->obtener_registro_db();
		$this->total=$this->base->obtener_conteo_db();
		if($this->total>0) {
	
	
			for($this->contador=0;$this->contador<$this->total;$this->contador++) {
	
				$this->id_bloque=$this->armar_registro[$this->contador][0];
				$this->incluir=$this->armar_registro[$this->contador][4];
				include($this->miConfigurador->configuracion["raiz_documento"].$this->miConfigurador->configuracion["bloques"]."/".$this->incluir."/bloque.php");
	
	
			}
	
	
		}
		return TRUE;
	
	}
}
