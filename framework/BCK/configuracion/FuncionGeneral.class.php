<?

/**
 *  @Descripcion Clase no cohesiva que contiene metodos varios que encapsulan el uso de las clases nucleares
 *
 *  */

class FuncionGeneral{

	var $sesion;
	
	
	/**
	 * 
	 * Arreglo que contiene las variables de configuración
	 * @var String
	 */
	
	var $configuracion;
	
	
	var $recurso;
	
	
	
	function __construct(){
		
	}
	
	

	function especificarConfiguracion($configuracion){
		$this->configuracion=$configuracion;
	}
	
	public function rescatarValorSesion($tipo)
	{
		if(!isset($this->sesion)){
			$this->abrirSesion($this->configuracion);
		}

		//Rescatar el valor de la variable usuario de la sesion
		if($tipo=="id_sesion"){
			$this->registro=$this->rescatarNumeroSesion($this->configuracion);
			return $this->registro;
		}else{
			$this->registro=$this->sesion->rescatar_valor_sesion($this->configuracion,$tipo);
		}


		if($this->registro)
		{
				
			return $this->registro[0][0];
		}
		else
		{
			return FALSE;
		}

	}

	public function rescatarNumeroSesion()
	{
		if(!isset($this->sesion)){
			$this->abrirSesion($this->configuracion);
		}
		$this->numeroSesion=$this->sesion->numero_sesion();
		return $this->numeroSesion;

	}


	public function abrirSesion($nombre="estructura")
	{
		include_once($this->configuracion["raiz_documento"].$this->configuracion["clases"]."/sesion.class.php");
		$this->sesion=new sesiones($this->configuracion);

		if(!isset($this->recurso[$nombre])){
			$this->conectarDB($this->configuracion);
		}

		$this->sesion->especificar_enlace($this->recurso[$nombre]->obtener_enlace());


		$this->esta_sesion=$this->sesion->numero_sesion();

		if (strlen($this->esta_sesion) != 32)
		{
			return false;

		}else{

			return true;
		}
	}


	public function actualizarSesion($nombre="estructura")
	{
		if(!isset($this->sesion)){
			if(!$this->abrirSesion($this->configuracion)){
				echo "Opsss!!!. Imposible abrir una sesi&oacute;n de Trabajo";
				exit;
			}
		}

		if(!isset($this->recurso[$nombre])){
			$this->conectarDB($this->configuracion);
		}

		$this->sesion->especificar_enlace($this->recurso[$nombre]->obtener_enlace());


		$resultado=$this->sesion->actualizarSesion($this->configuracion);

		return false;

	}

	public function limpiarSesion()
	{
		if(!isset($this->sesion)){
			if(!$this->abrirSesion($this->configuracion)){
				echo "Opsss!!!. Imposible abrir una sesi&oacute;n de Trabajo";
				exit;
			}
		}
		$this->sesion->borrar_sesion_expirada($this->configuracion);
		$this->sesion->terminar_sesion($this->configuracion,$this->esta_sesion);

		return true;
	}


	/*-----------Formularios----------------------*/

	public function revisarFormulario($camposAExcluir="", $tagsAExcluir="")
	{
		//		foreach($_REQUEST as $clave=>$valor){
		//                    echo $clave."--->".$valor."<br>";
		//                }


		//Evitar que se ingrese codigo HTML y PHP en las peticiones
		//en la cadena excluir se anotan los campos que se desean excluir de la
		//conversion
		if($excluir!=""){
			$variables=explode("|",$excluir);
		}else{
			$variables[0]="";
		}

		foreach ($_REQUEST as $clave => $valor)
		{
			if(!in_array($clave,$variables)){
				$_REQUEST[$clave]= strip_tags($valor);

			}else{
				if($tagsAExcluir!=""){
					$_REQUEST[$clave]= strip_tags($valor,$tagsAExcluir);
				}
			}
		}
	}

	/*--------------Manejo de Archivos ---------------*/

	public function cargarArchivoServidor($parametro)
	{
		@set_time_limit (0);
		//Cargar el documento en el servidor
		include_once($this->configuracion["raiz_documento"].$this->configuracion["clases"]."/subir_archivo.class.php");

		$subir = new subir_archivo();
		$subir->directorio_carga= $parametro["directorio"];
		$subir->nombre_campo=$parametro["nombreCampo"];
		$subir->tipos_permitidos= explode("|",$parametro["tipoArchivo"]);
			

			
			
		// Maximo tamanno permitido
		//$subir->tamanno_maximo=5000000;
			
		$subir->especial= "[[:space:]]|[\"\*\\\'\%\$\&\@\<\>]";

		$subir->unico=TRUE;
		$subir->permisos=0777;

		$resultado=$subir->cargar();


			
		if($resultado==false)
		{
			$this->errorCarga["noCarga"]="El archivo no pudo ser cargado en el servidor.";
			return false;
		}
		else
		{
			//guardar datos de la carga
			if(isset($subir->log["nombre_archivo"][0]))
			{
				$cargar["nombreArchivo"]=$subir->log["nombre_archivo"][0];
			}
				
			if(isset($subir->log["mi_archivo"][0]))
			{
				$cargar["nombreInterno"]=$subir->log["mi_archivo"][0];
			}
			//Obtener direccion IP
			$fuentes_ip = array(
			"HTTP_X_FORWARDED_FOR",
			"HTTP_X_FORWARDED",
			"HTTP_FORWARDED_FOR",
			"HTTP_FORWARDED",
			"HTTP_X_COMING_FROM",
			"HTTP_COMING_FROM",
			"REMOTE_ADDR",
			);

			foreach ($fuentes_ip as $fuentes_ip)
			{
				// Si la fuente existe captura la IP
				if (isset($_SERVER[$fuentes_ip])) {
					$proxy_ip = $_SERVER[$fuentes_ip];
					break;
				}
			}

			$cargar["ip"] = (isset($proxy_ip)) ? $proxy_ip : @getenv("REMOTE_ADDR");
			return $cargar;
		}
			

	}
	/*---------------------Bases de Datos ----------------------------------*/


	public function conectarDB($nombre="")
	{

		include_once($this->configuracion["raizDocumento"]."/framework/BCK/db/FabricaDbConexion.class.php");

		$this->conexion=new FabricaDbConexion($this->configuracion);

		if($nombre==""){
			$nombre="estructura";
		}

		$this->recurso[$nombre]=$this->conexion->recursodb($this->configuracion,$nombre);
		//$this->recurso[$nombre]->conectar_db();
		return true;

	}

	public function verificarVariables($valor, $nombre="estructura")
	{
		if(!isset($this->recurso[$nombre])){
			$this->conectarDB($this->configuracion,$nombre);
		}
		$valor=$this->recurso[$nombre]->verificar_variables($valor);
		return $valor;
	}



	public function ejecutarSQL($cadena_sql, $tipo="busqueda",$nombre="estructura")
	{

		//Para manejar varias conexiones a bases de datos, los recursos se guardan en una matriz
		if(!isset($this->recurso[$nombre])){
			$this->conectarDB($nombre);
		}

		$this->resultado=$this->recurso[$nombre]->ejecutarAcceso($this->configuracion,$cadena_sql,$tipo);

		if($this->resultado==false){
			if($this->configuracion["debugMode"]==1){
				echo "<h1>Oopss!!!!</h1>DB Transaction check error!!!<br>";
				echo $cadena_sql."<br>";
				echo $tipo."<br>";
				echo "El error reportado es: ".$this->recurso[$nombre]->obtener_error();

			}
		}

		return $this->resultado;
	}

	public function totalRegistros($nombre="estructura")
	{
		return $this->recurso[$nombre]->obtener_conteo_db();
	}

	public function ultimoInsertado($nombre="estructura")
	{
		return $this->recurso[$nombre]->ultimo_insertado();
	}

	public function totalRegistrosAfectados($nombre="estructura")
	{
		return $this->recurso[$nombre]->registros_afectados();
	}


	function mensaje_error($atributos)
	{?>
<link
	rel='stylesheet' type='text/css'
	href='<? echo $this->configuracion["host"].$this->configuracion["site"].$this->configuracion["estilo"]."/basico/estiloGeneral.php" ?>' />
<div class="marcoMensajeSolo">
	<div class="menuLateral">
		<div class="encabezadoMensaje">
			<img
				src="<? echo $this->configuracion["host"].$this->configuracion["site"].$this->configuracion["grafico"]?>/importante.png"
				border="0"> <br> <span class="texto_negrita"><?$atributos["mensajeErrorTitulo"]?>
			</span>

		</div>
		<div class="mensajeSoloCuerpo">
		<?$atributos["mensajeErrorCuerpo"]?>
		</div>
	</div>
</div>
		<?
	}



}


?>
