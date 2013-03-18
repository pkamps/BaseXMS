<?php 

namespace BaseXMS\RequestHandler;

use BaseXMS\Stdlib\DOMDocument;
use Zend\Http\PhpEnvironment\Response as ZendResponse;
use Zend\Http\Headers;


class UiComposer extends RequestHandler
{
	public function getResponse()
	{
		#$contentObjectHandler = new ContentObject();
		#$contentObjectHandler->setServiceLocator( self::$baseXMLServices );
		#$data = $contentObjectHandler->read( $this->id, 'xml' );
		
		$doc = new DOMDocument();
		$doc->loadXML( '<nodeid>'. $this->id .'</nodeid>' );
		
		$uiComposer = new \BaseXMS\UiComposer\UiComposer();
		$uiComposer->setServiceLocator( self::$baseXMLServices );
		$uiComposer->setContextData( $doc );
		
		$response = new ZendResponse();

		/* Debug parsing - TODO: won't work with the debug output at the end */
		//$uiComposer->dropIncludes = false;
		//$headers = Headers::fromString( 'Content-Type: text/xml' );
		//$response->setHeaders( $headers );

		$response->setContent( $uiComposer->run()->output() );
		$response->setStatusCode( 200 );
		
		return $response;
	}
}

?>