<?php 

namespace BaseXMS\UiComposer\UiComponent;

use BaseXMS\UiComposer\UiComposer;
use BaseXMS\UiComposer\Cachable;
use BaseXMS\UiComposer\RenderResult;

class UiComponent
{
	/**
	 * @var DOMDocument
	 */
	protected $context;
	
	/**
	 * @var UiComposer
	 */
	protected $uiComposer;
	
	/**
	 * @var boolean
	 */
	public $needsRerender = false;
	
	/**
	 * @var \BaseXMS\UiComposer\RenderResult
	 */
	public $renderResult;
	
	/**
	 * @return \BaseXMS\UiComposer\UiComponent\UiComponent
	 */
	protected function render()
	{
		$this->renderResult = new RenderResult( '<section>Default from UiComponent</section>' );
		return $this;
	}
	
	/**
	 * @param \DOMDocumentFragment $fragment
	 * @return \BaseXMS\UiComponent\UiComponent
	 */
	public function fillFragment( \DOMDocumentFragment $fragment )
	{
		$result = $this->getRenderResult();
		
		if( $result instanceof RenderResult )
		{
			$fragment->appendXML( $this->getRenderResult()->getOutput() );
		}
		else
		{
			$this->getUiComposer()->getServiceLocator()->get( 'log' )->warn( 'Could not get a RenderResult: ' . get_class( $this ) );
		}
		
		return $this;
	}
	
	/**
	 * @param UiComposer $composer
	 * @return \BaseXMS\UiComponent\UiComponent
	 */
	public function rerender()
	{
		$this->needsRerender = false;
		return $this;
	}
	
	/**
	 * Get instance id
	 */
	public function getId()
	{
		return spl_object_hash( $this );
	}
	
	/**
	 * @return boolean
	 */
	public function isRendered()
	{
		return ! is_null( $this->renderResult );
	}
	
	/**
	 * @param \DOMDocument $doc
	 * @return \BaseXMS\UiComponent\UiComponent
	 */
	public function setContext( \DOMDocument $doc )
	{
		$this->context = $doc;
		return $this;
	}

	/**
	 * @return DOMDocument
	 */
	public function getContext()
	{
		return $this->context;
	}
	
	/**
	 * @param UiComposer $composer
	 * @return \BaseXMS\UiComposer\UiComponent\UiComponent
	 */
	public function setUiComposer( UiComposer $composer )
	{
		$this->uiComposer = $composer;
		return $this;
	}
	
	/**
	 * @return \BaseXMS\UiComposer\UiComposer
	 */
	public function getUiComposer()
	{
		return $this->uiComposer;
	}
	
	/**
	 * @return \BaseXMS\UiComposer\UiComponent\mixed
	 */
	protected function getRenderResult()
	{
		if( ! $this->isRendered() )
		{
			// Let's check the cache first
			if( $this instanceof Cacheable &&
				$this->uiComposer->getServiceLocator()->has( 'cache' ) )
			{
				$cache = $this->uiComposer->getServiceLocator()->get( 'cache' );
				$key   = $this->getCacheKey();
			
				if( $cache->hasItem( $key ) )
				{
					$this->uiComposer->getServiceLocator()->get( 'log' )->debug( 'RenderResult cache hit.' );
			
					$this->renderResult = $cache->getItem( $key );
				}
				else
				{
					$this->uiComposer->getServiceLocator()->get( 'log' )->debug( 'RenderResult cache miss.' );
			
					$this->render();
			
					// Add to cache
					$cache->setItem( $key, $this->renderResult );
					$cache->setXml( $key, $this->getContext() );
				}
			}
			else
			{
				$this->render();
			}
		}
				
		return $this->renderResult;
	}
}

?>