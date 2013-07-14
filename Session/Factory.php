<?php 

namespace BaseXMS\Session;

use Zend\ServiceManager\FactoryInterface;
use Zend\ServiceManager\ServiceLocatorInterface;
use Zend\Session\Container;

class Factory implements FactoryInterface
{
	public function createService( ServiceLocatorInterface $serviceLocator )
	{
		$session = new \Zend\Session\SessionManager();
		$session->start();
		
		$container = new Container( 'initialized' );

		if( !isset( $container->init ) )
		{
			$session->regenerateId( true );
			$container->init = 1;
			$container->userId = 0;
		}
		
		return $session;
	}
}

?>