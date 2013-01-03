<?php 

namespace BaseXMS\UiComponent;
use BaseXMS\UiComposer;

class Head extends HtmlWidget
{
	public function getXml( UiComposer $composer )
	{
		return
'<head>
	<title>Default page title</title>
	<include type="inline-css" />
</head>';
	}
}

?>