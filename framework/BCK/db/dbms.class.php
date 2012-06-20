<?php 
class dbms
{
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
	
	var $miConfigurador;


	function especificar_db( $nombre_db )
	{
		$this->db = $nombre_db;
	}


        function especificar_usuario( $usuario_db )
	{
		$this->usuario = $usuario_db;
	}


        function especificar_clave( $clave_db )
	{
		$this->clave = $clave_db;
	}

	function especificar_servidor( $servidor_db )
	{
		$this->servidor = $servidor_db;
	}

	function especificar_dbsys( $sistema )
	{
		$this->dbsys = $sistema;
	
	}

        function especificar_enlace($enlace )
	{
		if(is_resource($enlace))
		{
			$this->enlace = $enlace;
		}
	}

	function obtener_enlace()
	{
		return $this->enlace;
		
	}


	function conectar_db()
	{
		switch($this->dbsys)
		{
			
			case 'mysql':
					
				$this->enlace=mysql_connect($this->servidor, $this->usuario, $this->clave);
				
				if($this->enlace)
				{
					$base=mysql_select_db($this->db);	
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
	}

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
		
		//echo $cadena_sql."<br>";
		$busqueda=mysql_query($cadena_sql,$this->enlace);

                //echo $busqueda."<br>";
		
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
				$salida = mysql_fetch_array($busqueda);

                                if(is_array($salida)){
                                    if($j==0){
                                        $this->keys=array_keys($salida);
                                        $i=0;
                                        foreach($this->keys as $clave=>$valor){
                                            if(is_string($valor)){
                                                $this->claves[$i++]=$valor;
                                            }
                                           //echo $clave."->".$valor."<br>";
                                        }
                                    }

                                    for($un_campo=0; $un_campo<$this->campo; $un_campo++)
                                    {
                                            $this->registro[$j][$un_campo] = $salida[$un_campo];
                                            $this->registro[$j][$this->claves[$un_campo]] = $salida[$un_campo];
                                    }
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

	function ultimo_insertado()
	{
		return mysql_insert_id($this->enlace);	
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
	function __construct(){
		
		
		
		
		
		if(isset($configuracion['db_dns'])){
			$this->servidor = $configuracion['db_dns'];		
		}
		
		if(isset($configuracion['db_nombre'])){
			$this->db = $configuracion['db_nombre'];
		}
		
		if(isset($configuracion['db_usuario']))	{
			$this->usuario = $configuracion['db_usuario'];		
		}
		
		if(isset($configuracion['db_clave'])){
			$this->clave = $configuracion['db_clave'];
		}
		
		if(isset($configuracion['db_sys'])){
			$this->dbsys = $configuracion['db_sys'];
		}

		$this->enlace=$this->conectar_db();

	}

	
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
