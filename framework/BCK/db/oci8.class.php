<?
/*
############################################################################
#    UNIVERSIDAD DISTRITAL Francisco Jose de Caldas                        #
#    Copyright: Vea el archivo LICENCIA.txt que viene con la distribucion  #
############################################################################
*/
/***************************************************************************
* @name          oci8.class.php 
* @author        Paulo Cesar Coronado
* @revision      Última revisión 12 de agosto 2008
****************************************************************************
* @subpackage   
* @package	clase
* @copyright    
* @version      0.2
* @author      	Paulo Cesar Coronado
* @link		http://computo.udistrital.edu.co
* @description  Esta clase esta disennada para administrar todas las tareas 
*               relacionadas con la base de datos ORACLE con OCI8.
*
******************************************************************************/


/*****************************************************************************
*Atributos
*
*@access private
*@param  $servidor
*		URL del servidor de bases de datos. 
*@param  $db
*		Nombre de la base de datos
*@param  $usuario
*		Usuario de la base de datos
*@param  $clave
*		Clave de acceso al servidor de bases de datos
*@param  $enlace
*		Identificador del enlace a la base de datos
*@param  $dbms
*		Nombre del DBMS oci8
*@param  $cadena_sql
*		Clausula SQL a ejecutar
*@param  $error
*		Mensaje de error devuelto por el DBMS
*@param  $numero
*		Número de registros a devolver en una consulta
*@param  $conteo
*		Número de registros que existen en una consulta
*@param  $registro
*		Matriz para almacenar los resultados de una búsqueda
*@param  $campo
*		Número de campos que devuelve una consulta
*TO DO    	Implementar la funcionalidad en DBMS ORACLE con OCI8		
*******************************************************************************/

/*****************************************************************************
*Métodos
*
*@access public
*
     * @name db_admin
*	 Constructor. Define los valores por defecto 
     * @name especificar_db
*	 Especifica a través de código el nombre de la base de datos
     * @name especificar_usuario
*	 Especifica a través de código el nombre del usuario de la DB
     * @name especificar_clave
*	 Especifica a través de código la clave de acceso al servidor de DB
     * @name especificar_servidor
*	 Especificar a través de código la URL del servidor de DB
     * @name especificar_dbms
*	 Especificar a través de código el nombre del DBMS
     * @name especificar_enlace
*	 Especificar el recurso de enlace a la DBMS
     * @name conectar_db
*	 Conecta a un DBMS
     * @name probar_conexion
*	 Con la cual se realizan acciones que prueban la validez de la conexión
     * @name desconectar_db
*	 Libera la conexion al DBMS
     * @name ejecutar_acceso_db
*	 Ejecuta clausulas SQL de tipo INSERT, UPDATE, DELETE
     * @name obtener_error
*	 Devuelve el mensaje de error generado por el DBMS
     * @name obtener_conteo_dbregistro_db
*	 Devuelve el número de registros que tiene una consulta
     * @name registro_db
*	 Ejecuta clausulas SQL de tipo SELECT
     * @name obtener_registro_db
*	 Devuelve el resultado de una consulta como una matriz bidimensional
     * @name obtener_error
*	 Realiza una consulta SQL y la guarda en una matriz bidimensional
*
******************************************************************************/

class oci8
{
	/*** Atributos: ***/
	/**
	 * 
	 * @access privado
	 */
	var $servidor;
	var $db;
	var $usuario;
	var $clave;
	var $enlace;
	var $dbsys;
	var $cadena_sql;
	var $error;
	var $numero;
	var $conteo;
	var $registro;
	var $campo;


	/*** Fin de sección Atributos: ***/

	/**
     * @name especificar_db 
	 * @param string nombre_db 
	 * @return void
	 * @access public
	 */

	function especificar_db( $nombre_db )
	{
		$this->db = $nombre_db;
	} // Fin del método especificar_db

	/**
     * @name especificar_usuario 
	 * @param string usuario_db 
	 * @return void
	 * @access public
	 */
	function especificar_usuario( $usuario_db )
	{
		$this->usuario = $usuario_db;
	} // Fin del método especificar_usuario


	/**
     * @name especificar_clave 
	 * @param string nombre_db 
	 * @return void
	 * @access public
	 */
	function especificar_clave( $clave_db )
	{
		$this->clave = $clave_db;
	} // Fin del método especificar_clave

	/**
	 * 
     * @name especificar_servidor
	 * @param string servidor_db 
	 * @return void
	 * @access public
	 */
	function especificar_servidor( $servidor_db )
	{
		$this->servidor = $servidor_db;
	} // Fin del método especificar_servidor

	/**
	 * 
     * @name especificar_dbms
	 *@param string dbms
	 * @return void
	 * @access public
	 */
	
	function especificar_dbsys( $sistema )
	{
		$this->dbsys = $sistema;
	
	} // Fin del método especificar_dbsys

	/**
	 * 
     * @name especificar_enlace
	 *@param resource enlace
	 * @return void
	 * @access public
	 */
	
	function especificar_enlace($enlace )
	{
		if(is_resource($enlace))
		{
			$this->enlace = $enlace;
		}
	} // Fin del método especificar_enlace

	
	/**
	 * 
     * @name obtener_enlace
	 * @return void
	 * @access public
	 */
	
	function obtener_enlace()
	{
		return $this->enlace;
		
	} // Fin del método obtener_enlace
	
	
	/**
	 * 
     * @name conectar_db
	 * @return void
	 * @access public
	 */
	function conectar_db()
	{
		/*echo "<br>Usuario:".$this->usuario;
		echo "<br>Clave:".$this->clave;
		echo "<br>Db:".$this->db;*/
		
		$this->enlace=oci_connect($this->usuario, $this->clave, $this->db);
		
		if($this->enlace)
		{
			return $this->enlace;					
						
		}
		else
		{
			$this->error =oci_error();	
		}
	} // Fin del método conectar_db

	/**
	 * 
     * @name probar_conexion
	 * @return void
	 * @access public
	 */
	function probar_conexion()
	{
		
		if($this->enlace==TRUE)
		{
			return TRUE;
		}
		else
		{
			return FALSE;
		}
		
		
	} // Fin del método probar_conexion

	/**
	 * 
     * @name desconectar_db
	 * @param resource enlace
	 * @return void
	 * @access public
	 */
	function desconectar_db()
	{
		oci_close($this->enlace);
		
	} //Fin del método desconectar_db

	
	/**
     * @name ejecutar_acceso_db	 
	* @param string cadena_sql 
	* @param string conexion_id
	* @return boolean
	* @access public
	 */
	
	function ejecutar_acceso_db($cadena_sql) 
	{
		$cadenaParser = OCIParse($this->enlace,$cadena_sql);
		$busqueda=OCIExecute($cadenaParser);
		if($busqueda)
		{
			return FALSE;
		} 
		else 
		{
			return TRUE;
		}
	}

	/**
     * @name obtener_error	 
	* @param string cadena_sql 
	* @param string conexion_id
	* @return boolean
	* @access public
	 */
	
	function obtener_error()
	{
		
		return $this->error;
	
	}//Fin del método obtener_error

	/**
     * @name registro_db
	* @param string cadena_sql 
	* @param int numero
	* @return boolean
	* @access public
	 */
	function registro_db($cadena_sql,$numero) 
	{
	
		unset($this->registro);	
		if(!is_resource($this->enlace))
		{
			return FALSE;
		}
		//echo "Ejemplo: ".$cadena_sql."<br>";
		$cadenaParser = OCIParse($this->enlace,$cadena_sql);
		$busqueda=OCIExecute($cadenaParser);
		if($busqueda)
		{
			
			$j=0;
			while(OCIFetchInto($cadenaParser, $row, OCI_RETURN_NULLS))
			{
					$a=0;
					$un_campo=0;
					$campos=count($row);
				while ($a<$campos) 
				{
				
					$this->registro[$j][$un_campo] = $row[$a++];					
					$un_campo++;
				}
				$j++;
				
				
							
				//$this->registro[$j][$un_campo] = $salida[$j][$un_campo];
				//echo $this->registro[$j][$un_campo];
				
			}
			
			$this->conteo=$j--;
			//echo $this->conteo;
			@OCIFreeCursor($cadenaParser);
			return $this->conteo;
		}
		else
		{
			unset($this->registro);
			$this->error =oci_error();
			//echo $this->error();
			return 0;
		}
	}// Fin del método registro_db
	
	
	/**
     * @name obtener_registro_db	 
	* @return registro []
	* @access public
	 */

	function obtener_registro_db() 
	{
		if(isset($this->registro))
		{
				
			return $this->registro;
		}
		else
		{			
			return false;	
		}
	}//Fin del método obtener_registro_db
	
	
	/**
     * @name obtener_conteo_db	 
	* @return int conteo
	* @access public
	 */
	function obtener_conteo_db() 
	{
		return $this->conteo;
	
	}//Fin del método obtener_conteo_db	
	

	

/**
     * @name transaccion
	* @return boolean resultado
	* @access public
	 */
	function transaccion($insert,$delete) 
	{
	
		$this->instrucciones=count($insert);
		
		for($contador=0;$contador<$this->instrucciones;$contador++)
		{
			/*echo $insert[$contador];*/
			$acceso=$this->ejecutar_acceso_db($insert[$contador]);
		
			if(!$acceso)
			{
				
				for($contador_2=0;$contador_2<$this->instrucciones;$contador_2++)
				{
					@$acceso=$this->ejecutar_acceso_db($delete[$contador_2]);
					/*echo $delete[$contador_2]."<br>";*/
					}
				return FALSE;
			
				}
			
		}
		return TRUE;
	
	}//Fin del método transaccion


	

	/**
     * @name db_admin
	 *	
	 */
	function __construct($registro)
	{
			$this->servidor = $registro[0][1];		
			$this->db = $registro[0][4];
			$this->usuario = $registro[0][5];		
			$this->clave = $registro[0][6];
			$this->dbsys = $registro[0][7];;
			//$this->enlace=$this->conectar_db();		
	}//Fin del método db_admin
	
	
	function ejecutar_busqueda($cadena_sql)
	{
		$this->registro_db($cadena_sql,0);
		$registro=$this->obtener_registro_db();
		return $registro;
	}
	
	function verificar_variables($variables)
	{
		
		return $variables;
	}
	
	//Funcion para el acceso a las bases de datos
		
	function ejecutarAcceso($cadena_sql,$tipo)
	{
		
		if(!is_resource($this->enlace))
		{
			return FALSE;
		}
			
		if($tipo=="busqueda")
		{
				
			$this->registro_db($cadena_sql,0);
			$esteRegistro=$this->obtener_registro_db();
			return $esteRegistro;
		}
		else
		{
			$resultado=$this->ejecutar_acceso_db($cadena_sql);
			return $resultado;
		}
	}
	

	
}//Fin de la clase db_admin

?>