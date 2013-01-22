<?php 
namespace BaseXMS\Stdlib;

class SimpleXMLElement extends \SimpleXMLElement
{
	public function toXML()
	{
		// probably expensive for big xml strings
		return str_replace( '<?xml version="1.0"?>' . "\n", '', $this->asXML() );
	}
}

?>