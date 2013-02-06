<?php 

namespace BaseXMS\UiComponent;

use BaseXMS\UiComposer;

class UiComponent
{
	protected $composer;
	protected $incElement;
	
	public $needsRerender = false;
	
	/**
	 * @param DOMDocumentFragment $fragment
	 * @return \BaseXMS\UiComponent\UiComponent
	 */
	public function render( \DOMDocumentFragment $fragment )
	{
		$content = '<section>Default from UiComponent</section>';
		$fragment->appendXML( $content );
		
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
	 * @param mixed $data
	 * @return string
	 */
	protected function storeData( $instanceData )
	{
		$instanceId = spl_object_hash( $this );
		
		$data = $this->composer->getSharedData();
		$data->componentData[ $instanceId ] = $instanceData;
		
		return $instanceId;
	}
	
	/**
	 * @param string $instanceId
	 */
	protected function readData( $instanceId )
	{
		$data = $this->composer->getSharedData();
		return $data->componentData[ $instanceId ];
	}
	
	/**
	 * @param UiComposer $composer
	 * @return \BaseXMS\UiComponent\UiComponent
	 */
	public function setComposer( UiComposer $composer )
	{
		$this->composer = $composer;
		return $this;
	}

	/**
	 * @param unknown_type $element
	 * @return \BaseXMS\UiComponent\UiComponent
	 */
	public function setIncElement( $element )
	{
		$this->incElement = $element;
		return $this;
	}

	/**
	 * Get instance id
	 */
	public function getId()
	{
		return spl_object_hash( $this );
	}
}

?>