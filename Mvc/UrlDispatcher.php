<?php 

namespace BaseXMS\Mvc;

use BaseXMS\Stdlib\DOMDocument;
use Zend\ServiceManager\ServiceLocatorAwareInterface;
use Zend\ServiceManager\ServiceLocatorInterface;

/*
 * The ControllerDispatcher has the request path as in input parameter.
 * Base on the input it returns a simple array containing the response code
 * and the response data. It is not responsible to parse the data which comes
 * from the XML database.
 * 
 */
class UrlDispatcher implements ServiceLocatorAwareInterface
{
	protected $requestPath;
	protected $services;
	private 	$returnFormat = '
<node>
	<code>{$code}</code>
	<id>{$x/@id/string()}</id>
	<contentclass>{$x/accessPaths/@class/string()}</contentclass>
	<path>{let $pathParts := $x/ancestor-or-self::*/@id/string() return if( count( $pathParts ) > 1 ) then string-join( $pathParts, "/" ) else "/"}</path>
</node>';
	
	
	/**
	 * Dispatch the url and returns a class name and the tree context Id
	 * 
	 * @param string $path
	 * @return DOMDocument 
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
				$response = new DOMDocument();
				$response->loadXML( '<node><code>400</code></node>' );
			}
		}
		catch( \Exception $e )
		{
			$response = new DOMDocument();
			$response->loadXML( '<node><code>500</code></node>' );
		}
		
		$this->services->get( 'accumulator' )->stop( 'URL Dispatch' );

		return $response;
	}
	
	/**
	 * @param string $path
	 */
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
	
	/**
	 * @return Ambigous <boolean, unknown>
	 */
	protected function getResponseByPath()
	{
		$nodePath = '';
		foreach( $this->requestPath as $part )
		{
			$nodePath .= '/node[accessPaths//entry[ ( @type="alt" or @type="main" ) and @path="' . $part . '"]]';
		}
		
		$query = 'let $code := 200 return let $x := ' . $nodePath . 'return if( $x ) then ' . $this->returnFormat . ' else null';
		
		$result = $this->services->get( 'xmldb' )->execute( $query, 'xml' );
	
		return $result ? $result : false;
	}

	protected function getResponseByFullPath()
	{
		$return = false;
	
		$fullPath = implode( '/', $this->requestPath );
	
		$nodePath = '//node[accessPaths//entry[@type="altFull" and @path="' . $fullPath . '"]]';

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
			$nodePath .= '/node[accessPaths//entry[ ( @type="alt" or @type="main" or @type="old" ) and @path="' . $part . '"]]';
		}

		$query = 'let $code := 000 return let $x := ' . $nodePath . 'return if( $x ) then ' . $this->returnFormat . ' else null';
		
		$orgRequest = $this->services->get( 'xmldb' )->execute( $query, 'xml' );
		
		if( $orgRequest instanceof \DOMDocument )
		{
			// build an array with involved node ids - reverse order
			$idPathParts = array_reverse( explode( '/', $orgRequest->queryToValue( '/node/path' ) ) );

			// translate requestPath
			$newRequestPath = array();
			foreach( array_reverse( $this->requestPath ) as $index => $part )
			{
				$query = '//node[@id="' . $idPathParts[ $index ] .'"]/accessPaths//entry[@type="old" and @path="' . $part . '"]/string()';

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
			$return = new DOMDocument();
			$return->loadXML( '<node><code>301</code><id></id><contentclass></contentclass><path>'. implode( '/', array_reverse( $newRequestPath ) ) .'</path></node>' );
		}
		
		return $return;
	}

	protected function getRedirectResponseByFullPath()
	{
		$return = false;
	
		$fullPath = implode( '/', $this->requestPath );
	
		$query  = 'let $code := 301 return let $x := //node[accessPaths//entry[@type="oldFull" and @path="' . $fullPath . '"]] ';
		$query .= 'return if( $x ) then ' . $this->returnFormat . ' else null';

		$result = $this->services->get( 'xmldb' )->execute( $query, 'xml' );
		
		return $result ? $result : false;
	}
		
	/**
	 * @param ServiceLocatorInterface $serviceLocator
	 */
	public function setServiceLocator( ServiceLocatorInterface $serviceLocator )
	{
		$this->services = $serviceLocator;
	}
	
	/**
	 * @return ServiceLocatorInterface
	 */
	public function getServiceLocator()
	{
		return $this->services;
	}
}

?>