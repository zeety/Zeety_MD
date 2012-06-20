<?
/**
 * Autenticador.class.php
 *
 * Encargado de gestionar las sesiones de usuario.
 *
 * @author 	Paulo Cesar Coronado
 * @version 	1.1.0.1
 * @package 	Kixi
 * @copyright 	Universidad Distrital Francisco Jose de Caldas - Grupo de Trabajo Academico GNU/Linux GLUD
 * @license 	GPL Version 3 o posterior
 *
 */
class Autenticador
{
	/**
	 *
	 * Nombre de la página que se desea verificar
	 * @var String
	 */

	var $pagina;

	/**
	 *
	 * Objeto cuyo atributo $cadena_sql contiene las clausulas sql que requiere Autenticador
	 * @var sesionSql
	 */
	var $miSql;

	var $configurador;
	
	function __construct(){

		$this->configurador=Configurador::singleton();
		require_once($this->configurador->getVariableConfiguracion("raizDocumento")."/framework/BCK/db/Sql.class.php");
		require_once($this->configurador->getVariableConfiguracion("raizDocumento")."/framework/BCK/sesion/sesionSql.class.php");
		require_once($this->configurador->getVariableConfiguracion("raizDocumento")."/framework/BCK/sesion/Sesion.class.php");

		$this->miSql=new sesionSql();

	}

	function especificarPagina($pagina){
		$this->pagina["nombre"]=$pagina;
	}

	private function verificarPagina(){

		$clausulaSQL=$this->miSql->cadenaSql("seleccionarPagina",$this->pagina["nombre"]);

		if($clausulaSQL){

			$registro=$this->configurador->funcion->ejecutarSQL($clausulaSQL,"busqueda");
			$totalRegistros=$this->configurador->funcion->totalRegistros();

			if($totalRegistros>0) {
				$this->pagina["nivel"]=$registro[0][0];
				return true;
			}
		}
			
		return false;
	}


	function autenticacion()
	{

		/**
		 * 1. Obtener el nivel de acceso de la pagina.
		 * Cada página tiene un nivel de acceso que corresponde al id del subsistema al cual pertenece. Un susbsistema
		 * es un conjunto de páginas hipervinculadas cuyo acceso está restringido a los usuarios que tengan el perfil
		 * autorizado.
		 */
		if($this->verificarPagina()){
			$this->verificarSesion();
			return true;
		}
	}


	function verificarSesion(){


		$nueva_sesion=new Sesion();
		$nueva_sesion->especificarNivel($this->pagina["nivel"]);
		$nueva_sesion->especificarEnlace($this->configurador->getVariableConfiguracion("enlace"));
		
		$esta_sesion=$nueva_sesion->verificarSesion();
		if($pagina_nivel!=0)
		{
			if(!$esta_sesion)
			{
				autenticacion::mensaje_error($configuracion);
				exit();
			}

		}
		$nueva_sesion->borrar_sesion_expirada($configuracion);


	}






}
?>
