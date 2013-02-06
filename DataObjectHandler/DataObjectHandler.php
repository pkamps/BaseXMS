<?php 

namespace BaseXMS\DataObjectHandler;

use BaseXMS\Stdlib\DOMDocument;

class DataObjectHandler
{
	protected $services;
	
	public function __construct( $services )
	{
		$this->services = $services;
	}	

	public function isValid( $doc )
	{
		return $doc->schemaValidateSource( $this->getSchema() );
	}
}

?>