<?php 

namespace BaseXMS\UiComponent;

class Factory
{
	public static function factory( $services, $type, $data )
	{
		$class = self::xpathMatch( self::getXPathContext( $type, $data ), self::getRules() );
		
		if( !class_exists( $class ) )
		{
			$services->get( 'log' )->warn( 'Unknown UiComponent: ' . $class );
			$class = '\BaseXMS\UiComponent\UiComponent';
		}
		
		$services->get( 'log' )->info( 'Loading UiComponent: ' . $class );
		
		$component = new $class;
		$component->init( $data );
		
		return $component;
	}
	
	private static function getRules()
	{
		//TODO: move to settings
		return array(
				'\BaseXMSInspect\UiComponent\Settings' => '/context[./type/text() = "content" and ./id/text() = "7"]',
				'\BaseXMS\UiComponent\Html'            => '/context/type[text() = "html"]',
				'\BaseXMS\UiComponent\Head'            => '/context/type[text() = "head"]',
				'\BaseXMS\UiComponent\Body'            => '/context/type[text() = "body"]',
				'\BaseXMS\UiComponent\Debug'           => '/context/type[text() = "debug"]',
				'\BaseXMS\UiComponent\Content'         => '/context/type[text() = "content"]',
				'\BaseXMS\UiComponent\Head\InlineCss'  => '/context/type[text() = "inline-css"]'
		);
	}
	
	private static function getXPathContext( $type, $data )
	{
		$context =
'<context>
	<type>'. $type .'</type>
	<id>'. $data->attributes()->id . '</id>
</context>';

		//	<raw>'. $data->raw->saveXML() . '</raw>
		
		$doc = new \DOMDocument();
		$doc->loadXML( $context );
		return new \DOMXpath( $doc );
	}
	
	private static function xpathMatch( $xPathObj, $tests )
	{
		$return = false;
		
		if( !empty( $tests ) )
		{
			foreach( $tests as $key => $test )
			{
				$result = $xPathObj->query( $test );
				
				if( $result->length )
				{
					$return = $key;
					break;
				}
			}
		}
		
		return $return;
	}
}

?>