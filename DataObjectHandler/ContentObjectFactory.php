<?php 

namespace BaseXMS\DataObjectHandler;

class ContentObjectFactory
{
	public static function factory( $data )
	{
		$class = (string) $data->attributes()->class;
		
		$class = class_exists( $class ) ? $class : '\BaseXMS\ViewModel\ViewModel';

		$model = new $class;
		$model->init( $services, $data );
		
		return $model;
	}
}

?>