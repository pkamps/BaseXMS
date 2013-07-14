<?php 

namespace BaseXMS\UiComposer\UiComponent\Head;

use BaseXMS\UiComposer\UiComposer;
use BaseXMS\UiComposer\UiComponent\HtmlWidget;
use BaseXMS\FC;

class JsFile extends HtmlWidget
{
	public $needsRerender = true;
	
	public function getXml()
	{
		return '<script id="js-file" type="text/javascript" src="" />';
	}
	
	/**
	 * 
	 */
	public function rerender()
	{
		$fileContent = $this->getFileContent( 'jsFileContent' );
		
		if( trim( $fileContent ) )
		{
			$httpFileNamePath = '/generated/' . md5( $fileContent ) . '.js';
			$fileNamePath = 'public' . $httpFileNamePath;

			if( !file_exists( $fileNamePath ) )
			{
				file_put_contents( $fileNamePath, $fileContent );
			}
			
			// get $doc and add the href
			$doc = $this->uiComposer->getDoc();
			
			//TODO: use composer xpath?
			$xPath = new \DOMXpath( $this->uiComposer->getDoc() );
				
			$linkElement = $xPath->query( '//script[@id="js-file"]' );
			
			$linkElement->item(0)->setAttribute( 'src', FC::assetLink( $httpFileNamePath ) );
		}
		
		$this->needsRerender = false;
	}
	
	protected function getFileContent( $property )
	{
		$return = '';
		$components = $this->uiComposer->getUiComponents();
		
		//Get all CSS file content
		foreach( $components as $component )
		{
			//TODO: check renderresult instead of component type
			if( $component instanceof HtmlWidget )
			{
				$return .= $component->getRenderResult()->$property;
			}
		}
		
		return $return;
	}
	
}

?>