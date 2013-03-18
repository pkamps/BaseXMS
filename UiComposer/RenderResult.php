<?php 

namespace BaseXMS\UiComposer;

class RenderResult
{
	/**
	 * @var string
	 */
	public $output;
	
	/**
	 * @param string $string
	 */
	public function __construct( $string = '' )
	{
		$this->output = $string;
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
}

?>