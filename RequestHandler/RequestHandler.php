<?php 

namespace BaseXMS\RequestHandler;

use Zend\Http\PhpEnvironment\Response as ZendResponse;
use Zend\ServiceManager\ServiceLocatorAwareInterface;
use Zend\ServiceManager\ServiceLocatorInterface;

class RequestHandler implements ServiceLocatorAwareInterface
{
	protected $serviceManager;
	protected $siteaccess;
	
	/**
	 * @var DomDocument
	 */
	protected $context;
	
	public function getResponse()
	{
		$this->serviceManager->get( 'log' )->warn( 'No concrete RequestHandler found.' );
		
		$response = new ZendResponse();
		$response->setStatusCode( 500 );
		return $response;
	}
	
	/**
	 * @param DomDocument $context
	 * @return \BaseXMS\RequestHandler\RequestHandler
	 */
	public function setContext( \DomDocument $context )
	{
		$this->context = $context;
		return $this;
	}

	public function setSiteAccess( $siteAccess )
	{
		$this->siteAccess = $siteAccess;
		return $this;
	}
	
	
	/* (non-PHPdoc)
	 * @see Zend\ServiceManager.ServiceLocatorAwareInterface::setServiceLocator()
	*/
	public function setServiceLocator( ServiceLocatorInterface $serviceLocator )
	{
		$this->serviceManager = $serviceLocator;
		return $this;
	}
	
	/* (non-PHPdoc)
	 * @see Zend\ServiceManager.ServiceLocatorAwareInterface::getServiceLocator()
	*/
	public function getServiceLocator()
	{
		return $this->serviceManager;
	}
	
}

?>