<?php 

namespace BaseXMS\UiComposer;

use BaseXMS\Stdlib\SimpleXMLElement as SimpleXMLElement;
use Zend\ServiceManager\ServiceLocatorAwareInterface;
use Zend\ServiceManager\ServiceLocatorInterface;
use Zend\Cache\StorageFactory;
use Zend\Cache\PatternFactory;


/*
 * composes a doc
 */
class UiComposer implements ServiceLocatorAwareInterface
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
	 * It is used in the UiComponent factory to get a UiComponent instance.
	 * 
	 * @var BaseXMS\Stdlib\DOMDocument
	 */
	private $contextData;
	
	private $xpath;
	
	/**
	 * @var ServiceLocatorInterface
	 */
	private $services;
	
	/*
	 * prevent endless loops
	 */
	private $buildMaxDepthCount = 0;
	private static $buildMaxDepth = 20;

	
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
		$uiComponent->fillFragment( $responseFragment, $this );
		
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
					$uiComponent->rerender( $this );
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
	
	/** 
	 * @return DOMDocument
	 */
	public function getDoc()
	{
		return $this->doc;
	}
	
	/**
	 * TODO: Rename to getContextData()
	 * @return DOMDocuement
	 */
	public function getData()
	{
		return $this->getContextData();
	}

	/**
	 * @return \BaseXMS\UiComposer\UiComposer
	 */
	public function getContextData()
	{
		return $this->contextData;
	}
	
	/**
	 * @param BaseXMS\Stdlib\DOMDocument $doc
	 * @return \BaseXMS\UiComposer\UiComposer
	 */
	public function setContextData( \BaseXMS\Stdlib\DOMDocument $doc )
	{
		$this->contextData = $doc;
		return $this;
	}
	
	//TODO: rename function name in consumers
	public function getServices()
	{
		return $this->getServiceLocator();
	}
	
	public function getUiComponents()
	{
		return $this->uiComponents;
	}
	
	public function getUiComponent( $instanceId )
	{
		return $this->uiComponents[ $instanceId ];
	}
	
	public function setServiceLocator( ServiceLocatorInterface $serviceLocator )
	{
		$this->services = $serviceLocator;

		// adding cache service
		if( true )
		{
			$cache = new \BaseXMS\Cache\Storage\Adapter\Filesystem();
			$cache->setOptions( array( 'cache_dir' => 'data/cache' ) );

			$this->services->setService( 'cache', $cache );
		}
	}
	
	public function getServiceLocator()
	{
		return $this->services;
	}
}

?>