<?php

namespace model\network;

use \DataObject as DataObject;
use \Model as Model;
use \Logger as Logger;

use \model\network\Endpoint_Type as Endpoint_Type;

/* import related objects */
use \model\network\Endpoint as Endpoint;
use \model\network\EndpointDBO as EndpointDBO;

class Endpoint_TypeDBO extends _Endpoint_TypeDBO
{
	public function favicon()
	{
		if (isset($this->favicon_url) ) {
			return $this->favicon_url;
		}

		return Config::Web('public/img/Logo_favicon.png');
	}

	/**
	const Newznab =		"Newznab";
	const RSS =			"RSS";
	const ComicVine =	"ComicVine";
	const SABnzbd =		"SABnzbd";
	const PreviewsWorld = "PreviewsWorld";
	*/
	public function isType( $str = 'RSS')
	{
		return (isset($this->code) && $this->code == $str);
	}

	public function isRSS()
	{
		return $this->isType(Endpoint_Type::RSS);
	}

	public function isSABnzbd()
	{
		return $this->isType(Endpoint_Type::SABnzbd);
	}

	public function isNewznab()
	{
		return $this->isType(Endpoint_Type::Newznab);
	}
}

?>
