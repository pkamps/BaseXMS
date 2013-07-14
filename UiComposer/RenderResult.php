<?php 

namespace BaseXMS\UiComposer;

class RenderResult
{
	/**
	 * @var string
	 */
	public $output;
	
	private $referencedObjects;
	
	/**
	 * @param string $string
	 */
	public function __construct( $string = '' )
	{
		$this->output = $string;
		$this->referencedObjects = array();
	}
	
	/**
	 * @param unknown_type $string
	 * @return RenderResult
	 */
	public function setOutput( $string )
	{
		$this->output = $string;
		return $this;
	}
	
	/**
	 * @return string
	 */
	public function getOutput()
	{
		return $this->output;
	}
	
	/**
	 * @param string $id
	 * @param object $object
	 * @return \BaseXMS\UiComposer\RenderResult
	 */
	public function setReferencedObject( $id, $object )
	{
		$this->referencedObjects[ $id ] = $object;
		
		return $this;
	}
	
	/**
	 * @param string $id
	 * @return object
	 */
	public function getReferencedObject( $id )
	{
		return isset( $this->referencedObjects[ $id ] ) ? $this->referencedObjects[ $id ] : null;
	}
}

?>