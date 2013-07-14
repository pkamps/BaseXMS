<?php

namespace BaseXMS;

use Zend\Permissions\Rbac\AssertionInterface;
use Zend\Permissions\Rbac\Rbac;

class AssertXPathMatch implements AssertionInterface
{
	private $accessQuery;
	private $contextDoc;

	public function __construct( $accessQuery, $contextDoc )
	{
		$this->accessQuery = $accessQuery;
		$this->contextDoc  = $contextDoc;
	}

	public function assert( Rbac $rbac )
	{
		$return = false;
		
		$role = $rbac->getRole( 'PermissionXML' );
		
		$accessResult = $role->doc->query( $this->accessQuery );
		
		if( $accessResult->length > 0 )
		{
			$limitationQuery = trim( $accessResult->item(0)->nodeValue );
			
			if( $limitationQuery )
			{
				if( $this->contextDoc instanceof \BaseXMS\Stdlib\DOMDocument )
				{
					$return = $this->contextDoc->query( $limitationQuery )->length > 0;
				}
			}
			else
			{
				$return = true;
			}
		}
		
		return $return;
	}
}

?>