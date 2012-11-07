<?php 

namespace BaseXMS\UiComponent;

class Html extends UiComponent
{
	public function getXml()
	{
		$content =
'<html>
	<include class="\BaseXMS\UiComponent\Head"></include>
	<include class="\BaseXMS\UiComponent\Body"></include>
</html>';
		
		$doc = new \DOMDocument();
		$doc->loadXML( $content );
		
		return $doc->firstChild;
	}
}

?>