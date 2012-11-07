<?php 

namespace BaseXMS;

use BaseXMS\SimpleXMLElement as SimpleXMLElement;

/*
 */
class UiComposer
{
	private $doc;
	private static $buildExecutionCount = 0;
	
	public function __construct( $services, $id )
	{
		$this->services = $services;
		$this->id = $id;

		// Build base doc
		$domImpl = new \DOMImplementation();
		$doctype   = $domImpl->createDocumentType( 'html' );
		$this->doc = $domImpl->createDocument( null, 'include', $doctype );
		$this->doc->lastChild->setAttribute( 'class', '\BaseXMS\UiComponent\Html' );
		
		$this->xpath = new \DOMXpath( $this->doc );
	}
	
	public function run()
	{
		return $this->build( $this->doc->firstChild );
	}
	
	public function build( $element )
	{
		self::$buildExecutionCount++;
		
		if( self::$buildExecutionCount < 1000 )
		{
			#$this->doc->formatOutput = true;
			#echo $this->doc->saveHTML();
				
			$includes = $this->xpath->query( './/include | /include', $element );

			if( $includes && $includes->length )
			{
				foreach( $includes as $include )
				{
					
					$includeClass = $include->getAttribute( 'class' );
					$this->services->get( 'log' )->info( 'Load UiComponent: ' . $includeClass );
					
					$uiComponent = UiComponent\Factory::factory( $this->services, $includeClass, $this->id );
					
					$subElement = $uiComponent->getXml();
					// need to import before we can replace it
					$subElement = $this->doc->importNode( $subElement, true );
					$include->parentNode->replaceChild( $subElement, $include );
	
					$this->build( $subElement );
				}
			}
		}
		else
		{
			throw new \Exception( 'too many loops' );
		}
		
		return $this;
	}
	
	public function output()
	{
		
		// won't work - php bug
		$this->doc->formatOutput = true;
		return $this->doc->saveHTML();
	}
}

?>