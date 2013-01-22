<?php 

namespace BaseXMS;

use BaseXMS\Stdlib\SimpleXMLElement as SimpleXMLElement;

/*
 * Recursively composes a page
 */
class UiComposer
{
	/*
	 *  Return is build in $doc
	 */
	private $doc;
	
	/*
	 * UiCollection of all components for a request
	 */
	private $uiComponents;

	/*
	 * Constructor gets the data from outside 
	 */
	private $data;
	
	/*
	 * Data collected during parsing UiComponents
	 */
	private $sharedData;
	
	
	private $xpath;
	private $services;
	
	/*
	 * prevent endless loops
	 */
	private static $buildExecutionCount = 0;
	
	/**
	 * @param unknown_type $services
	 * @param unknown_type $id
	 */
	public function __construct( $services, $data )
	{
		//TODO: Should I create a class?
		$this->sharedData = new \stdClass;
		
		$this->services = $services;
		$this->data = $data;

		// Build base doc
		$domImpl = new \DOMImplementation();
		$doctype   = $domImpl->createDocumentType( 'html' );
		$this->doc = $domImpl->createDocument( null, 'include', $doctype );
		$this->doc->lastChild->setAttribute( 'type', 'html' );
		
		$this->xpath = new \DOMXpath( $this->doc );
	}
	
	public function run()
	{
		$this->buildDoc( $this->doc->firstChild );
		$this->rerender();

		return $this;
	}
	
	/**
	 * @param \DOMNode $element
	 * @throws \Exception
	 * @return Ambiguous
	 */
	protected function buildDoc( \DOMNode $element )
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
					$uiComponent = UiComponent\Factory::factory( $this, $include );
					// Add component to list
					$this->uiComponents[] = $uiComponent;
						
					/*
					 * Add UiComponent xml
					 */
					$result = $uiComponent->render();

					if( $result instanceof \DOMNode )
					{
						// need to import before we can replace it
						$result = $this->doc->importNode( $result, true );
						$include->parentNode->replaceChild( $result, $include );
					
						$this->buildDoc( $result );
					}
					else
					{
						//TODO: add log entry
					}
				}
			}
		}
		else
		{
			throw new \Exception( 'too many execution loops' );
		}
		
		return $this;
	}
	
	protected function rerender()
	{
		if( !empty( $this->uiComponents ) )
		{
			//TODO: consider multiple loops until all components say needsRerender false.
			foreach( $this->uiComponents as $uiComponent )
			{
				if( $uiComponent->needsRerender )
				{
					$uiComponent->rerender();
				}
			}
		}
	}
	
	public function output()
	{
		
		// won't work - php bug
		$this->doc->formatOutput = true;
		return $this->doc->saveHTML();
	}
	
	public function getDoc()
	{
		return $this->doc;
	}
	
	public function getData()
	{
		return $this->data;
	}
	
	public function getSharedData()
	{
		return $this->sharedData;
	}

	public function getServices()
	{
		return $this->services;
	}
}

?>