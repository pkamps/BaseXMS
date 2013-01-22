<?php 

namespace BaseXMS\UiComponent\Head;

use BaseXMS\UiComposer;

class InlineCss extends \BaseXMS\UiComponent\HtmlWidget
{
	public $needsRerender = true;
	
	public function getXml()
	{
		return '<style id="inline-css" type="text/css"></style>';
	}
	
	/**
	 * 
	 */
	public function rerender()
	{
		$data = $this->composer->getSharedData();
		
		if( isset( $data->inlineCss ) && trim( $data->inlineCss ) )
		{
			// get $doc and add text node under the <style> tag
			$doc = $this->composer->getDoc();
			
			$content = $doc->createTextNode( $data->inlineCss );
			
			$xPath = new \DOMXpath( $this->composer->getDoc() );
			
			$styleElement = $xPath->query( '//style[@id="inline-css"]' );
			
			$styleElement->item(0)->appendChild( $content );
		}
		
		$this->needsRerender = false;
	}
}

?>