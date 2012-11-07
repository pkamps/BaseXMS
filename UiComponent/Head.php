<?php 

namespace BaseXMS\UiComponent;

class Head extends UiComponent
{
	public function getXml()
	{
		$content =
'<head>
	<title>My page title</title>
</head>';
		
		$doc = new \DOMDocument();
		$doc->loadXML( $content );
		
		return $doc->firstChild;
	}
}

?>