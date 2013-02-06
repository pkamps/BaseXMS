<?php 

namespace BaseXMS\Stdlib;

class DOMDocument extends \DOMDocument
{
	public static function gracefulLoadXml( $xmlString, $options = null )
	{
		set_error_handler( function ( $errno, $errstr, $errfile, $errline )
		{
			if ($errno==E_WARNING && (substr_count($errstr,"DOMDocument::loadXML()")>0))
			{
				throw new \DOMException( $errstr );
			}
			else
			{
				return false;
			}
		});

		$doc = self::loadXML( $xmlString, $options );
		restore_error_handler();
		
		return $doc;
	}
	
	public function loadSimpleXml( $simpleXml )
	{
		$domnode = dom_import_simplexml( $simpleXml );
		$domnode = $doc->importNode( $domnode, true );
		$this->appendChild( $domnode );
	}
}

?>