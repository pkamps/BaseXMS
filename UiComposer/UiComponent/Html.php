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
	
	protected function getJsFiles()
	{
		return array( 'js/jquery-1.9.1.js' );
	}
}

?>