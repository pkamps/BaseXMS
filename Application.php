<?php 

namespace BaseXMS;

use Zend\ServiceManager\ServiceManager;
use Zend\EventManager\EventManager;
use Zend\Mvc\Application as ZendApplication;
use Zend\Mvc\MvcEvent;
use Zend\Stdlib\RequestInterface as Request;
use Zend\Stdlib\ResponseInterface as Response;


class Application extends ZendApplication
{
	protected $baseXMSEventManager;
	
	public function __construct( $configuration, ServiceManager $serviceManager )
	{
		parent::__construct( $configuration, $serviceManager );
		
		$this->baseXMSEventManager = new EventManager();
		
		// shortcut the control lookup
		$this->getEventManager()->attach( MvcEvent::EVENT_DISPATCH, array( $this, 'onDispatch' ) );
		
		//TODO: moving to a dedicated function
		$this->serviceManager->setFactory( 'log', 'BaseXMS\Log\Factory' );
	}
	
	public function onDispatch( $e )
	{
		$request     = $e->getRequest();
		$routeMatch  = $e->getRouteMatch();
		$response    = $this->getResponse();
			
		$return = $this->dispatch( $request, $response, $routeMatch );
		
		$e->setResult( $return );
		
		$e->stopPropagation( true );
		
		return $return;
	}
	
	public function getBaseXMSEventManager()
	{
		return $this->baseXMSEventManager;
	}
	
	public function dispatch( Request $request, Response $response = null, $routeMatch )
	{
		$siteAccess = SiteaccessFactory::factory( 
				$this->getServiceManager(),
				$routeMatch->getParam( 'context' )
		);
		
		return $siteAccess->getResponse( $this->getServiceManager(), $routeMatch->getParam( 'path' ) );
	}	
}

?>