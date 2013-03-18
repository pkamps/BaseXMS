<?php 

namespace BaseXMS\UiComposer\UiComponent;

use BaseXMS\UiComposer\UiComposer;

class Content extends HtmlWidget
{
	public function getXml()
	{
		return '<div id="content">' . $this->uiComposer->getData()->raw->saveXML() . '</div>';
	}
}

?>