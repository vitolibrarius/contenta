<?php

class Menu {

	// Associative array of list items
	public $items = array();

	// Associative array of attributes for list
	public $attrs = array();

	// Current active URL
	public $current;


	/**
	 * Creates and returns a new Menu object
	 *
	 * @chainable
	 * @param   array   Array of list items (instead of using add() method)
	 * @return  menu
	 */
	public static function factory(array $items = NULL)
	{
		return new Menu($items);
	}

	/**
	 * Constructor, globally sets $items array
	 *
	 * @param   array   Array of list items (instead of using add() method)
	 * @return  void
	 */
	public function __construct(array $items = NULL)
	{
		$this->items = $items;
	}

	/**
	 * Add's a new list item to the menu
	 *
	 * @chainable
	 * @param   string   Title of link
	 * @param   string   URL (address) of link
	 * @param   menu	 Instance of class that contain children
	 * @return  menu
	 */
	public function add($title, $url, menu $children = NULL)
	{
		$this->items[] = array
		(
			'title'	=> $title,
			'url'	  => Config::Web($url),
			'children' => is_object($children) ? $children->items : NULL,
		);

		return $this;
	}

	public function addCallback($title, Closure $callback = null)
	{
		if ( is_null($callback) == false ) {
			$this->items[] = array(
				'title'	=> $title,
				'callback' => $callback
			);
		}
	}

	/**
	 * Renders the HTML output for the menu
	 *
	 * @param   array   Associative array of html attributes
	 * @param   array   Associative array containing the key and value of current url
	 * @param   array   The parent item's array, only used internally
	 * @return  string  HTML unordered list
	 */
	public function render(array $attrs = NULL, $current = NULL, array $items = NULL)
	{
		$items = empty($items) ? $this->items : $items;
		$current = empty($current) ? $this->current : $current;
		$attrs = empty($attrs) ? $this->attrs : $attrs;

		$menu = '<ul' . self::attributes($attrs) . '>';

		foreach ($items as $key => $item)
		{
			$has_children = isset($item['children']);
			$current_title = (isset($item['title']) ? $item['title'] : '');
			$current_class = "";

			if ( empty($current) == false && isset($item['callback']) == false) {
				$current_class = self::current($current, $item);
			}

			$classes = self::attributes(array('class' => $current_class));

			$menu .= '<!-- ' . $current_title . ' -->';
			$menu .= '<li'.$classes.'>';
			if ( isset($item['callback']) ) {
				$menu .= $item['callback']();
			}
			else {
				$menu .= '<a href="'.$item['url'].'">'.$current_title.'</a>';
			}
			$menu .= $has_children ? $this->render(array('class'=>'sub-menu'), $current, $item['children']) : NULL;
			$menu .= '</li>';
		}

		$menu .= '</ul>';
		return $menu;
	}

	/**
	 * Renders the HTML output for menu without any attributes or active item
	 *
	 * @return   string
	 */
	public function __toString()
	{
		return $this->render();
	}

	/**
	 * Easily set the current url, or list attributes
	 *
	 * @param   mixed   Value to set to
	 * @return  void
	 */
	public function __set($key, $value)
	{
		$this->attrs[$key] = $value;
	}

	/**
	 * Get the current url or a list attribute
	 *
	 * @return   mixed   Value of key
	 */
	public function __get($key)
	{
		if (isset($this->attrs[$key]))
			return $this->attrs[$key];
	}

	/**
	 * Nicely outputs contents of $this->items for debugging info
	 *
	 * @return   string
	 */
	public function debug()
	{
		return '<pre>'.print_r($this->items, TRUE).'</pre>';
	}

	/**
	 * Compiles an array of HTML attributes into an attribute string.
	 *
	 * @param   string|array  array of attributes
	 * @return  string
	 */
	protected static function attributes($attrs)
	{
		if (empty($attrs))
			return '';

		if (is_string($attrs))
			return ' '.$attrs;

		$compiled = '';
		foreach ($attrs as $key => $val) {
			$compiled .= ' '.$key.'="'.htmlspecialchars($val).'"';
		}

		return $compiled;
	}

	/**
	 * Figures out if items are parents of the active item.
	 *
	 * @param   array   The current url array (key, match)
	 * @param   array   The array to check against
	 * @return  bool
	 */
	protected static function current($current, array $item)
	{
		if ( strtolower($current) === strtolower($item['url']) ) {
			return 'active current';
		}
		else if (self::active($item, $current, 'url')) {
			return 'active';
		}

		return '';
	}

	/**
	 * Recursive function to check if active item is child of parent item
	 *
	 * @param   array   The list item
	 * @param   string  The current active item
	 * @param   string  Key to match current against
	 * @return  bool
	 */
	public static function active($array, $value, $key)
	{
		foreach ($array as $val)
		{
			if (is_array($val))
			{
				if (self::active($val, $value, $key))
					return TRUE;
			}
			else
			{
				if ($array[$key] === $value)
					return TRUE;
			}
		}

		return FALSE;
	}

}
