<?php

include_once $this->miConfigurador->configuracion["raizDocumento"]."/framework/BCK/db/Sql.class.php";



class guiSql extends Sql{

	var $cadena_sql;
	var $configurador;

	function __construct(){

		$this->configurador=Configurador::singleton();
		return 0;
	}


	function cadenaSql($indice,$parametro){

		$this->clausula($indice, $parametro);
		if(isset($this->cadena_sql[$indice])){
			return $this->cadena_sql[$indice];
		}
		return false;
	}

	private function clausula($indice,$parametro){

		switch ($indice){

			case "usuario":
				$cadena_sql="SELECT  ";
				$cadena_sql.="usuario, ";
				$cadena_sql.="estilo ";
				$cadena_sql.="FROM ";
				$cadena_sql.=$this->miConfigurador->configuracion["prefijo"]."estilo ";
				$cadena_sql.="WHERE ";
				$cadena_sql.="usuario='".$this->id_usuario."'";
				break;

			case "pagina":
				$cadena_sql="SELECT  ";
				$cadena_sql.=$this->miConfigurador->configuracion["prefijo"]."bloque_pagina.*,";
				$cadena_sql.=$this->miConfigurador->configuracion["prefijo"]."bloque.nombre, ";
				$cadena_sql.=$this->miConfigurador->configuracion["prefijo"]."pagina.parametro ";
				$cadena_sql.="FROM ";
				$cadena_sql.=$this->miConfigurador->configuracion["prefijo"]."pagina, ";
				$cadena_sql.=$this->miConfigurador->configuracion["prefijo"]."bloque_pagina, ";
				$cadena_sql.=$this->miConfigurador->configuracion["prefijo"]."bloque ";
				$cadena_sql.="WHERE ";
				$cadena_sql.=$this->miConfigurador->configuracion["prefijo"]."pagina.nombre='".$this->id_pagina."' ";
				$cadena_sql.="AND ";
				$cadena_sql.=$this->miConfigurador->configuracion["prefijo"]."bloque_pagina.id_bloque=".$this->miConfigurador->configuracion["prefijo"]."bloque.id_bloque ";
				$cadena_sql.="AND ";
				$cadena_sql.=$this->miConfigurador->configuracion["prefijo"]."bloque_pagina.id_pagina=".$this->miConfigurador->configuracion["prefijo"]."pagina.id_pagina";
				break;


					
		}
	}
}


?>