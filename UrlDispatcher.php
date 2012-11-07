<?php 

namespace BaseXMS;

/*
 * The ControllerDispatcher has the request path as in input parameter.
 * Base on the input it returns a simple array containing the response code
 * and the response data. It is not responsible to parse the data which comes
 * from the XML database.
 * 
 * TODO: just return a simpleXML response object containing the return code etc
 * TODO: Consider a "DispatcherResult" class instead of the return array
 */
class UrlDispatcher
{
	protected $requestPath;
	protected $services;
	private 	$returnFormat = '
<node>
	<code>{$code}</code>
	<id>{$x/@id/string()}</id>
	<contentclass>{$x/@class/string()}</contentclass>
	<path>{let $pathParts := $x/ancestor-or-self::*/@path/string() return if( count( $pathParts ) > 1 ) then string-join( $pathParts, "/" ) else "/"}</path>
</node>';
	
	
	public function __construct( $services )
	{
		$this->services = $services;
	}
	
	/**
	 * Dispatch the url and returns a class name and the tree context Id
	 * 
	 * @param string $path
	 * @return multitype:number string 
	 */
	public function dispatch( $path )
	{
		$this->services->get( 'accumulator' )->start( 'URL Dispatch', microtime( true ) );
		
		$this->setRequestPath( $path );
		
		try
		{
			$response = $this->getResponseByPath();
	
			if( !$response )
			{
				$response = $this->getResponseByFullPath();
			}
			
			if( !$response )
			{
				$response = $this->getRedirectResponse();
			}
	
			if( !$response )
			{
				$response = $this->getRedirectResponseByFullPath();
			}
			
			if( !$response )
			{
				$response = new SimpleXMLElement( '<node><code>400</code></node>' );
			}
		}
		catch( \Exception $e )
		{
			$response = new SimpleXMLElement( '<node><code>500</code></node>' );
		}
		
		$this->services->get( 'accumulator' )->stop( 'URL Dispatch' );

		return $response;
	}
	
	protected function setRequestPath( $path )
	{
		// Special case for root
		if( $path == '/' )
		{
			$pathParts = array( '' );
		}
		else
		{
			$pathParts = explode( '/', $path );
		}
		
		$this->requestPath = $pathParts;
	}
	
	protected function getResponseByPath()
	{
		$nodePath = '';
		foreach( $this->requestPath as $part )
		{
			$nodePath .= '/node[@path="' . $part . '" or properties/altPaths//entry[@path="' . $part . '"]]';
		}
		
		$query = 'let $code := 200 return let $x := ' . $nodePath . 'return if( $x ) then ' . $this->returnFormat . ' else null';
		
		$result = $this->services->get( 'xmldb' )->execute( $query, 'xml' );
	
		return $result ? $result : false;
	}

	protected function getResponseByFullPath()
	{
		$return = false;
	
		$fullPath = implode( '/', $this->requestPath );
	
		$nodePath = '//node[properties/altFullPaths//entry[@path="' . $fullPath . '"]]';

		$query = 'let $code := 200 return let $x := ' . $nodePath . 'return if( $x ) then ' . $this->returnFormat . ' else null';
		
		$result = $this->services->get( 'xmldb' )->execute( $query, 'xml' );

		return $result ? $result : false;
	}
	
	public function getRedirectResponse()
	{
		$return = false;
		
		// Check if it matches a redirect path
		$nodePath = '';
		foreach( $this->requestPath as $part )
		{
			$nodePath .= '/node[@path="' . $part . '" or ';
			$nodePath .= 'properties/altPaths//entry[@path="' . $part . '"] or ';
			$nodePath .= 'properties/oldPaths//entry[@path="' . $part . '"]]';
		}
		
		$query = $nodePath . '/@id/string()';
		$leafId = $this->services->get( 'xmldb' )->execute( $query );
		
		
		if( $leafId )
		{
			// build an array with involved node ids - reverse order
			$idPathParts = array( $leafId );
			
			while( $leafId = $this->getParentNode( $leafId ) )
			{
				$idPathParts[] = $leafId;
			}
			
			// translate requestPath
			$newRequestPath = array();
			foreach( array_reverse( $this->requestPath ) as $index => $part )
			{
				$query = '//node[@id=' . $idPathParts[ $index ] .']/properties/oldPaths//entry[@path="' . $part . '"]/string()';
				$newPath = $this->services->get( 'xmldb' )->execute( $query );
				
				if( $newPath )
				{
					$newRequestPath[] = $newPath;
				}
				else
				{
					$newRequestPath[] = $part;
				}
			}
			
			$return = new SimpleXMLElement( '<node><code>301</code><id></id><contentclass></contentclass><path>'. implode( '/', array_reverse( $newRequestPath ) ) .'</path></node>' );
		}
		
		return $return;
	}

	protected function getRedirectResponseByFullPath()
	{
		$return = false;
	
		$fullPath = implode( '/', $this->requestPath );
	
		$query  = 'let $code := 301 return let $x := //node[properties/oldFullPaths//entry[@path="' . $fullPath . '"]] ';
		$query .= 'return if( $x ) then ' . $this->returnFormat . ' else null';

		$result = $this->services->get( 'xmldb' )->execute( $query, 'xml' );
		
		return $result ? $result : false;
	}
	
	private function getParentNode( $id )
	{
		$query = '//node[@id=' . $id . ']/../@id/string()';
		$parentId = $this->services->get( 'xmldb' )->execute( $query );
		
		return $parentId;
	}
}

?>