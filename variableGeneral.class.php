<?php
/**
 * Define los valores predeterminados de variables para el aplicativo.
 * Principalmente define rutas a los archivos que deben ser cargados
 * antes de iniciar y que por lo tanto aún no son accesibles a traves
 * de la matriz $configuracion
 *
 * @author Paulo Cesar Coronado
 * @version 1.0.0.2, 29/12/2011
 */
class variableGeneral{
    
    /**
     *
     * @var string
     */
    var $variableGeneral;
    
    function __construct() {
        
        $this->variableGeneral["rutaFramework"]="framework"; //Relativa al directorio principal de la aplicación        
        $this->variableGeneral["rutaBiblioteca"]="BCK"; //Relativa a rutaFramework
        $this->variableGeneral["rutaConfiguracion"]="configuracion"; //Relativa al directorio principal de la aplicación        
    }
    
    function getVariableGeneral(){
        return $this->variableGeneral;
    }

};
?>
