<?php 

namespace BaseXMS\RequestHandler;

use Zend\Http\PhpEnvironment\Response as ZendResponse;

class UiComposer extends RequestHandler
{
	public function getResponse()
	{
		$uiComposer = new \BaseXMS\UiComposer( self::$baseXMLServices, $this->id );
				
		$response = new ZendResponse();
		$response->setContent( $uiComposer->run()->output() );
		$response->setStatusCode( 200 );
		
		return $response;
	}
}

?>