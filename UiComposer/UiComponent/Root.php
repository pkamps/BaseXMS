<?php 

namespace BaseXMS\UiComposer\UiComponent;

use BaseXMS\UiComposer\UiComposer;

class Root extends HtmlWidget
{
	/**
	 * @return DOMNode
	 */
	public function getXml()
	{
		return
'<include type="html" ttl="10"></include>';
	}
}

?>