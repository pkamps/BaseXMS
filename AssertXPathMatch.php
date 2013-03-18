<?php

namespace BaseXMS;

use Zend\Permissions\Rbac\AssertionInterface;
use Zend\Permissions\Rbac\Rbac;

class AssertXPathMatch implements AssertionInterface
{
	private $doc;
	private $permission;

	public function __construct( \DOMDocument $doc, $permission )
	{
		$this->doc = $doc;
		$this->permission = $permission;
	}

	public function assert( Rbac $rbac )
	{
		$return = false;

		$permissionParts = explode( '::', $this->permission );
		
		if( count( $permissionParts ) == 2 )
		{
			$role = $rbac->getRole( $permissionParts[0] );
			
			$limitation = $role->limitations[ $permissionParts[1] ];
			
			if( $limitation )
			{
				$xpath = new \DOMXpath( $this->doc );
				$return = $xpath->query( $limitation )->length > 0;
			}
		}
				
		return $return;
	}
}

?>