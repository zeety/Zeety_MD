<?

include_once("WidgetHtml.class.php");

class  FormularioHtml extends WidgetHtml
{
	
	var $configuracion;
	
	public function setConfiguracion($configuracion){
		$this->configuracion=$configuracion;
	}
	
	function recaptcha($atributos){

		require_once($this->configuracion["raiz_documento"].$this->configuracion["clases"]."/recaptcha/recaptchalib.php");
		$publickey =$this->configuracion["captcha_llavePublica"];


		if(isset($atributos["estilo"]) && $atributos["estilo"]!=""){
			$this->cadenaHTML="<div class='".$atributos["estilo"]."'>\n";
		}else{
			$this->cadenaHTML="<div class='recaptcha'>\n";
		}
		$this->cadenaHTML.=recaptcha_get_html($publickey);
		$this->cadenaHTML.="</div>\n";
		return $this->cadenaHTML;
		 
	}


	function marcoFormulario($tipo,$atributos){

		if($tipo=="inicio"){

			if(isset($atributos["estilo"]) && $atributos["estilo"]!=""){
				$this->cadenaHTML="<div class='".$atributos["estilo"]."'>\n";
			}else{
				$this->cadenaHTML="<div class='formulario'>\n";
			}
			$this->cadenaHTML.="<form ";
			$this->cadenaHTML.="enctype='".$atributos["tipoFormulario"]."' ";
			$this->cadenaHTML.="method='".strtolower($atributos["metodo"])."' ";
			$this->cadenaHTML.="action='index.php' ";
			$this->cadenaHTML.="title='Formulario' ";
			$this->cadenaHTML.="name='".$atributos["nombreFormulario"]."'>\n";
			
			
		}else{
			$this->cadenaHTML="</form>\n";
			$this->cadenaHTML.="</div>\n";
		}

		return $this->cadenaHTML;

	}

	function marcoAgrupacion($tipo,$atributos){

		$this->cadenaHTML="";
		if($tipo=="inicio"){
			$this->cadenaHTML="<div class='marcoControles'>\n";
			$this->cadenaHTML.="<fieldset>\n";
			$this->cadenaHTML.="<legend>\n".$atributos["leyenda"]."</legend>\n";
		}else{
			$this->cadenaHTML.="</fieldset>\n";
			$this->cadenaHTML.="</div>\n";

		}

		return $this->cadenaHTML;
	}


	function campoCuadroTexto($atributos){
		if(isset($atributos["estilo"]) && $atributos["estilo"]!=""){
			$this->cadenaHTML="<div class='".$atributos["estilo"]."'>\n";
		}else{
			$this->cadenaHTML="<div class='campoCuadroTexto'>\n";
		}
		if(isset($atributos["etiqueta"]) && $atributos["etiqueta"]!=""){
		$this->cadenaHTML.=$this->etiqueta($atributos);
		};
		if(isset($atributos["dobleLinea"])){
			$this->cadenaHTML.="<br>";
		}
		$atributos["estilo"]="";
		$this->cadenaHTML.=$this->cuadro_texto($this->configuracion, $atributos);
		$this->cadenaHTML.="</div>\n";

		return $this->cadenaHTML;
	}

	function campoEspacio(){

		$this->cadenaHTML="<div class='espacioBlanco'>\n</div>\n";
		return $this->cadenaHTML;

	}
	function campoFecha($atributos){
		if(isset($atributos["estilo"]) && $atributos["estilo"]!=""){
			$this->cadenaHTML="<div class='".$atributos["estilo"]."'>\n";
		}else{
			$this->cadenaHTML="<div class='campoFecha'>\n";
		}
		$this->cadenaHTML.=$this->etiqueta($atributos);
		$this->cadenaHTML.="<div style='display:table-cell;vertical-align:top;float:left;'><span style='white-space:pre;'>  </span>";
		$this->cadenaHTML.=$this->cuadro_texto($this->configuracion, $atributos);
		$this->cadenaHTML.="</div>";
		$this->cadenaHTML.="<div style='display:table-cell;vertical-align:top;float:left;'>";
		$this->cadenaHTML.="<span style='white-space:pre;'>  </span><img src=\"".$this->configuracion["host"].$this->configuracion["site"].$this->configuracion["grafico"]."/calendarito.jpg\" ";
		$this->cadenaHTML.="id=\"imagen".$atributos["id"]."\" style=\"cursor: pointer; border: 0px solid red;\" ";
		$this->cadenaHTML.="title=\"Selector de Fecha\" alt=\"Selector de Fecha\" onmouseover=\"this.style.background='red';\" onmouseout=\"this.style.background=''\" />";
		$this->cadenaHTML.="</div>";
		$this->cadenaHTML.="</div>\n";

		return $this->cadenaHTML;
	}

	function campoMensaje($atributos){

		if(isset($atributos["estilo"])&&$atributos["estilo"]!=""){
			$this->cadenaHTML="<div class='".$atributos["estilo"]."'>\n";
		}else{
			$this->cadenaHTML="<div class='campoMensaje'>\n";
		}

		if(isset($atributos["tamanno"])){
			switch($atributos["tamanno"]){
				case "grande":
					$this->cadenaHTML.="<span class='textoGrande texto_negrita'>".$atributos["mensaje"]."</span>";
					break;

				case "enorme":
					$this->cadenaHTML.="<span class='textoEnorme texto_negrita'>".$atributos["mensaje"]."</span>";
					break;

				case "pequenno":
					$this->cadenaHTML.="<span class='textoPequenno'>".$atributos["mensaje"]."</span>";
					break;

				default:
					$this->cadenaHTML.="<span class='textoMediano'>".$atributos["mensaje"]."</span>";
				break;
			}

		}else{
			$this->cadenaHTML.="<span class='textoMediano texto_negrita'>".$atributos["mensaje"]."</span>";
		}

		if(isset($atributos["linea"])&&$atributos["linea"]==true){
			$this->cadenaHTML.="<hr class='hr_division'>";
		}
		$this->cadenaHTML.="</div>\n";

		return $this->cadenaHTML;
	}

	function campoTextArea($atributos){


		if(isset($atributos["estilo"]) && $atributos["estilo"]!=""){
			$this->cadenaHTML="<div class='".$atributos["estilo"]."'>\n";
		}else{
			$this->cadenaHTML="<div class='campoAreaTexto'>\n";
		}
		//$this->cadenaHTML.="<div class='campoTextoEtiqueta'>\n";
		$this->cadenaHTML.=$this->etiqueta($atributos);
		//$this->cadenaHTML.="\n</div>\n";
		$this->cadenaHTML.="<div class='campoAreaContenido'>\n";
		$this->cadenaHTML.=$this->area_texto($this->configuracion, $atributos);
		$this->cadenaHTML.="\n</div>\n";
		$this->cadenaHTML.="</div>\n";
		return $this->cadenaHTML;
	}

	function campoBoton($atributos){
		if(isset($atributos["estilo"]) && $atributos["estilo"]!=""){
			$this->cadenaHTML="<div class='".$atributos["estilo"]."'>\n";
		}else{
			$this->cadenaHTML="<div class='campoBoton'>\n";
		}

		$this->cadenaHTML.=$this->boton($this->configuracion,$atributos);
		$this->cadenaHTML.="</div>\n";

		return $this->cadenaHTML;
	}

	function campoBotonRadial($atributos){


		if(isset($atributos["estilo"]) && $atributos["estilo"]!=""){
			$this->cadenaHTML="<div class='".$atributos["estilo"]."'>\n";
		}else{
			$this->cadenaHTML="<div class='campoBotonRadial'>\n";
		}
		$this->cadenaHTML.=$this->radioButton($this->configuracion,$atributos);
		$this->cadenaHTML.="\n</div>\n";
		return $this->cadenaHTML;
	}

	function campoCuadroSeleccion($atributos){


		if(isset($atributos["estilo"]) && $atributos["estilo"]!=""){
			$this->cadenaHTML="<div class='".$atributos["estilo"]."'>\n";
		}else{
			$this->cadenaHTML="<div class='campoCuadroSeleccion'>\n";
		}
		$this->cadenaHTML.=$this->checkBox($this->configuracion,$atributos);
		$this->cadenaHTML.="\n</div>\n";
		return $this->cadenaHTML;
	}


	function campoImagen($atributos){

		if(isset($atributos["estilo"]) && $atributos["estilo"]!=""){
			$this->cadenaHTML="<div class='".$atributos["estilo"]."'>\n";
		}else{
			$this->cadenaHTML="<div class='campoImagen'>\n";
		}

		$this->cadenaHTML.="<div class='marcoCentrado'>\n";
		$this->cadenaHTML.="<img src='".$atributos["imagen"]."' ";
		
		if(isset($atributos["etiqueta"]) && $atributos["etiqueta"]!=""){
			$this->cadenaHTML.="alt='".$atributos["etiqueta"]."' ";
		}

		if(isset($atributos["borde"])){
			$this->cadenaHTML.="border='".$atributos["borde"]."' ";
		}else{
			$this->cadenaHTML.="border='0' ";
		}

		if(isset($atributos["ancho"])){
			if($atributos["ancho"]!=""){
				$this->cadenaHTML.="width='".$atributos["ancho"]."' ";
			}			
		}else{
			$this->cadenaHTML.="width='200px' ";
		}

		if(isset($atributos["alto"])){
			if($atributos["ancho"]!=""){
				$this->cadenaHTML.="height='".$atributos["alto"]." ";
			}
			
		}else{
			$this->cadenaHTML.="height='200px' ";
		}
		$this->cadenaHTML.=" />";
		$this->cadenaHTML.="</div>\n";
		$this->cadenaHTML.="</div>\n";
		return $this->cadenaHTML;
	}

	function campoCuadroLista($atributos){


		$this->cadenaHTML="<div class='campoCuadroLista'>\n";
		$this->cadenaHTML.=$this->etiqueta($atributos);
		$this->cadenaHTML.=$this->cuadro_lista($this->configuracion,$atributos);
		$this->cadenaHTML.="</div>\n";

		return $this->cadenaHTML;
	}

	function campoTexto($atributos){


		if(isset($atributos["estilo"]) && $atributos["estilo"]!=""){
			$this->cadenaHTML="<div class='".$atributos["estilo"]."'>\n";
		}else{
			$this->cadenaHTML="<div class='campoTexto'>\n";
		}

		$this->cadenaHTML.="<div class='campoTextoEtiqueta'>\n";
		$this->cadenaHTML.=$atributos["etiqueta"];
		$this->cadenaHTML.="\n</div>\n";
		$this->cadenaHTML.="<div class='campoTextoContenido'>\n";
		if($atributos["texto"]!=""){
			$this->cadenaHTML.=nl2br($atributos["texto"]);
		}else{
			$this->cadenaHTML.="--";
		}
		$this->cadenaHTML.="\n</div>\n";
		$this->cadenaHTML.="\n</div>\n";

		return $this->cadenaHTML;
	}
	
	function campoMensajeEtiqueta($atributos){
	
	
		if(isset($atributos["estilo"]) && $atributos["estilo"]!=""){
			$this->cadenaHTML="<div class='".$atributos["estilo"]."'>\n";
		}else{
			$this->cadenaHTML="<div class='campoMensajeEtiqueta'>\n";
		}
	
		if(isset($atributos["estiloEtiqueta"]) && $atributos["estiloEtiqueta"]!=""){
			$this->cadenaHTML.="<div class='".$atributos["estiloEtiqueta"]."'>\n";
		}else{
			$this->cadenaHTML.="<div class='campoEtiquetaMensaje'>\n";
		}
		$this->cadenaHTML.=$atributos["etiqueta"];
		$this->cadenaHTML.="\n</div>\n";
		
		if(isset($atributos["estiloContenido"]) && $atributos["estiloContenido"]!=""){
			$this->cadenaHTML.="<div class='".$atributos["estiloContenido"]."'>\n";
		}else{
			$this->cadenaHTML.="<div class='campoContenidoMensaje'>\n";
		}
		if($atributos["texto"]!=""){
			$this->cadenaHTML.=nl2br($atributos["texto"]);
		}else{
			$this->cadenaHTML.="--";
		}
		$this->cadenaHTML.="\n</div>\n";
		$this->cadenaHTML.="\n</div>\n";
	
		return $this->cadenaHTML;
	}


	function campoMapa($atributos){

		$this->cadenaCampoMapa="<div class='campoMapaEtiqueta'>\n";
		$this->cadenaCampoMapa.=$atributos["etiqueta"];
		$this->cadenaCampoMapa.="</div>\n";
		$this->cadenaCampoMapa.="<div class='campoMapa'>\n";
		$this->cadenaCampoMapa.=$this->division("inicio",$atributos);
		$this->cadenaCampoMapa.=$this->division("fin",$atributos);
		$this->cadenaCampoMapa.="\n</div>\n";

		return $this->cadenaCampoMapa;
	}

	function division($tipo,$atributos){

		$this->cadenaHTML="";
		if($tipo=="inicio"){
			if(isset($atributos["estilo"])){
				$this->cadenaHTML="<div class='".$atributos["estilo"]."' ";
			}else{
				$this->cadenaHTML="<div ";
			}
			
			if(isset($atributos["estiloEnLinea"]) && $atributos["estiloEnLinea"]!=""){
				$this->cadenaHTML.="style='".$atributos["estiloEnLinea"]."' ";
			}
			
			$this->cadenaHTML.="id='".$atributos["id"]."' ";
			//$this->cadenaHTML.="name='".$atributos["id"]."' ";
			$this->cadenaHTML.=">\n";

		}else{

			$this->cadenaHTML.="\n</div>\n";

		}

		return $this->cadenaHTML;
	}
	
	function enlace($atributos){
		$this->cadenaHTML="";
		$this->cadenaHTML.="<a ";
		
		if(isset($atributos["id"])){
			$this->cadenaHTML.="id='".$atributos["id"]."' ";
		}
		
		if(isset($atributos["enlace"])){
			$this->cadenaHTML.="href='".$atributos["enlace"]."' ";
		}
		
		if(isset($atributos["tabIndex"])){
			$this->cadenaHTML.="tabindex='".$atributos["tabIndex"]."' ";
		}
		
		if(isset($atributos["estilo"]) && $atributos["estilo"]!=""){
			$this->cadenaHTML.="class='".$atributos["estilo"]."' ";
		}
		$this->cadenaHTML.=">\n";
		if(isset($atributos["enlaceTexto"])){
			$this->cadenaHTML.=$atributos["enlaceTexto"];
		}
		$this->cadenaHTML.="</a>\n";
		
		return $this->cadenaHTML;
		
	}

}//Fin de la clase FormularioHtml
?>
