<?php 

namespace BaseXMS\UiComponent;

class Factory
{
	public static function factory( $services, $class, $data )
	{
		$test = array(
				'class' => $class,
				'')
		$rules = self::getRules();
		
		foreach( $rules as $concreteClass => $rule )
		{
			if( count( array_intersect( $entry, $event ) ) == count( $entry ) )
			{
				$return = true;
				break;
			}
		}
		
		$class = class_exists( $class ) ? $class : '\BaseXMS\UiComponent\UiComponent';

		$model = new $class;
		$model->init( $services, $data );
		
		return $model;
	}
	
	private static function getRules()
	{
		return array(
				'Html' => array( 'class' => '\BaseXMS\UiComponent\Html' )
		);
	}
}

?>