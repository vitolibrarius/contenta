<?php

namespace model\pull_list;

use \DataObject as DataObject;
use \Model as Model;
use \Logger as Logger;

use \model\pull_list\Pull_List_Expansion as Pull_List_Expansion;

/* import related objects */
use \model\network\Endpoint_Type as Endpoint_Type;
use \model\network\Endpoint_TypeDBO as Endpoint_TypeDBO;

class Pull_List_ExpansionDBO extends _Pull_List_ExpansionDBO
{
	public function preg_quoted_pattern()
	{
		if ( isset($this->pattern) ) {
			return preg_quote($this->pattern);
		}
		return null;
	}
}

?>
