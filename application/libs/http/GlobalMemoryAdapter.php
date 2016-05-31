<?php

namespace http;

class GlobalMemoryAdapter implements \interfaces\GlobalAdapter
{
    private $global_sim = array();
	public function allValues()
	{
		return $this->global_sim;
	}

    public function get($key, $default = null)
    {
        return isset($this->global_sim[$key]) ? $this->global_sim[$key] : $default;
    }

    public function delete($key)
    {
    	unset( $this->global_sim[$key] );
    }

    public function set($key, $value)
    {
        $this->global_sim[$key] = $value;
    }

    public function getArray($key, array $default = null)
    {
    	return $this->get($key, $default);
    }

    public function addToArray($key, $value)
    {
		$this->global_sim[$key][] = $value;
    }

    public function clearArray($key)
    {
    	unset( $this->global_sim[$key] );
    }
}
