<?php 

namespace BaseXMS\UiComponent;

class UiComponent
{
	protected $services;
	protected $data;
	
	public function init( $services, $data )
	{
		$this->services = $services;
		$this->data     = $data;
	}
	
	public function render( $format )
	{
		return '<pre>' . print_r( $this->data, true ) . '</pre>';
	}
	
	public function getXml()
	{
		$content =
		'<span>Default from UiComponent</span>';
		
		$doc = new \DOMDocument();
		$doc->loadXML( $content );
		
		return $doc->firstChild;
	}
}

?>