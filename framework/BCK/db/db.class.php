<?	
class db
{

	//Constructor
	function db()
	{
		
	
	}
	
	
	static function acceso($cadena_sql,$acceso_db,$tipo)
	{
	
		if($tipo=="busqueda")
		{
			$resultado=$acceso_db->registro_db($cadena_sql,0);
			if($resultado!=0)
			{
				$registro=$acceso_db->obtener_registro_db();
				return $registro;
			}
			return false;
			
			
		}
		else
		{
			$resultado=$acceso_db->ejecutar_acceso_db($cadena_sql);
			return $resultado;
		}
	
	}
	
	
	
}	
?>
