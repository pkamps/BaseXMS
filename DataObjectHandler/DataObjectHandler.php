<?php 

namespace BaseXMS\DataObjectHandler;

use BaseXMS\Stdlib\DOMDocument;
use Zend\Permissions\Rbac\Rbac;
use Zend\ServiceManager\ServiceLocatorAwareInterface;
use Zend\ServiceManager\ServiceLocatorInterface;

class DataObjectHandler implements ServiceLocatorAwareInterface
{
	protected $services;
	protected $rbac;
	
	public function isValid( $doc )
	{
		return $doc->schemaValidateSource( $this->getSchema() );
	}
	
	/* (non-PHPdoc)
	 * @see Zend\ServiceManager.ServiceLocatorAwareInterface::setServiceLocator()
	 */
	public function setServiceLocator( ServiceLocatorInterface $serviceLocator )
	{
		$this->services = $serviceLocator;

		//TODO: Rbac as service?
		$this->rbac = new Rbac();
		
		$user = $this->services->get( 'user' );
		
		$role = $user->getRole();
		
		if( $role )
		{
			$this->rbac->addRole( $role );
		}
	}

	/* (non-PHPdoc)
	 * @see Zend\ServiceManager.ServiceLocatorAwareInterface::getServiceLocator()
	 */
	public function getServiceLocator()
	{
		return $this->services;
	}
}

?>