<?php

namespace BaseXMS;

use BaseXMS\Stdlib\DOMDocument;

use Zend\Permissions\Rbac\Role;

class User
{
	private $id;
	
	public function __construct( $id )
	{
		$this->id = $id;
	}
	
	public function getRole()
	{
		// Build PermissionXML role
		$xml =
<<<XML
<policy id="update_content">/node</policy>
XML;
		
		$doc = new DOMDocument();
		$doc->loadXML( $xml );
		
		$roleA = new Role( 'PermissionXML' );
		$roleA->doc = $doc;
		$roleA->addPermission( 'query' );
		
		// Build search filter Role
		$roleSearchFilter = new Role( 'SearchPermissionFilter' );
		$roleSearchFilter->filter = '/@id';
		
		// Group up Roles under the 'User' Role
		$userRole = new Role( 'User' );
		$userRole->addChild( $roleA );
		$userRole->addChild( $roleSearchFilter );
		
		return $userRole;
	}
}

?>