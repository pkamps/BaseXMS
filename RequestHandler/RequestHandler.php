<?php 

namespace BaseXMS\RequestHandler;

use Zend\Http\PhpEnvironment\Response as ZendResponse;

class RequestHandler
{
	static protected $baseXMLServices;
	protected $siteaccess;
	protected $id;
	
	public static function factory( $id, $requestHandlerClass, $siteAccess )
	{
		$return = null;
		
		self::$baseXMLServices = $siteAccess->getBaseXMSServices();
		
		if( $requestHandlerClass )
		{
			self::$baseXMLServices->get( 'log' )->info( 'RequestHandler class name: "' . $requestHandlerClass . '".' );
			
			$requestHandlerClass = $requestHandlerClass ? $requestHandlerClass : '\BaseXMS\RequestHandler\RequestHandler';
				
			if( class_exists( $requestHandlerClass ) )
			{
				$return = new $requestHandlerClass;
			}
			else
			{
				self::$baseXMLServices->get( 'log' )->warn( 'Could not find RequestHandler class: "' . $requestHandlerClass . '".' );
			}
	
			// couldn't get a valid class
			if( !( $return instanceof RequestHandler ) )
			{
				$return = new RequestHandler();
			}
	
				
			//TODO: use ini function?
			$return->id = $id;
			$return->siteAccess = $siteAccess;
		}
	
		return $return;
	}
	
	public function getResponse()
	{
		self::$baseXMLServices->get( 'log' )->warn( 'No concrete RequestHandler found.' );
		
		$response = new ZendResponse();
		$response->setStatusCode( 500 );
		return $response;
	}
}

?>