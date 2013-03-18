<?php 

namespace BaseXMS\UiComposer\UiComponent;

use BaseXMS\Stdlib\DOMXpath;
use BaseXMS\Stdlib\DOMDocument;
use BaseXMS\UiComposer\UiComposer;

class Factory
{
	static $rules;
	
	/**
	 * 
	 * @param UiComposer $composer
	 * @param DomElement $incElement
	 * @return UiComponent
	 */
	public static function factory( UiComposer $composer, $incElement )
	{
		$services = $composer->getServiceLocator();
		
		$context = self::getContextDescription( $incElement, $composer->getData() );
		
		$xpath = new DOMXpath( $context );
		$class = $xpath->getFirstMatchingXpath( self::getRules( $services ) );

		if( !class_exists( $class ) )
		{
			$services->get( 'log' )->warn( 'Unknown UiComponent: ' . $class );
			$class = '\BaseXMS\UiComposer\UiComponent\UiComponent';
		}
		
		$services->get( 'log' )->info( 'Loading UiComponent: ' . $class );
		
		$component = new $class;
		$component->setUiComposer( $composer );
		$component->setContext( $context );
		
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
	
	private static function getContextDescription( $incElement, $data )
	{
		$attributes = $incElement->attributes;
		$context = '<context type="includetag">';
		for( $i = 0; $i < $attributes->length; ++$i )
		{
			$item = $attributes->item( $i );
			$context .= '<'. $item->name .'>'. $item->value .'</'. $item->name .'>';
		}
		// Will break if we decide to store $data differently
		$context .= $data->saveXML( $data->firstChild );
		$context .= '<created>' . time() . '</created>';
		$context .= '</context>';
		
		$doc = new DOMDocument();
		$doc->loadXML( $context );
		
		//echo $doc->saveXML();
		
		return $doc;
	}
}

?>