<?php 

namespace BaseXMS\RequestHandler;

use Zend\Http\PhpEnvironment\Response as ZendResponse;
use BaseXMS\DataObjectHandler\ContentObject\ContentObject;

class UiComposer extends RequestHandler
{
	public function getResponse()
	{
		$contentObjectHandler = new ContentObject( self::$baseXMLServices );
		
		$uiComposer = new \BaseXMS\UiComposer( self::$baseXMLServices, $contentObjectHandler->read( $this->id ) );
				
		$response = new ZendResponse();
		$response->setContent( $uiComposer->run()->output() );
		$response->setStatusCode( 200 );
		
		return $response;
	}
}

?>