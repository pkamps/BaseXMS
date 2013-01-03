<?php

namespace BaseXMS;

use Zend\ServiceManager\ServiceManager;
use Zend\ServiceManager\ServiceLocatorInterface;
use Zend\EventManager\EventManager;
use Zend\Http\PhpEnvironment\Response as ZendResponse;
use Zend\Config\Config;
use BaseXMS\BaseX as BaseXDb;
use BaseXMS\RequestHandler\RequestHandler;

class SiteAccess
{
	protected $baseXMSServices;
	protected $config;
	
	public function __construct()
	{
		if( ! defined( 'BASEXMS_START' ) )
		{
			define( 'BASEXMS_START', microtime( true ) );
		}
	}
	
	public function init( ServiceLocatorInterface $serviceLocator )
	{
		$application = $serviceLocator->get( 'application' );

		// Add siteaccess specific config
		$configHandler = new Config( $application->getConfig() );
		$configHandler->merge( $this->addConfig() );

		//TODO: avoid config in context of the siteaccess
		$this->config = $configHandler->toArray();
		
		$application->getServiceManager()->setService( 'accumulator', new Accumulator() );
		$application->getServiceManager()->setFactory( 'xmldb', 'BaseXMS\BaseXFactory' );
		
		//TODO: remove all references to baseXMSServices
		$this->baseXMSServices = $application->getServiceManager();
		
		// not needed I think
		#$this->baseXMSServices->setService( 'siteaccess', $this );
	}
	
	public function getBaseXMSServices()
	{
		return $this->baseXMSServices;
	}
	
	/*
	 * building a Zend response object
	 * TODO: consider to move more lgic inside the RequestHandler factory
	*/
	public function getResponse( ServiceLocatorInterface $serviceLocator, $path )
	{
		$serviceLocator->get( 'log' )->info( 'Build Response' );
	
		$urlDispatcher = new UrlDispatcher( $serviceLocator );
		$baseXMSResponse = $urlDispatcher->dispatch( $path );
	
		$response = new ZendResponse();
	
		switch( (string) $baseXMSResponse->code )
		{
			case 200:
			{
				$requestHandler = RequestHandler::factory
				(
						(string) $baseXMSResponse->id,
						(string) $baseXMSResponse->contentclass,
						$this
				);

				// override response
				$response = $requestHandler->getResponse();
			}
			break;
					
			case 301:
			{

				if( true ) // redirect debug case
				{
					$response->setContent( 'Redirect to <a href="' . (string) $baseXMSResponse->path . '">' . (string) $baseXMSResponse->path . '</a>');
					$response->setStatusCode( 200 );
				}
				else
				{
					$response->setContent( 'Redirect' );
					$response->setStatusCode( 301 );
					$headers = Headers::fromString( 'Location: ' . (string) $baseXMSResponse->path );
					$response->setHeaders( $headers );
				}
			}
			break;
					
			case 500:
			{
				$response->setContent( '500 - Server Error.' );
				$response->setStatusCode( (string) $baseXMSResponse->code );
			}
			break;
				
			case 400:
			{
				$response->setContent( '404 - Not found.' );
				$response->setStatusCode( (string) $baseXMSResponse->code );
			}
			break;
					
			default:
			{
				$response->setContent( 'Unhandled return code.' );
				$response->setStatusCode( 500 );
			}
		}
	
		$response->setReasonPhrase( $response->getReasonPhrase() ); // set default phrase
	
		$serviceLocator->get( 'accumulator' )->start( 'BaseXMS', BASEXMS_START );
		$serviceLocator->get( 'accumulator' )->stop( 'BaseXMS' );
	
		return $response;
	}
	
	public function getConfig()
	{
		return $this->config;
	}
	
	/**
	 * Add siteaccess related config. It overrides/adds to the module config.
	 * 
	 * @return \Zend\Config\Config
	 */
	protected function addConfig()
	{
		return new Config( array() );
	}
}
