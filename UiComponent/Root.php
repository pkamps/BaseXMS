<?php 

namespace BaseXMS\UiComponent;

use BaseXMS\UiComposer;

class Root extends HtmlWidget
{
	/**
	 * @return DOMNode
	 */
	public function getXml()
	{
		return
'<include type="html"></include>';
	}
}

?>