<?php 

namespace BaseXMS\RequestHandler;

class Factory
{

	/**
	 * @param DomDocument $context
	 * @param \BaseXMS\Mvc\SiteAccess $siteAccess
	 * @return Ambigous <NULL, unknown, \BaseXMS\RequestHandler\RequestHandler>
	 */
	public static function factory( $context, \BaseXMS\Mvc\SiteAccess $siteAccess )
	{
		$return = null;

		$services = $siteAccess->getServiceLocator();
		
		// Get Request handler class
		$requestHandlerClass = $context->queryToValue( '//contentclass' ); //TODO: contentclass is not really the right name
		
		if( !$requestHandlerClass )
		{
			$requestHandlerClass = '\BaseXMS\RequestHandler\RequestHandler';
			$services->get( 'log' )->warn( 'No RequestHandler class specified - falling back to default class.' );
		}
		
		if( !class_exists( $requestHandlerClass ) )
		{
			$services->get( 'log' )->warn( 'Could not find RequestHandler class: "' . $requestHandlerClass . '".' );
			$requestHandlerClass = '\BaseXMS\RequestHandler\RequestHandler';
		}

		$services->get( 'log' )->info( 'Loading RequestHandler class: "' . $requestHandlerClass . '".' );
		$return = new $requestHandlerClass;
		
		// couldn't get a valid class
		if( !( $return instanceof RequestHandler ) )
		{
			$services->get( 'log' )->warn( '"'. $requestHandlerClass .'" is not a RequestHandler - falling back to default class.' );
			$return = new RequestHandler();
		}
		
		$return
				->setContext( $context )
				->setServiceLocator( $services )
				->setSiteAccess( $siteAccess );
	
		return $return;
	}
}

?>