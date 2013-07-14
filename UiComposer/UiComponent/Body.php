<?php 

namespace BaseXMS\UiComposer\UiComponent;

use BaseXMS\UiComposer\UiComposer;
use BaseXMS\UiComposer\Cacheable;

class Body extends HtmlWidget //implements Cacheable
{
	public function getXml()
	{
		return
'<body>
	<include type="content" />
	<include type="jsfile" />
</body>';
	}
	
	public function getCacheKey()
	{
		return $this->getContext()->queryToValue( '/context/type' );
	}
}

?>