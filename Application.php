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
		$this->serviceManager->setService( 'user', new \BaseXMS\User() );
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
	
	public function run()
	{
		$events = $this->getEventManager();
		$event  = $this->getMvcEvent();
	
		// Define callback used to determine whether or not to short-circuit
		$shortCircuit = function ($r) use ($event) {
			if ($r instanceof ResponseInterface) {
				return true;
			}
			if ($event->getError()) {
				return true;
			}
			return false;
		};
	
		// Trigger route event
		$result = $events->trigger(MvcEvent::EVENT_ROUTE, $event, $shortCircuit);
		if ($result->stopped()) {
			$response = $result->last();
			if ($response instanceof ResponseInterface) {
				$event->setTarget($this);
				$event->setResponse($response);
				$events->trigger(MvcEvent::EVENT_FINISH, $event);
				return $response;
			}
			if ($event->getError()) {
				return $this->completeRequest($event);
			}
			return $event->getResponse();
		}
		if ($event->getError()) {
			return $this->completeRequest($event);
		}
	
		// Trigger dispatch event
		$result = $events->trigger(MvcEvent::EVENT_DISPATCH, $event, $shortCircuit);
	
		// Complete response
		$response = $result->last();
		
		//TODO: replace this shortcut hack
		return $response;
	}
	
}

?>