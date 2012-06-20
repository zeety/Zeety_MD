<?php
/**
 * Configurador.class.php
 *
 * Encargado de rescatar las variables de configuracion globales,
 *
 * @author 	Paulo Cesar Coronado
 * @version 	1.1.0.1
 * @package 	Kixi
 * @copyright 	Universidad Distrital Francisco Jose de Caldas - Grupo de Trabajo Academico GNU/Linux GLUD
 * @license 	GPL Version 3 o posterior
 *
 */
class Configurador{

	private static $instance;


	/**
	 *
	 * Arreglo que contiene las variables de configuración globales para el aplicativo.
	 * Las variables se extraen de la base de datos de acuerdo a los datos de acceso declarados
	 * en config.inc.php.
	 *
	 * @var string
	 */
	private $configuracion;

	/**
	 *
	 * Rutas a los archivos de definición de clases que deben ser cargadas porque aún
	 * no se han rescatado las variables de configuración.
	 * @var string
	 */
	var $rutas;

	/**
	 * Puntero al archivo config.inc.php
	 * @var file
	 */
	var $fp;
	/**
	 * Objeto de la clase Encriptador se encarga de codificar/decodificar cadenas de texto.
	 * @var Encriptador
	 */
	var $cripto;

	/**
	 *
	 * Objeto de la Clase FuncionGeneral, con funciones miembro generales que encapsulan funcionalidades
	 * básicas.
	 * @var FuncionGeneral
	 */
	var $funcion;
	/**
	 *
	 * Permite la conexión simple a una base de datos
	 * @var ConectorBasicoDB
	 */

	var $conectorDB;

	function __construct() {
		$this->configuracion["inicio"] = true;
	}


	public static function singleton()
	{
		if (!isset(self::$instance)) {
			$className = __CLASS__;
			self::$instance = new $className;
		}
		return self::$instance;
	}


	function setRutas($variables){
		$this->rutas=$variables;
	}


	function getConfiguracion() {
		return $this->configuracion;
	}
	
	
	function getVariableConfiguracion($cadena="") {
		
		if(isset($this->configuracion[$cadena])){
			return $this->configuracion[$cadena];
		}
		
		return false;		
		
	}
	
	function setVariableConfiguracion($variable="", $cadena="") {
	
		$this->configuracion[$variable]=$cadena;
		return true;
	
	}
	
	/**
	 * Rescata las variables de configuración que se encuentran en config.inc.php y 
	 * en la base de datos principal (cuyos datos de conexión están en config.inc.php).
	 * Los datos son cargados en el arreglo $configuracion
	 * @param Ninguno 
	 * @return number
	 */

	function variable($ruta="") {
			
		$rutaEstandar=$this->rutas["rutaFramework"]."/". $this->rutas["rutaBiblioteca"];
			
		require_once($rutaEstandar."/db/"."ConectorBasicoDB.class.php");
		require_once($rutaEstandar."/configuracion/"."FuncionGeneral.class.php");
		require_once($rutaEstandar."/criptografia/"."Encriptador.class.php");
		require_once($rutaEstandar."/criptografia/"."aes.class.php");
		require_once($rutaEstandar."/criptografia/"."aesctr.class.php");

		$this->cripto = Encriptador::singleton();
		$this->funcion = new funcionGeneral();

		$this->abrirArchivoConfiguracion();

		$this->conectorDB = new ConectorBasicoDB($this->configuracion);

		$resultado=$this->rescatarVariablesDB();

		if($resultado){
			$this->rescatarVariablesSesion();
		};		
		
		$this->funcion->especificarConfiguracion($this->configuracion);
		
		return 0;
	}

	private function abrirArchivoConfiguracion($ruta=""){

		if($ruta==""){
			$ruta="configuracion";
		}

		$this->fp = fopen($ruta . "/config.inc.php", "r");
		if (!$this->fp) {
			return false;
		}

		$this->i = 0;
		while (!feof($this->fp)) {
			$this->linea = $this->cripto->decodificar(fgets($this->fp, 4096), "");
			$this->i++;
			switch ($this->i) {
				case 3:
					$this->configuracion["db_sys"] = $this->linea;
					break;

				case 4:
					$this->configuracion["db_dns"] = $this->linea;
					break;
				case 5:
					$this->configuracion["db_nombre"] = $this->linea;
					break;
				case 6:
					$this->configuracion["db_usuario"] = $this->linea;
					break;
				case 7:
					$this->configuracion["db_clave"] = $this->linea;
					break;
				case 8:
					$this->configuracion["db_prefijo"] = $this->linea;
					break;
			}
			if ($this->i == 8) {
				break;
			}
			
		}
		fclose($this->fp);
			
	}

	private function rescatarVariablesDB(){
		if (is_resource($this->conectorDB->enlace)) {
			$cadena_sql = "SELECT ";
			$cadena_sql.=" `parametro`,  ";
			$cadena_sql.=" `valor`  ";
			$cadena_sql.="FROM ";
			$cadena_sql.=$this->configuracion["db_prefijo"] . "configuracion ";

			$this->total = $this->conectorDB->registro_db($cadena_sql, 0);

			if ($this->total > 0) {
				$this->registro = $this->conectorDB->obtener_registro_db();

				for ($j = 0; $j < $this->total; $j++) {
					$this->configuracion[$this->registro[$j][0]] = $this->registro[$j][1];
					//	echo $this->configuracion[$this->registro[$j][0]]."<br>";
				}
				$this->configuracion["instalado"] = 1;
			} else {
				error_log("No se puede iniciar la aplicacion. Imposible rescatar las variables de configuracion!", 0);
				return 0;
			}
		} else {
			error_log("No se puede iniciar la aplicacion. Imposible determinar un recurso de base de datos.!", 0);
			return 0;
		}

	}

	private function rescatarVariablesSesion(){

		if (isset($_SERVER["HTTPS"])) {

			$this->configuracion["host"] = $this->configuracion["host_seguro"];
		}

		//Rescatar las variable basicas de sesion de un usuario
		$this->configuracion["id_sesion"] = $this->funcion->rescatarNumeroSesion($this->configuracion);

		$this->configuracion["sesionAcceso"] = $this->funcion->rescatarValorSesion($this->configuracion, "acceso");
		$this->configuracion["sesionIdUsuario"] = $this->funcion->rescatarValorSesion($this->configuracion, "id_usuario");
		$this->configuracion["sesionUsuario"] = $this->funcion->rescatarValorSesion($this->configuracion, "usuario");

		$lenguaje = $this->funcion->rescatarValorSesion($this->configuracion, "lenguaje");
		if ($lenguaje) {
			$this->configuracion["lenguaje"] = $this->funcion->rescatarValorSesion($this->configuracion, "lenguaje");
		}

		//                foreach($this->configuracion as $clave=>$valor ){
		//                    echo $clave."-->".$valor."<br>";
		//                }

	}

};

?>
