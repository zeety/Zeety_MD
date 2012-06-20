<?php

/**
 * index.php
 * 
 * Punto de entrada al aplicativo. Crea un objeto de variableGeneral para poder inicializar
 * las rutas generales que permiten referenciar a la clase Incializador cuyo objeto se contituye
 * en el controlador primario de la aplicación.
 *  
 * 
 * @author Paulo Cesar Coronado
 * @copyright Universidad Distrital Francisco Jose de Caldas
 * @license GPL v3 o posterior
 * @version 1.0.0.3, 29/12/2012 
 * 
 */

/**
 * Unico archivo que se incluye utilizando una ruta predeterminada
 */
require_once("variableGeneral.class.php");


class Aplicacion{

    /**
     * Objeto. Para gestionar las variables de inicio del aplicativo
     * @var variableGeneral
     */
    var $miVariable;
    
    /**
     * Arreglo. Contiene las rutas donde se encuentran los archivos del aplicativo.
     * @var string
     */
    var $variablesGenerales;
    
    /**
     * Objeto. Se encarga de las tareas preliminares que se requieren para lanzar la aplicación.
     *  
     * @var Inicializador
     */
    var $miInicializador;

    function __construct() {
    
        $GLOBALS["configuracion"] = TRUE;
        $this->miVariable = new variableGeneral();
        $this->variablesGenerales = $this->miVariable->getVariableGeneral();
        
        $rutaBiblioteca=$this->variablesGenerales["rutaFramework"]."/". $this->variablesGenerales["rutaBiblioteca"];
        $rutaClasesConfiguracion=$rutaBiblioteca."/".$this->variablesGenerales["rutaConfiguracion"];
        
        /*
         * Una vez que se han cargado las rutas del aplicativo se pueden incluir las clases principales.
         */
        
        require_once($rutaClasesConfiguracion."/Inicializador.class.php");
        
        $this->miInicializador = new Inicializador();
        
        $this->miInicializador->setMisVariables($this->variablesGenerales);
        
        $this->miInicializador->iniciar();
    }

};

/**
 * Iniciar la aplicacion.
 */
$miAplicacion = new Aplicacion();
?>