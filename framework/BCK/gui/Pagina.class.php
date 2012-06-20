<?
/**
 * Pagina.class.php
 *
 * Clase que describe los objetos Pagina que conforman el aplicativo.
 *
 * @author 	Paulo Cesar Coronado
 * @version 	1.1.0.1
 * @package 	framework::BCK::gui
 * @copyright 	Universidad Distrital Francisco Jose de Caldas - Grupo de Trabajo Academico GNU/Linux GLUD
 * @license 	GPL Version 3 o posterior
 *
 */

class Pagina {

	//Atributos de Clase
	var $id_pagina;

	var $miConfigurador;

	var $cadena_sql;

	var $armador;

	var $consultorSQL;


	//Metodos
	function __construct($pagina) {

		$this->especificarPagina($pagina);
		$this->miConfigurador=Configurador::singleton();

		include_once($this->miConfigurador->configuracion["raizDocumento"]."/framework/BCK/gui/ArmadorPagina.class.php");
		include_once($this->miConfigurador->configuracion["raizDocumento"]."/framework/BCK/gui/guiSql.class.php");

		$this->armador=new ArmadorPagina();
		$this->consultorSQL= new guiSql();


		$this->iniciar();

		/*     Descomentar si se quiere obligar el uso del protocolo HTTPS
		 if(!isset($_SERVER["HTTPS"])&&!isset($_REQUEST["googlemaps"])) {

		error_log("Attemp to access from a non HTTPS page without googlemaps variable", 0);
		echo "<script>location.replace('".$this->miConfigurador->configuracion["host_no_seguro"].$this->miConfigurador->configuracion["site"]."')</script>";

		}else {
			
		} */
	}



	function especificarPagina($pagina) {
		$this->id_pagina=$pagina;
		return true;

	}


	function mostrar_pagina() {
		$GLOBALS["tab"]=1;
		
		$this->cadena_sql=$this->clausulasql("pagina");

		$this->base=new dbms($this->miConfigurador->configuracion);
		$this->enlace=$this->base->conectar_db();

		if (is_resource($this->enlace)) {
			$this->base->registro_db($this->cadena_sql,0);
			$this->registro=$this->base->obtener_registro_db();
			$this->total=$this->base->obtener_conteo_db();

			if($this->total<1) {
				echo "<h3>La pagina que esta intentando acceder no esta disponible.</h3><br>";
				exit;
			}
			else {
				//Verificar parametros por defecto
				if($this->registro[0][5]!="") {
					$parametros=explode("&",$this->registro[0][5]);
					foreach($parametros as $valor) {
						$elParametro=explode("=",$valor);
						$_REQUEST[$elParametro[0]]=$elParametro[1];

					}
				}
				$nueva_sesion=new sesiones($this->miConfigurador->configuracion);
				$esta_sesion=$nueva_sesion->numero_sesion();
				$this->registro=$nueva_sesion->rescatar_valor_sesion($this->miConfigurador->configuracion,"id_usuario");
				if($this->registro) {
					$this->id_usuario=$this->registro[0][0];
				}
				else {
					$this->id_usuario=0;
				}

				$this->SQL=$this->clausulasql("usuario");


				$this->base->registro_db($this->SQL,0);
				$this->registro=$this->base->obtener_registro_db();
				$this->total=$this->base->obtener_conteo_db();
				if($this->total<1) {
					$this->estilo='basico';
				}
				else {
					$this->estilo=$this->registro[0][1];
				}

				unset($this->registro);
				unset($this->total);


				$this->tamanno=$this->miConfigurador->configuracion["tamanno_gui"];
				$GLOBALS["fila"]=0;
				$GLOBALS["tab"]=1;


				if(!isset($_REQUEST["no_pagina"])) {
					//Para paginas que utilizan ajax
					if(isset($_REQUEST["xajax"])) {
						require_once($this->miConfigurador->configuracion["raiz_documento"].$this->miConfigurador->configuracion["clases"]."/xajax/xajax_core/xajax.inc.php");
						$GLOBALS["xajax"] = new xajax();
						//$GLOBALS["xajax"]->configure('debug', true);


						//Registrar las funciones especificas de ajax para la pagina
						//Las funciones vienen relacionadas en la variable xajax separadas por el caracter "|"
						$funciones_ajax=explode('|', $_REQUEST["xajax"]);
						$i=0;

						//Incluir el archivo que procesara las peticiones Ajax en PHP
						if(!isset($_REQUEST["xajax_file"])) {
							include_once($this->miConfigurador->configuracion["raiz_documento"].$this->miConfigurador->configuracion["clases"]."/xajax/include/funciones_ajax.class.php");
							while(isset($funciones_ajax[$i])) {
								$GLOBALS["xajax"]->register(XAJAX_FUNCTION,new xajaxUserFunction($funciones_ajax[$i],$this->miConfigurador->configuracion["host"].$this->miConfigurador->configuracion["site"].$this->miConfigurador->configuracion["clases"]."/xajax/include/funciones_ajax.class.php"));
								$i++;
							}
						}
						else {
							include_once($this->miConfigurador->configuracion["raiz_documento"].$this->miConfigurador->configuracion["clases"]."/xajax/include/".$_REQUEST["xajax_file"].".class.php");
							while(isset($funciones_ajax[$i])) {
								$GLOBALS["xajax"]->register(XAJAX_FUNCTION,new xajaxUserFunction($funciones_ajax[$i],$this->miConfigurador->configuracion["raiz_documento"].$this->miConfigurador->configuracion["clases"]."/xajax/include/".$_REQUEST["xajax_file"].".class.php"));
								//$GLOBALS["xajax"]->registerExternalFunction($funciones_ajax[$i],$this->miConfigurador->configuracion["host"].$this->miConfigurador->configuracion["site"].$this->miConfigurador->configuracion["clases"]."/xajax/include/".$_REQUEST["xajax_file"].".class.php",XAJAX_POST);
								//$GLOBALS["xajax"]->registerFunction($funciones_ajax[$i],$this->miConfigurador->configuracion["host"].$this->miConfigurador->configuracion["site"].$this->miConfigurador->configuracion["clases"]."/xajax/include/".$_REQUEST["xajax_file"].".class.php",XAJAX_POST);
								$i++;
							}
						}



						$GLOBALS["xajax"]->processRequest();
						$GLOBALS["xajax"]->printJavascript($this->miConfigurador->configuracion["host"].$this->miConfigurador->configuracion["site"].$this->miConfigurador->configuracion["clases"]."/xajax/");
					}

					$this->armarHTML();


				}
				else {
					$this->armar_no_pagina('C',$this->cadena_sql);
				}
			}


		}

	}

	function procesar_pagina() {
		include_once($this->miConfigurador->configuracion["raiz_documento"].$this->miConfigurador->configuracion["bloques"]."/".$_REQUEST['action']."/bloque.php");
	}






	function iniciar(){

		if(isset($_REQUEST['action'])) {
			$this->procesar_pagina($this->miConfigurador->configuracion);
			return true;
		}
		$this->mostrar_pagina();
		return true;
	}







}



?>
