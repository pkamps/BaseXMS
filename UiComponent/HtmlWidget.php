<?php 

namespace BaseXMS\UiComponent;

use BaseXMS\UiComposer;

class HtmlWidget extends UiComponent
{
	/**
	 * @return DOMNode
	 */
	public function render( UiComposer $composer, $element )
	{
		/*
		 * page CSS
		 */
		
		// Adding CSS
		$data = $composer->getSharedData();
		$data->inlineCss .= $this->getCss( $composer );
		
		/*
		 * replace include tag with Widget XML
		 */
		//TODO: add function getNode()
		$xml = $this->getXml( $composer );
		$doc = new \DOMDocument();
		$doc->loadXML( $xml );
		
		return $doc->firstChild;
	}
	
	public function getXml( UiComposer $composer )
	{
		return '<span>Default from HtmlWidget</span>';
	}

	public function getCss( UiComposer $composer )
	{
		return '';
	}
}

?>