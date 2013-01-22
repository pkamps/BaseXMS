<?php 

namespace BaseXMS\UiComponent;

use BaseXMS\UiComposer;

class UiComponent
{
	protected $composer;
	protected $incElement;
	
	public $needsRerender = false;
		
	/**
	 * @param UiComposer $composer
	 * @return DOMNode
	 */
	public function render()
	{
		/* not sure what to do here
		$content = '<span>Default from UiComponent</span>';
		
		$doc = new \DOMDocument();
		$doc->loadXML( $content );
		
		return $doc->firstChild;
		*/
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
	
}

?>