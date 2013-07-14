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
		
		// Let modules add a log writer
		if( $serviceLocator->has( 'ModuleManager' ) )
		{
			$serviceLocator->get( 'ModuleManager' )->getEventManager()->trigger( 'AddLogWriter', $this, $l );
		}
		
		if( $l->getWriters()->isEmpty() )
		{
			$l->addWriter( new Null() );
		}
		\Zend\Log\Logger::registerErrorHandler( $l );

		return $l;
	}
}

?>