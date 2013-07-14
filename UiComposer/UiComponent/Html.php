<?php 

namespace BaseXMS\UiComposer\UiComponent;

use BaseXMS\UiComposer\UiComposer;

class Html extends HtmlWidget
{
	/**
	 * @return DOMNode
	 */
	public function getXml()
	{
		return
'<html>
	<include type="head"></include>
	<include type="body"></include>
</html>';
	}
	
	protected function getJsFileContent()
	{
		$return  = file_get_contents( 'module/BaseXMSZend/public/js/jquery-1.9.1.js' );
		$return .= file_get_contents( 'module/BaseXMSZend/public/js/bootstrap.min.js' );
		//jquery-ui-1.10.1.custom.min.js
		
		return $return;
	}
	
	protected function getCssFileContent()
	{
		$return  = file_get_contents( 'module/BaseXMSZend/public/css/bootstrap.min.css' );
		$return .= file_get_contents( 'module/BaseXMSZend/public/css/bootstrap-responsive.min.css' );
	
		return $return;
	}
	
}

?>