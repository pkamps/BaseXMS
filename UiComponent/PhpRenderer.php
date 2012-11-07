<?php 

namespace BaseXMS\UiComponent;

use Zend\View\Renderer\PhpRenderer as ZendRenderer;
use Zend\View\Resolver\TemplatePathStack;

class PhpRenderer extends UiComponent
{
	public function render( $format )
	{
		$format = 'read';
		$modules = array( 'Sandbox' );
		
		$class_identifier = str_replace( '\\', '/', get_class( $this ) );
		
		$resolver = new TemplatePathStack();
		
		foreach( $modules as $module )
		{
			$path = 'module/'. $module .'/view/objects/' . $class_identifier . '/';
			//TODO: add logging here
			$resolver->addPath( $path );
		}

		$renderer = new ZendRenderer();
		$renderer->data = $this->data;
		$renderer->setResolver( $resolver );
		$templateFile = $renderer->resolver( $format );
		
		if( $templateFile )
		{
			$this->services->get( 'log' )->info( 'Loading template for class: '. $class_identifier . ' Tempalte: ' . $templateFile );
			
			$return = $renderer->render( $format );
		}
		else
		{
			$this->services->get( 'log' )->err( 'Failed to locate template for class: '. $class_identifier . ' View: read' );
			$return = 'Missing template';
		}
		
		return $return;
	}
}

?>