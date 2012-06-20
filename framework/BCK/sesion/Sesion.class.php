<?



class Sesion
{
	/** Aggregations: */

	/** Compositions: */

	/*** Attributes: ***/

	/**
	 * Miembros privados de la clase
	 * @access private
	 */
	var $conexion_id;
	var $db_sel;
	var $sesion_id;
	var $expiracion;
	var $usuario;
	var $nivel;
	var $registro_sesion;
	
	var $miConfigurador;


	/**
	 * @name sesiones
	 * constructor
	 */
	function __construct()
	{
		$this->miConfigurador=Configurador::singleton();
		
		$this->usuario= 'ANONIMO';
		$this->expiracion=$this->miConfigurador->getVariableConfiguracion("expiracion");
		$this->nivel=0;
	}//Fin del método session

	/**
	 * @name sesiones Verifica la existencia de una sesion válida en la máquina del cliente
	 * @param string nombre_db
	 * @return void
	 * @access public
	 */
	function verificarSesion()
	{
			
		//Verificar si en el cliente existe registrada una cookie que tenga el identificador de la sesion

		$this->sesion_id=$this->numeroSesion();
		//echo $this->sesion_id;
		//Se eliminan las sesiones expiradas
		$borrar=$this->borrarSesionExpirada();
		$resultado=$this->abrirSesion($this->sesion_id);

		/* Detecta errores*/
		if ($resultado == FALSE)
		{
			return FALSE;
		}
		else
		{
			// Si no hubo errores se puede actualizar los valores
			// Update, porque se tiene un identificador
			/*Crear una nueva cookie*/
			setcookie("aplicativo",$this->sesion_id,(time()+$this->expiracion),"/");
			$cadena_sql="UPDATE ";
			$cadena_sql.=$configuracion["prefijo"]."valor_sesion ";
			$cadena_sql.="SET ";
			$cadena_sql.="valor=".(time()+$this->expiracion)." ";
			$cadena_sql.="WHERE ";
			$cadena_sql.="id_sesion='".($this->sesion_id)."' ";
			$cadena_sql.="AND ";
			$cadena_sql.="variable='expiracion'";
			//echo $cadena_sql;
			$this->db_sel = new dbms($this->aplicativo);
			$this->db_sel->especificar_enlace($this->conexion_id);
			$resultado=$this->db_sel->ejecutar_acceso_db($cadena_sql);
			return $resultado;
		}
	}//Fin del método sesion


	/**
	 *@METHOD numero_sesion
	 *
	 * Rescata el número de sesion correspondiente a la máquina
	 * @PARAM sesion
	 * @return valor
	 * @access public
	 */

	function numeroSesion()
	{

		if(isset($_COOKIE["aplicativo"]))
		{
			$this->id_sesion=$_COOKIE["aplicativo"];
		}
		else
		{
			if(isset($_REQUEST["aplicativo"]))
			{
				$this->id_sesion=$_REQUEST["aplicativo"];
			}
			else
			{
				$this->id_sesion='0';
			}

		}
		return $this->id_sesion;

	}/*Fin de la función numero_sesion*/


	/**
	 *@METHOD abrir_sesion
	 *
	 * Busca la sesión en la base de datos
	 * @PARAM sesion
	 * @return valor
	 * @access public
	 */

	function abrirSesion($sesion)
	{
		// Primero se verifica la longitud del parámetro
		 
		if (strlen($sesion) != 32)
		{
			return FALSE;
		}
		else
		{
			// Verifica la validez del id de sesion

			if ($this->caracteres_validos($sesion) != strlen($sesion))
			{
				return FALSE;
			}

			$this->especificar_sesion($sesion);
			// Busca una sesión que coincida con el id del computador y el nivel de acceso de la página
			$this->el_resultado=$this->rescatar_valor_sesion($configuracion,'acceso',$this->sesion_id);
			if($this->el_resultado)
			{
				if($this->el_resultado[0][0]>=$this->nivel)
				{
					$this->el_resultado=$this->rescatar_valor_sesion($configuracion,'expiracion',$this->sesion_id);
					if($this->el_resultado[0][0]>time())
					{
						return TRUE;
					}
					else
					{
						return FALSE;
					}
				}
				else
				{
					return FALSE;
				}
			}
			else
			{
				return FALSE;
			}

		}
			

		 
	} //Final del método abrir_sesion


	/**
	 *@METHOD caracteres_validos
	 *
	 * Verifica que los caracteres en el identificador de sesión sean válidos
	 * @PARAM cadena
	 * @return valor
	 * @access public//Realizar un barrido por la matriz de resultados para comprobar que se tiene los privilegios para la pagina
	 $this->validacion=0;
	 for($this->i=0;$this->i<$this->count;$this->i++)
	 {
	 */


	function caracteresValidos($cadena)
	{
		// Retorna el número de elementos que coinciden con la lista de caracteres
		return strspn($cadena, "abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ0123456789");
	}

	/**
	 *@METHOD especificar_sesion
	 *
	 * Obtiene los valores guardados para una determinad	return FALSE;a sesión de usuario
	 * @return valor
	 * @access public
	 */

	function especificarSesion($sesion_id)
	{

		$this->sesion_id=$sesion_id;

	} // end of member function especificar_sesion


	/**
	 *@METHOD especificar_enlace
	 *
	 * @param conexion_id
	 * @return valor
	 * @access public
	 */

	function especificarEnlace($conexion_id)
	{

		if(is_resource($conexion_id)){
			$this->conexion_id=$conexion_id;
		}

	} //Fin de la función especificar_enlace


	/**
	 *@METHOD especificar_expiracion
	 * @return valor
	 * @access public
	 */

	function especificarExpiracion($expiracion)
	{

		$this->expiracion=$expiracion;

	} //Fin del mètodo especificar_expiracion


	/**
	 *@METHOD especificar_nivel
	 *
	 * @param nivel
	 * @access public
	 */

	function especificarNivel($nivel)
	{
		$this->nivel=$nivel;

	} //Fin de la función especificar_enlace


	/**
	 *@METHOD especificar_usuario
	 * @return valor
	 * @access public
	 */

	function especificarUsuario($usuario)
	{

		$this->usuario=$usuario;

	} //Fin del mètodo especificar_usuario


	/**
	 *@METHOD especificar_usuario
	 * @return valor
	 * @access public
	 */

	function especificarIdUsuario($id_usuario)
	{

		$this->id_usuario=$id_usuario;

	} //Fin del mètodo especificar_usuario



	/**
	 *@METHOD crear_sesion
	 *
	 * Crea una nueva sesión en la base de datos.
	 * @PARAM usuario_aplicativo
	 * @PARAM nivel_acceso
	 * @PARAM expiracion
	 * @PARAM conexion_id
	 * @return boolean
	 * @access public
	 */

	function crearSesion($configuracion,$usuario_aplicativo,$nivel_acceso)
	{


		if($usuario_aplicativo!=""){
			$this->usuario=$usuario_aplicativo;
		}
			
		if($nivel_acceso!=""){
			$this->nivel=$nivel_acceso;
		}

		//Identificador de sesion
		$this->fecha=explode (" ",microtime());
		$this->sesion_id=md5($this->fecha[1].substr ($this->fecha[0],2).rand());
		//secho $this->sesion_id;
		if (strlen($this->sesion_id) != 32)
		{
			return FALSE;
		}
		else
		{
			// Verifica la validez del id de sesion
			if ($this->caracteres_validos($this->sesion_id) != strlen($this->sesion_id))
			{
				return FALSE;
			}
			/**Borra todas las sesiones que existan con el id del computador*/

			if(isset($_COOKIE["aplicativo"]))
			{
				$this->la_sesion=$_COOKIE["aplicativo"];
				$this->terminar_sesion($configuracion,$this->la_sesion);
					
			}
			/*Actualizar la cookie*/
			$tiempo=time()+$this->expiracion;
			//echo $tiempo;
			//echo "La expiracion de las cookies es: ".$this->expiracion."<br>";
			setcookie("aplicativo",$this->sesion_id,(time()+$this->expiracion),"/");
			/*Borrar cookies anteriores
			 setcookie("aplicativo","",time()+60*60*2,"/");*/
			//$cadena_sql = "INSERT INTO ".$configuracion["prefijo"]."valor_sesion ( id_usuario , id_sesion , expiracion , nivel_acceso ) VALUES ('".$this->usuario."', '".$this->sesion_id."',".(time()+$this->expiracion).",".$this->nivel.")";


			//Insertar id_usuario
			$this->resultado=$this->guardar_valor_sesion($configuracion,'id_usuario',$this->id_usuario,$this->sesion_id);
			//Insertar usuario
			$this->resultado=$this->guardar_valor_sesion($configuracion,'usuario',$this->usuario,$this->sesion_id);
			//Insertar expiracion
			$this->resultado=$this->guardar_valor_sesion($configuracion,'expiracion',(time()+$this->expiracion),$this->sesion_id);
			//Insertar nivel de acceso
			$this->resultado=$this->guardar_valor_sesion($configuracion,'acceso',$this->nivel,$this->sesion_id);

			//@todo TAF specific: insertar la empresa a la cual pertenece el usuario
			$this->resultado=$this->guardar_valor_sesion($configuracion,'id_empresa',$this->id_empresa,$this->sesion_id);

			if($this->resultado){

				return $this->sesion_id;

			}else{

				return FALSE;

			}
				
		}

		 
	}//Fin del método crear_sesion




	/**
	 *@METHOD rescatar_valor_sesion
	 * @PARAM variable
	 * @PARAM usuario_aplicativo ??
	 * @return boolean
	 * @access public
	 */


	function rescatarValorSesion($configuracion,$variable)
	{

		if(isset($_COOKIE["aplicativo"]))
		{
			$this->sesion_id=$_COOKIE["aplicativo"];
		}
		else
		{
			return FALSE;
		}

		// Busca la sesión
		$this->cadena_sql="SELECT ";
		$this->cadena_sql.="valor ";
		$this->cadena_sql.="FROM ";
		$this->cadena_sql.=$configuracion["prefijo"]."valor_sesion ";
		$this->cadena_sql.="WHERE ";
		$this->cadena_sql.="id_sesion ='".($this->sesion_id)."' ";
		$this->cadena_sql.="AND ";
		$this->cadena_sql.="variable='".$variable."' ";
		//echo $this->cadena_sql."<br>";

		if(!isset($this->db_sel))
		{
			$this->db_sel = new dbms($configuracion);
			$this->db_sel->especificar_enlace($this->conexion_id);
		}

		$this->resultado=$this->db_sel->registro_db($this->cadena_sql,0);
		$this->count = $this->db_sel->obtener_conteo_db();


		if($this->count>0)
		{
			$this->registro_sesion=$this->db_sel->obtener_registro_db();
			unset($this->db_sel);
			return $this->registro_sesion;
		}
		else
		{
			unset($this->db_sel);
			return FALSE;
		}


	}//Fin del método rescatar_valor_sesion	return FALSE;


	/**
	 *@METHOD guardar_valor_sesion
	 * @PARAM variable
	 * @PARAM valor
	 * @return boolean
	 * @access public
	 */

	function guardarValorSesion($configuracion,$variable,$valor,$sesion)
	{
		$num_args = func_num_args();
		if ($num_args == 0)
		{
			return FALSE;
		}
		else
		{
			 
			if(strlen($sesion)!=32)
			{
				if(isset($_COOKIE["aplicativo"]))
				{
					$this->sesion_id=$_COOKIE["aplicativo"];
				}
				else
				{
					return FALSE;
				}
			}
			else
			{
				$this->sesion_id=$sesion;

			}
			$cadena_sql = "SELECT * FROM ".$configuracion["prefijo"]."valor_sesion WHERE id_sesion = '".($this->sesion_id)."'  AND variable='".$variable."'";
			if(!isset($this->db_sel))
			{
				$this->db_sel = new dbms($configuracion);
				$this->db_sel->especificar_enlace($this->conexion_id);
			}
			$resultado=$this->db_sel->registro_db($cadena_sql,1);
			/*echo $resultado; importante para depurar*/
			$count = $this->db_sel->obtener_conteo_db();

			if($count>0){
				//Si la variable ya existe en la sesión entonces UPDATE
				$cadena_sql="UPDATE ".$configuracion["prefijo"]."valor_sesion SET valor='".$valor."' WHERE id_sesion='".($this->sesion_id)."' AND variable='".$variable."'";
				/*echo $cadena_sql;*/
				$resultado=$this->db_sel->ejecutar_acceso_db($cadena_sql);
					
				return $resultado;
					

			}
			else
			{
				//Si la variable no existe entonces INSERT
				$cadena_sql = "INSERT INTO ".$configuracion["prefijo"]."valor_sesion ( id_sesion,variable,valor) VALUES ('".$this->sesion_id."', '".$variable."','".$valor."' )";
				/*echo $cadena_sql;*/
				$resultado=$this->db_sel->ejecutar_acceso_db($cadena_sql);
				return $resultado;
			}


		}
	}//Fin del método guardar_valor_sesion



	/**
	 *@METHOD borrar_valor_sesion
	 * @PARAM variable
	 * @PARAM valor
	 * @return boolean
	 * @access public
	 */

	function borrarValorSesion($configuracion,$variable,$sesion)
	{
		$num_args = func_num_args();
		 
		if ($num_args == 0)
		{
			return FALSE;
		}
		else
		{
			 

			if(strlen($sesion)!=32)
			{
				if(isset($_COOKIE["aplicativo"]))
				{
					$this->sesion_id=$_COOKIE["aplicativo"];
				}
				else
				{
					return FALSE;
				}
			}
			else
			{
				$this->sesion_id=$sesion;

			}
			if($variable!='TODOS'){
				$cadena_sql = "DELETE FROM ".$configuracion["prefijo"]."valor_sesion WHERE id_sesion='".$this->sesion_id."' AND variable='".$variable."'";
			}else{
				$cadena_sql = "DELETE FROM ".$configuracion["prefijo"]."valor_sesion WHERE id_sesion='".$this->sesion_id."'";

			}



			if(!isset($this->db_sel))
			{
				$this->db_sel = new dbms($configuracion);
				$this->db_sel->especificar_enlace($this->conexion_id);
			}


			$resultado=$this->db_sel->ejecutar_acceso_db($cadena_sql);
			//echo $resultado; importante para depurar
			return $resultado;
		}
		 
	}//Fin del método borrar_valor_sesion

	/**
	 * @name borrar_sesion_expirada
	 * @return void
	 * @access public
	 */
	function borrarSesionExpirada()
	{

		/*Comprobamos que exista*/

		//SELECT para borrar valores de sesión expirados y garantiza integridad referencial
		$this->tiempo=time();
		$cadena_sql = "SELECT ";
		$cadena_sql.="id_sesion, ";
		$cadena_sql.="valor ";
		$cadena_sql.="FROM ";
		$cadena_sql.=$this->miConfigurador->getVariableConfiguracion("prefijo")."valor_sesion ";
		$cadena_sql.="WHERE ";
		$cadena_sql.="variable='expiracion'";
		//echo $cadena_sql;
		if(!isset($this->db_sel)){
			$this->db_sel = new dbms();
			$this->db_sel->especificar_enlace($this->conexion_id);
		}
		$this->db_sel->registro_db($cadena_sql,0);
		$conteo=$this->db_sel->obtener_conteo_db();
		if($conteo>0)
		{
			$registro=$this->db_sel->obtener_registro_db();
			for($i=0;$i<$conteo;$i++)
			{
				if($registro[$i][1]<time())
				{
					$this->sql="DELETE ";
					$this->sql.="FROM ";
					$this->sql.=$this->miConfigurador->getVariableConfiguracion("prefijo")."valor_sesion ";
					$this->sql.="WHERE ";
					$this->sql.="id_sesion='".$registro[$i][0]."'";
					//echo $this->sql."<br>";
					if(!$this->db_sel->ejecutar_acceso_db($this->sql))
					{
						//Si hay un error al borrar el registro retorna FALSE
						return FALSE;
					}
				}
			}
		}


			
	}//Fin del método borrar_sesion_expirada



	/**
	 *
	 * @name terminar_sesion
	 * @return boolean
	 * @access public
	 */
	 
	function terminarSesion($configuracion,$sesion)
	{
		if(strlen($sesion)!=32)
		{
			return FALSE;
		}
		else
		{
			$this->esta_sesion=$sesion;
		}

		$cadena_sql = "DELETE FROM ".$configuracion["prefijo"]."valor_sesion WHERE id_sesion = '".($this->esta_sesion)."'";
		if(!isset($this->db_sel)){
			$this->db_sel = new dbms($configuracion);
			$this->db_sel->especificar_enlace($this->conexion_id);
		}
		$resultado=$this->db_sel->ejecutar_acceso_db($cadena_sql);
		//Borrar las cookies del equipo remoto
		@setcookie("aplicativo","",$this->expiracion,"/");
		return $resultado;


	}//Fin del método terminar_sesion


	function borrarSesion($configuracion,$sesion)
	{
		if(strlen($sesion)!=32)
		{
			return FALSE;
		}
		else
		{
			$this->esta_sesion=$sesion;
		}

		$cadena_sql = "DELETE FROM ".$configuracion["prefijo"]."valor_sesion WHERE id_sesion = '".($this->esta_sesion)."'";
		if(!isset($this->db_sel)){
			$this->db_sel = new dbms($configuracion);
			$this->db_sel->especificar_enlace($this->conexion_id);
		}
		$resultado=$this->db_sel->ejecutar_acceso_db($cadena_sql);
		return $resultado;


	}//Fin del método terminar_sesion


	function especificarClave($clave_aplicativo)
	{
		$this->clave_aplicativo=$clave_aplicativo;
	}


	/**
	 *
	 *
	 * @param string autenticar
	 * @return void
	 * @access public
	 */
	function autenticar($id_conexion)
	{

		if (is_resource($id_conexion))
		{

			$this->cadena_sql='SELECT usuario,clave FROM aplicativo_usuario WHERE usuario="'.$this->usuario_aplicativo.'" AND clave="'.$this->clave_aplicativo.'"';
			if(!isset($this->db_sel)){
				$this->db_sel = new dbms($configuracion);
				$this->db_sel->especificar_enlace($id_conexion);
			}
			$resultado=$this->db_sel->registro_db($this->cadena_sql, "1");
			//echo $resultado; importante para depurar
			$this->count = $this->db_sel->obtener_conteo_db();
			/**Si ha encontrado un registro que coincida con los criterios de búsqueda retorna TRUE de otra forma retorna
			 FALSE**/
				
			if($this->count>0)
			{

				$this->mensaje=true;
				return $this->mensaje;
			}else{

				$this->mensaje=false;
				return $this->mensaje;
			}
		}
	}



	function actualizarSesion($configuracion)
	{
		$resultado = $this->borrar_valor_sesion($configuracion,"id_usuario",$this->sesion_id);
		$resultado &= $this->borrar_valor_sesion($configuracion,"usuario",$this->sesion_id);
		$resultado &= $this->borrar_valor_sesion($configuracion,"acceso",$this->sesion_id);
		$resultado &= $this->borrar_valor_sesion($configuracion,"expiracion",$this->sesion_id);
		//@TAF
		$resultado &= $this->borrar_valor_sesion($configuracion,"id_empresa",$this->sesion_id);


		$resultado &= $this->guardar_valor_sesion($configuracion,"id_usuario",$this->id_usuario,$this->sesion_id);
		$resultado &= $this->guardar_valor_sesion($configuracion,"usuario",$this->usuario,$this->sesion_id);
		$resultado &= $this->guardar_valor_sesion($configuracion,"acceso",$this->nivel,$this->sesion_id);
		$resultado &= $this->guardar_valor_sesion($configuracion,"expiracion",time()+$configuracion["expiracion"],$this->sesion_id);

		//@TAF
		$resultado &= $this->guardar_valor_sesion($configuracion,"id_empresa",$this->id_empresa,$this->sesion_id);

		return $resultado;
	}



}//Fin de la clase sesion

?>
