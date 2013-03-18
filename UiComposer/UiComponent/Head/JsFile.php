<?php 

namespace BaseXMS\UiComposer\UiComponent\Head;

use BaseXMS\UiComposer\UiComposer;
use BaseXMS\UiComposer\UiComponent\HtmlWidget;

class JsFile extends HtmlWidget
{
	public $needsRerender = true;
	
	public function getXml()
	{
		return
<<<HTML
<div id="jsfiles" />

<script type="text/javascript">
$(function()
{
	tinyMCE.init(
	{
		mode     : 'textareas',
		encoding : 'xml'
	});
});
</script>
HTML;
	}
	
	/**
	 * 
	 */
	public function rerender()
	{
		$jsFiles = array();
		$components = $this->uiComposer->getUiComponents();
		
		//Get js files
		foreach( $components as $component )
		{
			if( $component instanceof HtmlWidget )
			{
				$jsFiles = array_merge( $jsFiles, $component->getRenderResult()->jsFiles );
			}
		}
		
		$jsFilesHtml = '';
		if( !empty( $jsFiles ) )
		{
			foreach( $jsFiles as $file )
			{
				$jsFilesHtml .= '<script type="text/javascript" src="'. $file .'" charset="utf-8" />';
			}
			
			// get $doc and add text node under the <style> tag
			$doc = $this->uiComposer->getDoc();
			
			$fragment = $doc->createDocumentFragment();
			$fragment->appendXML( $jsFilesHtml );
			
			$xpath = new \DOMXPath( $doc );
			$containerElement = $xpath->query( '//*[@id="jsfiles"]' );
			$containerElement = $containerElement->item(0);
			
			$containerElement->appendChild( $fragment );
		}
		
		$this->needsRerender = false;
	}
}

?>