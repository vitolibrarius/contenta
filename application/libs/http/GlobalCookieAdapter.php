<?php

namespace http;

use \Cookies as Cookies;

class PhpCookieAdapter implements \interfaces\GlobalAdapter
{
	public function allValues()
	{
		return $_COOKIE;
	}

    public function get($key, $default = null)
    {
        return isset($_COOKIE[$key]) ? $_COOKIE[$key] : $default;
    }

    public function set($key, $value)
    {
		$domain = "." . parse_url(Config::Url(), PHP_URL_HOST);
		setcookie( $key, $value, time() + COOKIE_RUNTIME, Config::Web('/'), $domain);
    }

    public function delete($key)
    {
		$domain = "." . parse_url(Config::Url(), PHP_URL_HOST);
		// set the rememberme-cookie to ten years ago (3600sec * 365 days * 10).
		// that's obviously the best practice to kill a cookie via php
		// @see http://stackoverflow.com/a/686166/1114320
		setcookie($key, false, time() - (3600 * 3650), Config::Web('/'), $domain);
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
