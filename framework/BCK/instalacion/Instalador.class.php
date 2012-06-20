<?php
/**
 * Describe al objeto encargado de instalar el framework/aplicativo.
 *
 * @author	Paulo Cesar Coronado
 * @version	0.0.0.2, 29/12/2011
 * @package 	framework:BCK:instalacion
 * @copyright Universidad Distrital F.J.C
 * @license	GPL Version 3.0 o posterior
 *
 */

class Instalador{


	var $conexion;


	function __construct(){

	}

	function procesarInstalacion(){
		//Esta función se invoca si existe el indice instalador en el arreglo $_REQUEST
		$mensajeError="";
		if($this->revisarFormulario()){

			//Los campos al parecer son válidos:
			//1. determinar si se puede ingresar a la base de datos
			$this->conexion=$this->verificarConexionDB();
			if($this->conexion){

				//2. Verificar si existe un archivo con la estructura de la base de datos
				if($this->verificarEstructura()){

					//3. Crear la estructura en la base de datos
					$this->limpiarDB();
					if($this->crearEstructura()){

						//4. Guardar los datos de configuracion en la DB
						if($this->guardarConfiguracionDB()){

							//5. Guardar los datos en el archivo de configuracion
							if($this->guardarDatosConfiguracion()){
								$this->mostrarMensajeExito();
								unset($_REQUEST);

							}else{
								$mensajeError="No se pudo guardar los datos de configuración!!!\nCambiar permisos de /configuracion/config.inc.php";
							}
						}else{
							$mensajeError="No se pudo guardar los datos de configuración en la base de datos!!!";
						}
							
							
					}else{
						$mensajeError="No se pudo crear la estructura de la base de datos!!!";
					}



				}else{
					$mensajeError="No se encontró la estructura de la base de datos!!!";
				}

			}else{
				$mensajeError="No se puede conectar a la base de datos!!!";
			}


		}else{
			$mensajeError="Los datos recibidos est&aacute;n corruptos!!!";
		}

		if($mensajeError!=""){
			$this->mostrarFormularioDatosConexion($mensajeError);
		}
	}

	function guardarConfiguracionDB(){

		$resultado=true;

		$_REQUEST["instalado"]="true";
		$_REQUEST["debugMode"]="false";
		$_REQUEST["dbPrincipal"]=$_REQUEST["nombre"];
		$_REQUEST["hostSeguro"]="https://".substr($_REQUEST["host"],strpos($_REQUEST["host"], "//")+2);



		foreach($_REQUEST as $clave=>$valor)
		{
			if(($clave!="motor")
			&&($clave!="dns")
			&&($clave!="nombre")
			&&($clave!="usuario")
			&&($clave!="clave")
			&&($clave!="instalador")
			)
			{
				if($clave=="raizDocumento")
				{
					$valor.=$_REQUEST["site"];
				}
				$cadena_sql="INSERT INTO ";
				$cadena_sql.=$_REQUEST["prefijo"]."configuracion ";
				$cadena_sql.="(";
				$cadena_sql.="`id_parametro` , ";
				$cadena_sql.="`parametro` , ";
				$cadena_sql.="`valor` ";
				$cadena_sql.=") ";
				$cadena_sql.="VALUES ";
				$cadena_sql.="( ";
				$cadena_sql.="NULL, ";
				$cadena_sql.="'".$clave."',";
				$cadena_sql.="'".$valor."' ";
				$cadena_sql.=")";
				$resultado&=mysql_query($cadena_sql,$this->conexion);
			}
		}

		if($resultado!=TRUE)
		{
			return false;
		}

		return true;

	}


	function guardarDatosConfiguracion(){

		$resultado=true;
		$configuracion=$_REQUEST["raizDocumento"].$_REQUEST["site"]."/configuracion/config.inc.php";
		$fp=@fopen($configuracion,"w+");
		if(!$fp)
		{
			return false;
		}

		$linea="<?php\n/*\n";
		$resultado&=fwrite($fp,$linea);
		$linea=$this->codificar($_REQUEST["motor"]);
		$resultado&=fwrite($fp,$linea."\n");
		$linea=$this->codificar($_REQUEST["dns"]);
		$resultado&=fwrite($fp,$linea."\n");
		$linea=$this->codificar($_REQUEST["nombre"]);
		$resultado&=fwrite($fp,$linea."\n");
		$linea=$this->codificar($_REQUEST["usuario"]);
		$resultado&=fwrite($fp,$linea."\n");
		$linea=$this->codificar($_REQUEST["clave"]);
		$resultado&=fwrite($fp,$linea."\n");
		$linea=$this->codificar($_REQUEST["prefijo"]);
		$resultado&=fwrite($fp,$linea."\n");
		$linea="*/\n?>";
		$resultado&=fwrite($fp,$linea);
		$linea=$this->cuerpoPaginaConfiguracion();
		$resultado&=fwrite($fp,$linea);

		fclose($fp);
		return $resultado;
	}

	private function codificar($texto){
		return AesCtr::encrypt($texto,"", 256);
	}

	function cuerpoPaginaConfiguracion(){

		$cadena="<?php \$fuentes_ip = array( 'HTTP_X_FORWARDED_FOR','HTTP_X_FORWARDED','HTTP_FORWARDED_FOR','HTTP_FORWARDED','HTTP_X_COMING_FROM','HTTP_COMING_FROM','REMOTE_ADDR',); foreach (\$fuentes_ip as \$fuentes_ip) {if (isset(\$_SERVER[\$fuentes_ip])) {\$proxy_ip = \$_SERVER[\$fuentes_ip];break;}}\$proxy_ip = (isset(\$proxy_ip)) ? \$proxy_ip:@getenv('REMOTE_ADDR');?><html><head><title>Acceso no autorizado.</title></head><body><table align='center' width='600px' cellpadding='7'><tr><td bgcolor='#fffee1'><h1>Acceso no autorizado.</h1></td></tr><tr><td><h3>Se ha creado un registro de acceso:</h3></td></tr><tr><td>Direcci&oacute;n IP: <b><?php echo \$proxy_ip ?></b><br>Hora de acceso ilegal:<b> <? echo date('d-m-Y h:m:s',time())?></b><br>Navegador y sistema operativo utilizado:<b><?echo \$_SERVER['HTTP_USER_AGENT']?></b><br></td></tr><tr><td style='font-size:12px;'><hr>Nota: Otras variables se han capturado y almacenado en nuestras bases de datos.<br></td></tr></table></body></html>";
		return $cadena;
	}

	function mostrarMensajeExito(){
		include_once 'exitoInstalacion.page.php';
	}

	function mostrarFormularioDatosConexion($mensajeError=""){

		include_once 'datosConexion.form.php';
	}

	private function revisarFormulario($excluir="")
	{
		foreach ($_REQUEST as $clave => $valor)
		{
			$_REQUEST[$clave]= strip_tags($valor);
		}
		if(isset($_REQUEST["motor"])&&$_REQUEST["motor"]!="" &&
		isset($_REQUEST["dns"])&&$_REQUEST["dns"]!="" &&
		isset($_REQUEST["prefijo"])&&
		isset($_REQUEST["nombre"])&&$_REQUEST["nombre"]!="" &&
		isset($_REQUEST["usuario"])&&$_REQUEST["usuario"]!="" &&
		isset($_REQUEST["clave"])&&$_REQUEST["clave"]!="" &&
		isset($_REQUEST["nombreAplicativo"])&&$_REQUEST["nombreAplicativo"]!="" &&
		isset($_REQUEST["raizDocumento"])&&$_REQUEST["raizDocumento"]!="" &&
		isset($_REQUEST["host"])&&$_REQUEST["host"]!="" &&
		isset($_REQUEST["site"])&&$_REQUEST["site"]!="" &&
		isset($_REQUEST["nombreAdministrador"])&&$_REQUEST["nombreAdministrador"]!="" &&
		isset($_REQUEST["claveAdministrador"])&&$_REQUEST["claveAdministrador"]!="" &&
		isset($_REQUEST["correoAdministrador"])&&$_REQUEST["correoAdministrador"]!="" &&
		isset($_REQUEST["enlace"])&&$_REQUEST["enlace"]!="" &&
		isset($_REQUEST["googlemaps"])&&
		isset($_REQUEST["recatchapublica"])&&
		isset($_REQUEST["recatchaprivada"])&&
		isset($_REQUEST["expiracion"])&&$_REQUEST["expiracion"]!=""){
			return true;
		}
		return false;
	}


	private function verificarConexionDB(){
		$conexion=@mysql_connect($_REQUEST["dns"],$_REQUEST["usuario"],$_REQUEST["clave"]);
		if($conexion){
			$base=mysql_select_db($_REQUEST["nombre"],$conexion);
			if($base){
				return $conexion;
			}
		}

		return false;

	}

	private function verificarEstructura(){

		$arquitectura=$_REQUEST["raizDocumento"].$_REQUEST["site"]."/framework/BCK/instalacion/estructura.sql";
		$arquitecturap=@fopen($arquitectura,"r");


		if(!$arquitecturap)
		{

			return false;

		}else
		{
			fclose($arquitecturap);
			return true;

		}

	}

	private function crearEstructura(){
		$arquitectura=$_REQUEST["raizDocumento"].$_REQUEST["site"]."/framework/BCK/instalacion/estructura.sql";
		$arquitecturap=@fopen($arquitectura,"r");
		$instruccionesSQL=$this->instruccion_sql($arquitecturap);
		$i=0;
		$total=count($instruccionesSQL);
		foreach($instruccionesSQL as $clave=> $cadena_sql)
		{
			$resultado=mysql_query($cadena_sql,$this->conexion);
			if($resultado==TRUE)
			{
				$i++;
			}
		}

		if($total==$i)
		{
			return  true;
		}else{
			return  false;
		}
	}

	private function instruccion_sql( $puntero )
	{
		$instruccion = "";
		$listo = FALSE;
		$indice=0;

		while (!feof( $puntero ))
		{
			$linea = trim(fgets( $puntero, 1024 ));
			$tamanno = strlen( $linea ) - 1;

			//Salta espacios en blanco
			if ( $tamanno < 0 )
			{
				continue;
			}

			//Salta comentarios
			if ( ($linea{0}=='-') && ($linea{1}=="-") )
			{
				continue;
			}

			if ( ($linea{$tamanno}==';') && ($linea{$tamanno - 1}!=';'))
			{
				$listo = TRUE;
				$linea = substr( $linea, 0, $tamanno );
			}

			if ( $instruccion != ""  )
			{
				$instruccion .= " ";
			}
			$instruccion .= $linea."\n";

			if ($listo)
			{
				$instruccion = str_replace(";;", ";", $instruccion);
				$instruccion = str_replace("<nombre>", $_REQUEST["prefijo"], $instruccion);
				$instrucciones[$indice++] = $instruccion;

				$instruccion= '';
				$listo = FALSE;
			}
		}
		fclose( $puntero );
		return $instrucciones;

	}

	private function limpiarDB(){
		$cadena_sql = "SHOW TABLES FROM ".$_REQUEST["nombre"];
		if($result = mysql_query($cadena_sql,$this->conexion)){
			while($row = mysql_fetch_row($result)){
				$found_tables[]=$row[0];
			}
				
			$resultado=true;
			if(isset($found_tables)){
				foreach($found_tables as $table_name){
					$sql = "DROP TABLE IF EXISTS ".$_REQUEST["nombre"].".".$table_name;
					$resultado&= mysql_query($sql,$this->conexion);
				}
					
				return $resultado;
			}
			return false;
		}
		else{
			return false;
		}

	}

}

?>