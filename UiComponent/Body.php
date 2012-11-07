<?php 

namespace BaseXMS\UiComponent;

class Body extends UiComponent
{
	public function getXml()
	{
		$content =
'<body>Hello World.</body>';
		
		$doc = new \DOMDocument();
		$doc->loadXML( $content );
		
		return $doc->firstChild;
	}
}

?>