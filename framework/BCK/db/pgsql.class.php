<?
/*
############################################################################
#    UNIVERSIDAD DISTRITAL Francisco Jose de Caldas                        #
#    Copyright: Vea el archivo LICENCIA.txt que viene con la distribucion  #
############################################################################
*/
/***************************************************************************
* @name          pgsql.class.php 
* @author        Paulo Cesar Coronado
* @revision      Última revisión 02 de abril de 2008
****************************************************************************
* @subpackage   
* @package	clase
* @copyright    
* @version      0.2
* @author      	Paulo Cesar Coronado
* @link			http://computo.udistrital.edu.co
* @description  Esta clase esta disennada para administrar todas las tareas 
*               relacionadas con la base de datos POSTGRESQL.
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
*		Nombre del DBMS POSTGRES
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
*TO DO    	Implementar la funcionalidad en DBMS POSTGRESQL		
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

class pgsql
{
	/*** Atributos: ***/
	/**
	 * 
	 * @access privado
	 */
	var $servidor;
	var $puerto;
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
		switch($this->dbsys)
		{
			
			case 'pgsql':
					
				//$db es el nombre de la instancia de la base de datos ORACLE especificada en el tnsnames.ora
				
				$this->enlace=pg_connect($this->servidor, $this->puerto , $this->usuario, $this->clave);
				
				if($this->enlace)
				{

					$base=pgsql_select_db($this->db);	
					if($base)
					{
						return $this->enlace;
					}
					else
					{
						$this->error=mysql_errno();
					}
					
								
				}
				else
				{
					$this->error = mysql_errno();	
				}
					
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
	
	function logger($configuracion,$id_usuario,$evento)
	{
		$this->cadena_sql = "INSERT INTO ";
	 	$this->cadena_sql.= "".$configuracion["prefijo"]."logger ";
	 	$this->cadena_sql.= "( ";
	 	$this->cadena_sql.= "`id_usuario` ,";
		$this->cadena_sql.= " `evento` , ";
		$this->cadena_sql.= "`fecha`  ";
		$this->cadena_sql.= ") ";
		$this->cadena_sql.= "VALUES (";
		$this->cadena_sql.= $id_usuario."," ;
		$this->cadena_sql.= "'".$evento."'," ;
		$this->cadena_sql.= "'".time()."'" ;
		$this->cadena_sql.=")";
		//echo $this->cadena_sql;
	 	$this->ejecutar_acceso_db($this->cadena_sql); 
		unset($this->db_sel);
		return TRUE;	
	
	}
	

	/**
	 * 
     * @name desconectar_db
	 * @param resource enlace
	 * @return void
	 * @access public
	 */
	function desconectar_db()
	{
		mysql_close($this->enlace);
		
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
		if(!mysql_query($cadena_sql,$this->enlace)) 
		{
			$this->error= mysql_errno();
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
		if(!is_resource($this->enlace))
		{
			return FALSE;
		}
		$busqueda=mysql_query($cadena_sql,$this->enlace);
		
		if($busqueda)
		{
			unset($this->registro);
			@$this->campo = mysql_num_fields($busqueda);
			@$this->conteo = mysql_num_rows($busqueda);
			if($numero==0)
			{
				
				$numero=$this->conteo;
			}
			
			for($j=0; $j<$numero; $j++)
			{
				$salida = mysql_fetch_row($busqueda);
				for($un_campo=0; $un_campo<$this->campo; $un_campo++)
				{
					$this->registro[$j][$un_campo] = $salida[$un_campo];
				}
			}
			@mysql_free_result($busqueda);
			return $this->conteo;
		}
		else
		{
			unset($this->registro);
			$this->error =mysql_error();
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

	function ultimo_insertado($enlace)
	{
		return mysql_insert_id($enlace);	
	}

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
	function dbms($configuracion)
	{
		if(isset($configuracion['db_dns']))
		{
			$this->servidor = $configuracion['db_dns'];		
		}
		
		if(isset($configuracion['db_nombre']))
		{
			$this->db = $configuracion['db_nombre'];
		}
		
		if(isset($configuracion['db_usuario']))
		{
			$this->usuario = $configuracion['db_usuario'];		
		}
		
		if(isset($configuracion['db_clave']))
		{
			$this->clave = $configuracion['db_clave'];
		}
		
		if(isset($configuracion['dbsys']))
		{
			$this->dbsys = $configuracion['dbsys'];
		}
		
		$this->enlace=$this->conectar_db();		
	}//Fin del método db_admin
	
	//F
	
	function ejecutar_busqueda($cadena_sql)
	{
		$this->registro_db($cadena_sql,0);
		$registro=$this->obtener_registro_db();
		return $registro;
	}
	
	
	function vaciar_temporales($configuracion,$sesion)
	{
		$this->esta_sesion=$sesion;
		$this->cadena_sql="DELETE ";
		$this->cadena_sql.="FROM ";
		$this->cadena_sql.=$configuracion["prefijo"]."registrado_borrador ";
		$this->cadena_sql.="WHERE ";
		$this->cadena_sql.="identificador<".(time()-3600);
		$this->ejecutar_acceso_db($this->cadena_sql);
		
	}
	
	//Funcion para preprocesar la creacion de clausulas sql;
	function verificar_variables($variables)
	{
		if(is_array($variables))
		{
			foreach ($variables as $key => $value) 
			{
				$variables[$key]=mysql_real_escape_string($value);
			}
		}
		else
		{
			$variables=mysql_real_escape_string($variables);
		}
		
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
