<?php

namespace BaseXMS\Log;

use Zend\Log\Filter\FilterInterface;

class Mock extends \Zend\Log\Writer\Mock
{
	protected $name;
	
	public function setName( $name )
	{
		$this->name = $name;
	}
	
	public function getName()
	{
		return $this->name;
	}
}
