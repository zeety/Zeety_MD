<?php

class sesionSql extends Sql{

	var $cadena_sql;
	var $miConfigurador;

	function __construct(){

		$this->miConfigurador=Configurador::singleton();
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
				
			case "seleccionarPagina":
				$this->cadena_sql[$indice]="SELECT ";
				$this->cadena_sql[$indice].="nivel ";
				$this->cadena_sql[$indice].="FROM ";
				$this->cadena_sql[$indice].=$this->miConfigurador->getVariableConfiguracion("prefijo")."pagina ";
				$this->cadena_sql[$indice].="WHERE ";
				$this->cadena_sql[$indice].="nombre='".$parametro."' ";
				$this->cadena_sql[$indice].="LIMIT 1";
				break;
		}
	}
}


?>