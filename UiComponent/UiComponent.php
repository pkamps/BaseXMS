<?php 

namespace BaseXMS\UiComponent;

use BaseXMS\UiComposer;

class UiComponent
{
	protected $data;
	public $needsRerender = false;
	
	public function init( $data )
	{
		$this->data = $data;
	}
	
	/**
	 * @param UiComposer $composer
	 * @return DOMNode
	 */
	public function render( UiComposer $composer, $element )
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
	public function rerender( UiComposer $composer )
	{
		$this->needsRerender = false;
		
		return $this;
	}
}

?>