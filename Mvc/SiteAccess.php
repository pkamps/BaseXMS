<?php

namespace BaseXMS\Mvc;

use BaseXMS\Stdlib\DOMDocument;

use Zend\EventManager\EventManager;
use Zend\Http\PhpEnvironment\Response as ZendResponse;
use Zend\Config\Config;
use BaseXMS\BaseX as BaseXDb;
use BaseXMS\RequestHandler\RequestHandler;

class SiteAccess extends BaseXMSService
{
	/**
	 * @var DOMDocument
	 */
	protected $baseXMSResponse;
	
	public function __construct()
	{
		if( ! defined( 'BASEXMS_START' ) )
		{
			define( 'BASEXMS_START', microtime( true ) );
		}
	}
	
	/**
	 * Merges the app config with the siteaccess config
	 */
	public function init()
	{
		// Add siteaccess specific config
		$configHandler = new Config( $this->serviceManager->get( 'config' ) );
		$configHandler->merge( $this->addConfig() );
	
		$this->serviceManager->setAllowOverride( true );
		{
			$this->serviceManager->setService( 'config', $configHandler->toArray() );
		}
		$this->serviceManager->setAllowOverride( false );
						
		// not needed I think
		#$this->baseXMSServices->setService( 'siteaccess', $this );
	}

	/**
	 * @return \BaseXMS\Mvc\SiteAccess
	 */
	public function dispatch( $path )
	{
		$urlDispatcher = new UrlDispatcher();
		$urlDispatcher->setServiceLocator( $this->serviceManager );
		$this->baseXMSResponse = $urlDispatcher->dispatch( $path );
		
		return $this;
	}
	
	/*
	 * building a Zend response object
	 * TODO: consider to move more logic inside the RequestHandler factory
	*/
	public function getResponse()
	{
		$this->serviceManager->get( 'log' )->info( 'Build Response' );
	
		$response = new ZendResponse();

		switch( $this->baseXMSResponse->queryToValue( '//code' ) )
		{
			case 200:
			{
				$requestHandler = \BaseXMS\RequestHandler\Factory::factory(
						$this->baseXMSResponse,
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
					$response->setContent( 'Redirect to <a href="' . (string) $this->baseXMSResponse->path . '">' . (string) $this->baseXMSResponse->path . '</a>');
					$response->setStatusCode( 200 );
				}
				else
				{
					$response->setContent( 'Redirect' );
					$response->setStatusCode( 301 );
					$headers = Headers::fromString( 'Location: ' . (string) $this->baseXMSResponse->path );
					$response->setHeaders( $headers );
				}
			}
			break;
					
			case 500:
			{
				$response->setContent( '500 - Server Error.' );
				$response->setStatusCode( (string) $this->baseXMSResponse->code );
			}
			break;
				
			case 400:
			{
				$response->setContent( '404 - Not found.' );
				$response->setStatusCode( (string) $this->baseXMSResponse->code );
			}
			break;
					
			default:
			{
				$response->setContent( 'Unhandled return code.' );
				$response->setStatusCode( 500 );
			}
		}
	
		$response->setReasonPhrase( $response->getReasonPhrase() ); // set default phrase
	
		$this->serviceManager->get( 'accumulator' )->start( 'BaseXMS', BASEXMS_START );
		$this->serviceManager->get( 'accumulator' )->stop( 'BaseXMS' );
	
		return $response;
	}
	
	/**
	 * deprecated
	 */
	public function getConfig()
	{
		$this->serviceManager->get( 'log' )->warn( 'Calling deprecated function: SiteAccess::getConfig' );
		return $this->serviceManager->get( 'config' );
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
