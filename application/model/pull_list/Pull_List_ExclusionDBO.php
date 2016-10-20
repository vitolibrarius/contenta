<?php

namespace model\pull_list;

use \DataObject as DataObject;
use \Model as Model;
use \Logger as Logger;

use \model\pull_list\Pull_List_Exclusion as Pull_List_Exclusion;

/* import related objects */
use \model\network\Endpoint_Type as Endpoint_Type;
use \model\network\Endpoint_TypeDBO as Endpoint_TypeDBO;

class Pull_List_ExclusionDBO extends _Pull_List_ExclusionDBO
{
	public function isExcluded($source = '')
	{
		if ( is_string($source) && empty($source) == false) {
			if ( preg_match('/(?:\b'.$this->pattern.'\b)+/', $source) ) {
				return true;
			}
		}
		return false;
	}
}

?>
