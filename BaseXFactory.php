<?php

namespace BaseXMS;

use Zend\ServiceManager\FactoryInterface;
use Zend\ServiceManager\ServiceLocatorInterface;

class BaseXFactory implements FactoryInterface
{
	public function createService( ServiceLocatorInterface $serviceLocator )
	{
		$return = new BaseX( $serviceLocator );
		return $return;
	}
}

?>