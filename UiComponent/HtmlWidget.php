<?php 

namespace BaseXMS\UiComponent;

use BaseXMS\UiComposer;

class HtmlWidget extends UiComponent
{
	/**
	 * @return DOMNode
	 */
	public function render( \DOMDocumentFragment $fragment )
	{
		/*
		 * page CSS
		 */
		
		// Adding CSS
		$data = $this->composer->getSharedData();
		$data->inlineCss .= $this->getCss( $this->composer );
		
		/*
		 * replace include tag with Widget XML
		 */
		//TODO: add function getNode()
		$xml = $this->getXml();

		$fragment->appendXML( $xml );
		
		return $this;
		
		// dead code
		if( !$doc )
		{
			$this->composer->getServices()->get( 'log' )->err( 'XML parse errors in "' . get_class( $this ) . '"' );
			return null;
		}
		else
		{
			return $doc->firstChild;
		}
	}
	
	public function getXml()
	{
		return '<span>Default from HtmlWidget</span>';
	}

	public function getCss()
	{
		return '';
	}
}

?>