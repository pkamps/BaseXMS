<?php 

namespace BaseXMS\Mvc;

use BaseXMS\User;

use Zend\ServiceManager\ServiceManager;
use Zend\Mvc\Router\RouteMatch;
use Zend\Session\Container;
use Zend\ServiceManager\ServiceLocatorAwareInterface;
use Zend\ServiceManager\ServiceLocatorInterface;
use BaseXMS\Mvc\Service\ServiceManagerConfig;
use BaseXMS\Mvc\SiteAccessFactory;


class Application implements ServiceLocatorAwareInterface
{
	/**
	 * @var ServiceLocatorInterface
	 */
	private $serviceManager;
	
	/**
	 * @var Zend\Mvc\Router\RouteMatch
	 */
	private $routeMatch;
	

	/**
	 * @return \BaseXMS\Mvc\Application
	 */
	public static function init()
	{
		$configuration = include 'config/application.config.php';
		
		$smConfig = isset( $configuration[ 'service_manager' ] ) ? $configuration[ 'service_manager' ] : array();
		
		$serviceManager = new ServiceManager( new ServiceManagerConfig( $smConfig ) );
		
		$serviceManager->setService( 'ApplicationConfig', $configuration );
		$serviceManager->get( 'ModuleManager' )->loadModules();
		
		$serviceManager->get( 'log' )->debug( 'Init Application' );
		$application = new Application();
		$application->setServiceLocator( $serviceManager );

		return $application;
	}
	
	public function route()
	{
		$request = $this->serviceManager->get( 'Request' );
		
		$requestPath = substr( $request->getRequestUri(), strlen( $request->getBasePath() ) );
		
		//get parameters
		$requestPath = preg_replace( '/\?.*$/', '', $requestPath );
		
		$regex = '(\G(\/:(?<context>.*?)|)((?<path>\/.*?)|)(\/|)$)';
		preg_match( $regex, $requestPath, $matches );
		
		$options = array(
				'controller' => 'index',
				'context'    => $matches[ 'context' ],
				'path'       => $matches[ 'path' ]
		);
		
		$this->routeMatch = new RouteMatch( $options, strlen( $requestPath ) );
		
		return $this;
	}
	
	public function setSiteAccess()
	{
		// init session
		$this->serviceManager->get( 'session' );
		
		// set user context
		$container = new Container( 'initialized' );
		$this->serviceManager->setService( 'user', new User( $container->userId ) );
		
		$this->siteAccess = SiteaccessFactory::factory
		(
				$this->serviceManager,
				$this->routeMatch->getParam( 'context' )
		);
		
		return $this;
	}
	
	public function getResponse()
	{
		$response = $this->siteAccess->dispatch( $this->routeMatch->getParam( 'path' ) )->getResponse();
		
		// Calc total run time
		$this->getServiceLocator()->get( 'accumulator' )->start( 'Total', SCRIPT_START );
		$this->getServiceLocator()->get( 'accumulator' )->stop( 'Total' );
		$this->getServiceLocator()->get( 'accumulator' )->memory_usage( 'Before sending response' );
		
		$this->addDebugOutput( $response );
		
		return $response;
	}

	/* (non-PHPdoc)
	 * @see Zend\ServiceManager.ServiceLocatorAwareInterface::setServiceLocator()
	*/
	public function setServiceLocator( ServiceLocatorInterface $serviceLocator )
	{
		$this->serviceManager = $serviceLocator;
	}
	
	/* (non-PHPdoc)
	 * @see Zend\ServiceManager.ServiceLocatorAwareInterface::getServiceLocator()
	*/
	public function getServiceLocator()
	{
		return $this->serviceManager;
	}
	
	public function addDebugOutput( $response )
	{
		// build doc for debug output
		$doctype = \DOMImplementation::createDocumentType( 'html' );
		$doc     = \DOMImplementation::createDocument( null, 'include', $doctype );
		$doc->lastChild->setAttribute( 'type', 'debug' );

		// run it through the composer
		$uiComposer = new \BaseXMS\UiComposer\UiComposer();
		$uiComposer->setServiceLocator( $this->serviceManager );
		$uiComposer->setDoc( $doc );
		
		//TODO: allow to specify the output location for the debug output
		$content = $response->getContent() . $uiComposer->run()->output();
		$response->setContent( $content );
		
		return $this;
	}
	
	/**
	 * @return \Zend\Mvc\Router\RouteMatch
	 */
	public function getRouteMatch()
	{
		return $this->routeMatch;
	}
}

?>