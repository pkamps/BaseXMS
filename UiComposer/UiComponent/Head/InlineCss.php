<?php 

namespace BaseXMS\UiComposer\UiComponent\Head;

use BaseXMS\UiComposer\UiComposer;
use BaseXMS\UiComposer\UiComponent\HtmlWidget;

class InlineCss extends HtmlWidget
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
		$cssData = '';
		$components = $this->uiComposer->getUiComponents();
		
		//Get all embed CSS
		foreach( $components as $component )
		{
			if( $component instanceof HtmlWidget )
			{
				$cssData .= $component->getRenderResult()->embedCss;
			}
		}
		
		if( trim( $cssData ) )
		{
			// get $doc and add text node under the <style> tag
			$doc = $this->uiComposer->getDoc();
			
			$content = $doc->createTextNode( $this->minify( $cssData ) );
			
			//TODO: use composer xpath?
			$xPath = new \DOMXpath( $this->uiComposer->getDoc() );
			
			$styleElement = $xPath->query( '//style[@id="inline-css"]' );
			
			$styleElement->item(0)->appendChild( $content );
		}
		
		$this->needsRerender = false;
	}
	
	private function minify( $input )
	{
		$regex = array(
				"`^([\t\s]+)`ism"=>'',
				"`([:;}{]{1})([\t\s]+)(\S)`ism"=>'$1$3',
				"`(\S)([\t\s]+)([:;}{]{1})`ism"=>'$1$3',
				"`\/\*(.+?)\*\/`ism"=>"",
				"`([\n|\A|;]+)\s//(.+?)[\n\r]`ism"=>"$1\n",
				"`(^[\r\n]*|[\r\n]+)[\s\t]*[\r\n]+`ism"=>"\n"
		);
		return preg_replace( array_keys( $regex ), $regex, $input );
	}
}

?>