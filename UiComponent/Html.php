<?php 

namespace BaseXMS\UiComponent;

use BaseXMS\UiComposer;

class Html extends HtmlWidget
{
	/**
	 * @return DOMNode
	 */
	public function getXml( UiComposer $composer )
	{
		return
'<html>
	<include type="head"></include>
	<include type="body"></include>
</html>';
	}
}

?>