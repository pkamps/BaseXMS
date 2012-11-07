<?php 

namespace BaseXMS\Log;

use Zend\ServiceManager\FactoryInterface,
    Zend\ServiceManager\ServiceLocatorInterface;
use Zend\Log\Writer\Null;

class Factory implements FactoryInterface
{
	public function createService( ServiceLocatorInterface $serviceLocator )
	{
		$l = new Logger();
		
		if( $serviceLocator->has( 'application' ) )
		{
			$eventManager = $serviceLocator->get( 'application' )->getBaseXMSEventManager();
			$eventManager->trigger( 'AddLogWriter', $this, $l );
		}
		
		if( $l->getWriters()->isEmpty() )
		{
			$l->addWriter( new Null() );
		}
		
		return $l;
	}
}

?>