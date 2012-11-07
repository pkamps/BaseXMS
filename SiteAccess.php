<?php

namespace BaseXMS;

use Zend\ServiceManager\ServiceManager;
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
	
	
	public function init( $application )
	{
		// Add siteaccess specific config
		$appConfig = is_object( $application ) ? $application->getConfig() : array(); 
		$configHandler = new Config( $appConfig );
		$configHandler->merge( $this->addConfig() );
		$this->config = $configHandler->toArray();
		
		$this->baseXMSServices = new ServiceManager();
		$this->baseXMSServices->setService( 'application', $application );
		$this->baseXMSServices->setService( 'siteaccess', $this );
		$this->baseXMSServices->setService( 'accumulator', new Accumulator() );
		$this->baseXMSServices->setFactory( 'log', 'BaseXMS\Log\Factory' );
		$this->baseXMSServices->setFactory( 'xmldb', 'BaseXMS\BaseXFactory' );
	}
	
	public function getBaseXMSServices()
	{
		return $this->baseXMSServices;
	}
	
	/*
	 * building a Zend response object
	*/
	public function getResponse( $path )
	{
		//print_r( $this->baseXMSServices->get( 'application')->getConfig() );
		
		$this->baseXMSServices->get( 'log' )->info( 'Build Response' );
	
		$urlDispatcher = new UrlDispatcher( $this->baseXMSServices );
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
	
		$this->baseXMSServices->get( 'accumulator' )->start( 'BaseXMS', BASEXMS_START );
		$this->baseXMSServices->get( 'accumulator' )->stop( 'BaseXMS' );
	
		$this->outputLog();
	
		echo $this->baseXMSServices->get( 'accumulator')->getDataAsHtml();
	
		return $response;
	}
	
	public function getConfig()
	{
		return $this->config;
	}
	
	protected function addConfig()
	{
		return new Config( array() );
	}
	
	private function outputLog()
	{
		$formatter = new Log\Formatter\Html;
	
		// TODO: loop over writers and print out all Mock writer content
		$events = $this->baseXMSServices->get( 'log' )->getWriters()->top()->events;
	
		if( !empty( $events ) )
		{
			foreach( $events as $event )
			{
				echo '<ul>';
				echo $formatter->format( $event );
				echo '</ul>';
			}
		}
	}	
}
