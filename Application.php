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
		//TODO: better place for a siteacces factory configuration?
		$appConfig = $this->getServiceManager()->get( 'ApplicationConfig' );
				
		$siteaccesses = isset( $appConfig[ 'siteaccesses' ] ) ? $appConfig[ 'siteaccesses' ] : array();
		
		$siteAccess = SiteaccessFactory::factory( $routeMatch->getParam( 'context' ),
		                                          $siteaccesses,
		                                          $this );
		
		$baseXMSResponse = $siteAccess->getResponse( $routeMatch->getParam( 'path' ) );
		
		return $baseXMSResponse;
	}	
}

?>