<?php
/**
 * Administra el inicio de la aplicacion.
 *
 * @author	Paulo Cesar Coronado
 * @version	1.0.0.2, 29/12/2011
 * @package 	clases
 * @copyright 	Universidad Distrital F.J.C
 * @license	GPL Version 3.0 o posterior
 *
 */

require_once("Configurador.class.php");

class Inicializador{

	/**
	 *
	 * Objeto. Su atributo $configuracion contiene los valores necesarios para gestionar la aplicacion.
	 * @var Configurador
	 */
	var $miConfigurador;

	/**
	 *
	 * Objeto. Actua como controlador del modulo de instalación del framework/aplicativo
	 * @var Instalador
	 */
	var $miInstalador;

	/**
	 *
	 * Objeto. Instancia de la pagina que se está visitando
	 * @var Pagina
	 */
	var $miPagina;

	/**
	 *
	 * Arreglo.Ruta de acceso a los archivos, se utilizan porque aún no se ha rescatado las
	 * variables de configuración.
	 *
	 * @var string
	 */
	var $misVariables;




	/**
	 * Contructor
	 * @param none
	 * @return integer
	 * */

	function __construct(){

		/**
		 *
		 * Debido al caracter global que tienen las variables de configuración y los diferentes métodos de FuncionGeneral
		 * es adecuado aplicar Singleton para Configurador, evidentemente esto crea un alto acoplamiento que podría ser
		 * interpretado como una práctica no recomendada, pero que en este caso es necesario para reducir la complejidad del
		 * código. Esto también se justifica por la estabilidad del paquete configuracion
		 *
		 */

		$this->miConfigurador=Configurador::singleton();
		return 0;
	}

	/**
	 *
	 * Iniciar la aplicación.
	 */

	function iniciar(){

		// Poblar el atributo miConfigurador->configuracion
		$this->miConfigurador->variable();

		if(!$this->miConfigurador->getVariableConfiguracion("instalado"))
		{
			$this->instalarAplicativo();

		}else{

			$this->ingresar();
		}
	}

	/**
	 *
	 * Asigna los valores a las variables que indican las rutas predeterminadas.
	 * @param strting array $variables
	 */

	function setMisVariables($variables){
		$this->misVariables=$variables;
		$this->miConfigurador->setRutas($variables);
	}

	/**
	 *
	 * Ingresar al aplicativo.
	 * @param Ninguno
	 * @return int
	 */
	private function ingresar() {

		/**
		 * @global boolean $GLOBALS['autorizado']
		 * @name $autorizado
		 */
		$GLOBALS["autorizado"]=TRUE;

		$pagina=$this->determinarPagina();

		/**
		 * Verificar que se tenga una sesión válida
		 */

		require_once($this->miConfigurador->getVariableConfiguracion("raizDocumento")."/framework/BCK/sesion/Autenticador.class.php");
		$this->autenticador=new Autenticador();
		$this->autenticador->especificarPagina($pagina);

		if($this->autenticador->autenticacion()){
				require_once($this->miConfigurador->getVariableConfiguracion("raizDocumento")."/framework/BCK/gui/Pagina.class.php");
				$this->miPagina=new pagina($pagina);
				return true;			
		}
		$this->miConfigurador->setVariableConfiguracion("error", true);
		require_once($this->miConfigurador->getVariableConfiguracion("raizDocumento")."/framework/BCK/gui/Mensaje.page.php");
		return false;
	}


	private function determinarPagina(){
		/**
		 * Determinar la página que se desea cargar
		 */

		if(isset($_REQUEST[$this->miConfigurador->getVariableConfiguracion("enlace")])) {

			$this->miConfigurador->cripto->decodificar_url($_REQUEST[$this->miConfigurador->getVariableConfiguracion("enlace")]);

			if(isset($_REQUEST["redireccionar"])) {
				$this->redireccionar();
				return 0;
			}
			if(isset($_REQUEST["pagina"])) {
				return $_REQUEST["pagina"];
			}else {
				return "";
			}

		}else {

			return "index";
		}
	}

	/**
	 *
	 * Instalar el aplicativo.
	 */

	private function instalarAplicativo() {

		$rutaBiblioteca=$this->misVariables["rutaFramework"]."/". $this->misVariables["rutaBiblioteca"];
		require_once($rutaBiblioteca."/instalacion/Instalador.class.php");
		$this->miInstalador=new Instalador();
		if(isset($_REQUEST["instalador"])){
			$this->miInstalador->procesarInstalacion();
		}else{
			$this->miInstalador->mostrarFormularioDatosConexion();
		}
		return 0;
	}
	/**
	 * Redireccionar a otra página
	 * @return number
	 */

	function redireccionar(){
		$variable="";

		foreach($_REQUEST as $clave=> $val) {
			if($clave !="redireccion") {
				$variable.="&".$clave."=".$val;
			}
		}

		$this->miConfigurador->cripto->decodificar_url($_REQUEST["redireccion"]);

		foreach($_REQUEST as $clave=> $val) {
			$variable.="&".$clave."=".$val;
		}

		$variable=$this->miConfigurador->cripto->codificar_url($variable,$this->miConfigurador->configuracion);
		$indice=$this->miConfigurador->configuracion["host"].$this->miConfigurador->configuracion["site"]."/index.php?";
		echo "<script>location.replace('".$indice.$variable."')</script>";
		return 0;


	}

};

?>