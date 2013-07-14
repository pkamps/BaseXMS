<?php 

namespace BaseXMS\UiComposer\UiComponent\Head;

use BaseXMS\UiComposer\UiComposer;
use BaseXMS\UiComposer\UiComponent\HtmlWidget;
use BaseXMS\FC;

class CssFile extends HtmlWidget
{
	public $needsRerender = true;
	
	public function getXml()
	{
		return '<link id="css-file" rel="stylesheet" type="text/css" href="" />';
	}
	
	/**
	 * 
	 */
	public function rerender()
	{
		$cssFileContent = '';
		$components = $this->uiComposer->getUiComponents();
		
		//Get all CSS file content
		foreach( $components as $component )
		{
			//TODO: check renderresult instead of component type
			if( $component instanceof HtmlWidget )
			{
				$cssFileContent .= $component->getRenderResult()->cssFileContent;
			}
		}
		
		if( trim( $cssFileContent ) )
		{
			// build file
			$cssFileContent = $this->minify( $cssFileContent );
			
			$httpFileNamePath = '/generated/' . md5( $cssFileContent ) . '.css';
			$fileNamePath = 'public' . $httpFileNamePath;

			if( !file_exists( $fileNamePath ) )
			{
				file_put_contents(  $fileNamePath, $cssFileContent );
			}
			
			// get $doc and add the href
			$doc = $this->uiComposer->getDoc();
			
			//TODO: use composer xpath?
			$xPath = new \DOMXpath( $this->uiComposer->getDoc() );
				
			$linkElement = $xPath->query( '//link[@id="css-file"]' );
			
			//TODO url function missing
			$linkElement->item(0)->setAttribute( 'href', FC::assetLink( $httpFileNamePath ) );
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