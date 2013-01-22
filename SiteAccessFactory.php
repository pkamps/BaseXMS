<?php 

namespace BaseXMS;

use Zend\ServiceManager\ServiceLocatorInterface;

class SiteAccessFactory
{
	public static function factory( ServiceLocatorInterface $serviceLocator, $context = '' )
	{
		$appConfig = $serviceLocator->get( 'application' )->getConfig();
		$siteaccesses = isset( $appConfig[ 'siteaccesses' ] ) ? $appConfig[ 'siteaccesses' ] : array();
		
		$class = '\BaseXMS\SiteAccess';
		
		if( !empty( $siteaccesses ) )
		{
			if( isset( $siteaccesses[ $context ] ) )
			{
				if( class_exists( $siteaccesses[ $context ] ) )
				{
					$class = $siteaccesses[ $context ];
				}
				else
				{
					$serviceLocator->get( 'log' )->warn( 'Unable to load siteaccess: ' . $class );
				}
			}
			else
			{
				$serviceLocator->get( 'log' )->warn( 'Unkown siteaccess context: ' . $context );
			}
		}
		
		$serviceLocator->get( 'log' )->info( 'Loading siteaccess: ' . $class );
		
		$instance = new $class;
		$instance->init( $serviceLocator );
		
		return $instance;
	}
}

?>