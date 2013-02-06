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
	
	/**
	 * @var string
	 */
	private $docTypeQualifiedName = 'html';
	
	
	/**
	 * @var array
	 */
	private $tidyOptions;
	
	/*
	 * UiCollection of all components for a request
	 */
	private $uiComponents;

	/*
	 * Constructor gets the data from outside. It is used in the UiComponent factory to get
	 * a UiComponent instance.
	 */
	private $contextData;
	
	/*
	 * Data collected during parsing UiComponents
	 */
	private $sharedData;
	
	
	private $xpath;
	private $services;
	
	/*
	 * prevent endless loops
	 */
	private $buildMaxDepthCount = 0;
	private static $buildMaxDepth = 20;
	
	/**
	 * @param ServiceManager $services
	 * @param DOMDocument $data
	 */
	public function __construct( $services, \DOMDocument $data )
	{
		//TODO: Should I create a class?
		$this->sharedData  = new \stdClass;
		$this->services    = $services;
		$this->contextData = $data;
	}
	
	public function run()
	{
		return $this->createDoc()->buildDoc()->rerender();
	}
	
	/**
	 * Builds the doc and the xpath which is needed for buildDoc
	 * 
	 * @return \BaseXMS\UiComposer
	 */
	protected function createDoc()
	{
		$domImpl = new \DOMImplementation();
		
		if( $this->docTypeQualifiedName )
		{
			$doctype   = $domImpl->createDocumentType( $this->docTypeQualifiedName );
			$this->doc = $domImpl->createDocument( null, 'include', $doctype );
		}
		else
		{
			$this->doc = $domImpl->createDocument( null, 'include' );
		}
		
		$this->doc->lastChild->setAttribute( 'type', 'root' );
		
		$this->xpath = new \DOMXpath( $this->doc );

		return $this;
	}
	
	/**
	 * do-while loop is better than a recursive algo. Let's say there is a in deep down Ui component,
	 * at least we parse out the basic structure of the doc (would be different in a recursive approach).
	 *
	 * @return \BaseXMS\UiComposer
	 */
	protected function buildDoc()
	{
		do
		{
			$this->buildMaxDepthCount++;
			
			$includes = $this->xpath->query( '//include' );
			
			$had_includes = $includes->length > 0;
			
			if( $had_includes )
			{
				foreach( $includes as $include )
				{
					$this->handleInclude( $include );
				}
			}
		}
		while( $had_includes && $this->buildMaxDepthCount < self::$buildMaxDepth );
		
		if( $this->buildMaxDepthCount == self::$buildMaxDepth )
		{
			$this->services->get( 'log' )->err( 'Reached max execution loops in UiComposer. Rendering interrupted.' );
		}
		
		return $this;
	}
	
	/**
	 * Based on the include and contextData this function gets a UiComponent.
	 * The it asks the UiComponent to fill an DOMFragment.
	 * 
	 * @param \DOMNode $include
	 * @return \BaseXMS\UiComposer
	 */
	protected function handleInclude( \DOMNode $include )
	{
		$uiComponent = UiComponent\Factory::factory( $this, $include );
			
		/*
		 * Create Fragment and let the UiComponent fill it
		*/
		$responseFragment = $this->doc->createDocumentFragment();
		$uiComponent->render( $responseFragment );
			
		//TODO: that's only the case if UiComponent destroyes it - unlikely?
		if( $responseFragment instanceof \DOMDocumentFragment )
		{
			$include->parentNode->replaceChild( $responseFragment, $include );
		}
		else
		{
			unset( $responseFragment );
			$this->services->get( 'log' )->warn( 'Did not get a valid DOMFragment' );
		}

		// Add component to list
		$this->uiComponents[ $uiComponent->getId() ] = $uiComponent;
		
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
		
		return $this;
	}
	
	public function output()
	{
		$output = $this->doc->saveHTML();

		//Tidy output
		if( !empty( $this->tidyOptions ) && extension_loaded( 'tidy' ) )
		{
			//not supported yet
		}
		
		return $output;
	}
	
	public function getDoc()
	{
		return $this->doc;
	}
	
	public function getData()
	{
		return $this->contextData;
	}
	
	public function getSharedData()
	{
		return $this->sharedData;
	}

	public function getServices()
	{
		return $this->services;
	}
	
	public function getUiComponents()
	{
		return $this->uiComponents;
	}
	
	public function getUiComponent( $instanceId )
	{
		return $this->uiComponents[ $instanceId ];
	}
}

?>