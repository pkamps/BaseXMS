<?php 

namespace BaseXMS\UiComponent;

use BaseXMS\Stdlib\DOMXpath;

class Factory
{
	static $rules;
	
	/**
	 * 
	 * @param UiComposer $composer
	 * @param DomElement $incElement
	 * @return UiComponent
	 */
	public static function factory( $composer, $incElement )
	{
		$services = $composer->getServices();
		
		$xpath = self::getXPathContext( $incElement, $composer->getData() );
		$class = $xpath->getFirstMatchingXpath( self::getRules( $services ) );
		
		if( !class_exists( $class ) )
		{
			$services->get( 'log' )->warn( 'Unknown UiComponent: ' . $class );
			$class = '\BaseXMS\UiComponent\UiComponent';
		}
		
		$services->get( 'log' )->info( 'Loading UiComponent: ' . $class );
		
		$component = new $class;
		$component->setComposer( $composer );
		$component->setIncElement( $incElement );
		
		return $component;
	}
	
	private static function getRules( $services )
	{
		$rules = array();
		
		if( !isset( self::$rules ) )
		{
			$config = $services->get( 'application' )->getConfig();
	
			$designs = $config[ 'designs' ];
			
			if( !empty( $designs ) )
			{
				ksort( $designs );
	
				foreach( array_reverse( $designs ) as $design )
				{
					if( isset( $config[ 'designrules' ][ $design ] ) )
					{
						$rules = $rules + $config[ 'designrules' ][ $design ]; 
					}
				}
			}

			self::$rules = $rules;
		}
		
		return self::$rules;
	}
	
	private static function getXPathContext( $incElement, $data )
	{
		$attributes = $incElement->attributes;
		$context = '<context>';
		for( $i = 0; $i < $attributes->length; ++$i )
		{
			$item = $attributes->item( $i );
			$context .= '<'. $item->name .'>'. $item->value .'</'. $item->name .'>';
		}
		$context .= '<id>'. $data->attributes()->id . '</id>';
		$context .= '</context>';
		//	<raw>'. $data->raw->saveXML() . '</raw>
		
		$doc = new \DOMDocument();
		$doc->loadXML( $context );
		return new DOMXpath( $doc );
	}
}

?>