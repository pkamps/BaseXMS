<?php 

namespace BaseXMS\DataObjectHandler;

class DataObjectHandler
{
	protected $services;
	
	public function __construct( $services )
	{
		$this->services = $services;
	}	

	public function isValid( $node )
	{
		$doc = $this->simpleXmlToDomDocument( $node );
		return $doc->schemaValidateSource( $this->getSchema() );
	}
	
	public function simpleXmlToDomDocument( $simpleXmlElement )
	{
		$domnode = dom_import_simplexml( $simpleXmlElement );
		$dom = new \DOMDocument();
		$domnode = $dom->importNode( $domnode, true );
		$dom->appendChild( $domnode );
	
		return $dom;
	}

}

?>