<?php 

namespace BaseXMS\RequestHandler;

use Zend\Http\PhpEnvironment\Response as ZendResponse;
use BaseXMS\DataObjectHandler\ContentObject;
use Zend\Http\Headers;

class UiComposer extends RequestHandler
{
	public function getResponse()
	{
		$contentObjectHandler = new ContentObject( self::$baseXMLServices );
		
		$uiComposer = new \BaseXMS\UiComposer( self::$baseXMLServices, $contentObjectHandler->read( $this->id, 'xml' ) );
		
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