<?php 

namespace BaseXMS\UiComponent;

use BaseXMS\UiComposer;

class Body extends HtmlWidget
{
	public function getXml()
	{
		return
'<body>
	<include type="content" />
	<include type="debug" />
</body>';
	}
}

?>