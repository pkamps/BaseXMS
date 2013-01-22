<?php 

namespace BaseXMS\UiComponent;

use BaseXMS\UiComposer;

class Content extends HtmlWidget
{
	public function getXml()
	{
		return '<div id="content">' . $this->composer->getData()->raw->saveXML() . '</div>';
	}
}

?>