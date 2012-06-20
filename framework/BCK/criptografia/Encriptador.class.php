<?
/***************************************************************************
 *   PHP Application Framework Version 10                                  *
 *   Copyright (c) 2003 - 2009                                             *
 *   Teleinformatics Technology Group de Colombia                          *
 *   ttg@ttg.com.co                                                        *
 *                                                                         *
****************************************************************************/


class Encriptador{
	
	private static $instance;
	
	
	//Constructor
	function __construct(){
		
	}
	
	
	public static function singleton()
	{
		if (!isset(self::$instance)) {
			$className = __CLASS__;
			self::$instance = new $className;
		}
		return self::$instance;
	}
	
	
	function codificar_url($cadena,$configuracion)
	{
		$cadena= AesCtr::encrypt($cadena,"", 256);
		$cadena=$configuracion["enlace"]."=".$cadena;
		return $cadena;
	
	}
	
	/**
	 * 
	 * Método para decodificar la cadena GET para obtener las variables de la petición
	 * @param $cadena
	 * @return boolean
	 */
	
	function decodificar_url($cadena)
	{
		$cadena=AesCtr::decrypt($cadena,"",256);
		
		parse_str($cadena,$matriz);
		
		foreach($_REQUEST as $clave => $valor) 
		{
			unset($_REQUEST[$clave]);
		} 
		
		foreach($matriz as $clave=>$valor)
		{
			$_REQUEST[$clave]=$valor;			
		}
		
		return true;
	}
	
	function codificar($cadena)
	{
		$cadena= AesCtr::encrypt($cadena,"", 256);
		return $cadena;
	
	}
	
	
	function decodificar($cadena)
	{
		$cadena=AesCtr::decrypt($cadena,"",256);		
		return $cadena;
	
	
	}
	
}//Fin de la clase

?>
