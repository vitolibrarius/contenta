<?php

namespace http;

class GlobalGETAdapter implements \interfaces\GlobalAdapter
{
	public function __toString()
	{
		return "GlobalGETAdapter " . var_export($_GET, true);
	}

	public function allValues()
	{
		return $_GET;
	}

    public function get($key, $default = null)
    {
        return isset($_GET[$key]) ? $_GET[$key] : $default;
    }

    public function delete($key)
    {
    }

    public function set($key, $value)
    {
    }

    public function getArray($key, array $default = null)
    {
    	return $this->get($key, $default);
    }

    public function addToArray($key, $value)
    {
    }

    public function clearArray($key)
    {
    }
}
