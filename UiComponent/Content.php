<?php 

namespace BaseXMS\UiComponent;

use BaseXMS\UiComposer;

class Content extends HtmlWidget
{
	public function getXml( UiComposer $composer )
	{
		return '<div id="content">' . $this->data->raw->saveXML() . '</div>';
	}
}

?>