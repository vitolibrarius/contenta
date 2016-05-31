<?php

namespace http;

class PhpPOSTAdapter implements \interfaces\GlobalAdapter
{
	public function allValues()
	{
		return $_POST;
	}

    public function get($key, $default = null)
    {
        return isset($_POST[$key]) ? $_POST[$key] : $default;
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
