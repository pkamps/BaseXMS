<?php 

namespace BaseXMS\UiComponent;

use BaseXMS\UiComposer;

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
}

?>