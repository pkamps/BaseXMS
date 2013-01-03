<?php 

/* PK: not sure if we need a factory - basically the contentobject structure is always the same */
namespace BaseXMS\DataObjectHandler\ContentObject;

class Factory
{
	public static function factory( $data )
	{
		$class = (string) $data->attributes()->class;
		
		$class = class_exists( $class ) ? $class : '\BaseXMS\DataObjectHandler\ContentObject';

		$model = new $class;
		$model->init( $services, $data );
		
		return $model;
	}
}

?>