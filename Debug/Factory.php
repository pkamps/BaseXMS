<?php 

namespace BaseXMS\Debug;

use Zend\ServiceManager\FactoryInterface,
    Zend\ServiceManager\ServiceLocatorInterface;


class Factory implements FactoryInterface
{
	public function createService( ServiceLocatorInterface $serviceLocator )
	{
		return new Accumulator();
	}
}

?>