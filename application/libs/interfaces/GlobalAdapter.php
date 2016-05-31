<?php

namespace interfaces;

interface GlobalAdapter
{
	public function allValues();
    public function get($key, $default = null);
    public function set($key, $value);
    public function delete($key);

    public function getArray($key, array $default = null);
    public function addToArray($key, $value);
    public function clearArray($key);
}
