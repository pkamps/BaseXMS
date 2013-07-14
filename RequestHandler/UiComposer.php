<?php 

namespace BaseXMS\RequestHandler;

use BaseXMS\Stdlib\DOMDocument;
use Zend\Http\PhpEnvironment\Response as ZendResponse;
use Zend\Http\Headers;

class UiComposer extends RequestHandler
{
	public function getResponse()
	{
		$doc = new DOMDocument();
		$doc->loadXML( '<nodeid>'. $this->context->queryToValue( '//id' ) .'</nodeid>' );
		
		$uiComposer = new \BaseXMS\UiComposer\UiComposer();
		$uiComposer->setServiceLocator( $this->getServiceLocator() );
		$uiComposer->setContextData( $doc );
		
		$response = new ZendResponse();

		$response->setContent( $uiComposer->run()->output() );
		$response->setStatusCode( 200 );
		
		return $response;
	}
}

?>