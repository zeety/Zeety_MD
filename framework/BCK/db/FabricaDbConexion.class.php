<?
/**
* Fabrica de objetos para la familia de conexiones a bases de datos
*
* @author	Paulo Cesar Coronado
* @version	1.0.0.2, 03/04/2012
* @package 	framework::BCK::db
* @copyright Universidad Distrital F.J.C
* @license	GPL Version 3.0 o posterior
*
*/

class FabricaDbConexion
{
	
	var $configuracion;
	
	public function __construct($configuracion)
	{
		$this->configuracion=$configuracion;
	}
	
	public function recursodb($nombre="")
	{
		include_once("dbms.class.php");
		include_once("mysql.class.php");
		include_once("oci8.class.php");
		include_once("pgsql.class.php");
		include_once($this->configuracion["raizDocumento"]."/framework/BCK/criptografia/Encriptador.class.php");
		//include_once("mysqli.class.php");

		$cripto = Encriptador::singleton();
			
		$acceso_db=new dbms($this->configuracion);
		$enlace=$acceso_db->conectar_db();
		
		if (is_resource($enlace))
		{
			if($nombre!="")
			{
				$cadena_sql="SELECT "; 
				$cadena_sql.="`nombre`, ";			 
				$cadena_sql.="`servidor`, "; 
				$cadena_sql.="`puerto`, "; 
				$cadena_sql.="`ssl`, "; 
				$cadena_sql.="`db`, "; 
				$cadena_sql.="`usuario`, "; 
				$cadena_sql.="`password`, ";
				$cadena_sql.="`dbms` "; 
				$cadena_sql.="FROM ";
                $cadena_sql.=$this->configuracion["prefijo"]."dbms ";
				$cadena_sql.="WHERE "; 
				$cadena_sql.="nombre='".$nombre."'";  

                $acceso_db->registro_db($cadena_sql,0);
				$registro=$acceso_db->obtener_registro_db();
				if(is_array($registro))
				{	
					$dbms=$registro[0][7];
					
					if($dbms=='oci8_t'){
						$trunc_ps=new trunc($this->configuracion);
						$registro[0][6]=$trunc_ps->transformar($registro[0][6]);
						$dbms='oci8';
					}

                   //Decodificar la contrasenna
					$registro[0][6]=$crypto->decodificar($registro[0][6]);
					
										
					return new $dbms($registro);
				}
				else
				{
					$dbms=$this->configuracion["db_sys"];
					return new $dbms($this->configuracion);
					
				}
			}
			else
			{
				return $acceso_db;
			}
		}
			
		
		
	}
	
}//Fin de la clase db_admin

?>
