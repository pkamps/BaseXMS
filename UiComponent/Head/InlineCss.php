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
		
		$cssData = $data->inlineCss;
		
		if( isset( $cssData ) && trim( $cssData ) )
		{
			// get $doc and add text node under the <style> tag
			$doc = $this->composer->getDoc();
			
			$content = $doc->createTextNode( $this->minify( $cssData ) );
			
			$xPath = new \DOMXpath( $this->composer->getDoc() );
			
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