<?php
namespace BaseXMS;

class Node
{
	protected $xmlDb;
	
	public function __construct( $xmlDb )
	{
		$this->xmlDb = $xmlDb;
	}
	
	public function read( $id )
	{
		$query = '//node[properties/altFullPaths//entry[@path="' . $fullPath . '"]]/content';
		return $this->xmlDb->execute( $query, 'simplexml' );
	}
}

?>