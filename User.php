<?php

namespace BaseXMS;

use Zend\Permissions\Rbac\Role;

class User
{
	public function getRole()
	{
		$roleA = new Role( 'ContentObject' );
		$roleA->limitations[ 'update' ] = '/node';
		$roleA->addPermission( 'update' );
		
		return $roleA;
	}
}

?>