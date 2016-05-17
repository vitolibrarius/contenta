<?php

namespace utilities;

class TemplateGenerator
{
    var $values = array();
    var $path_to_file= array();
    var $destinationRelationMap = null;

    function __construct($path_to_file, array $defaultValues = null)
    {
		if( file_exists($path_to_file) == false ) {
			trigger_error("Template File not found! '" . $path_to_file . "'",E_USER_ERROR);
			return;
		}
        $this->path_to_file = $path_to_file;

		/* set some default values */
        $this->dateString(date('Y-m-d'));
        $this->timeString(date('H:i:s'));
        $this->datetime(date('Y-m-d H:i:s'));
        $this->template_filename(basename($this->path_to_file));

		if (is_array($defaultValues) ) {
			$this->values = array_merge($this->values, $defaultValues);
		}
    }

	public function __call($method, $args)
	{
		if ( is_array($args) && count($args) == 1) {
			return array_setValueForKeypath( $method, $args[0], $this->values );
		}
		return array_valueForKeypath( $method, $this->values );
	}

    public function generate()
    {
//         ob_start();
//         include $this->path_to_file;
//         $content = ob_get_contents();
//         ob_end_clean();
//         return $content;

		$openDelimiter = '{';
		$closeDelimiter = '}';
		$template = file_get_contents($this->path_to_file);

        $keys = array();
        foreach ($this->values as $key => $value) {
            $keys[] = $openDelimiter . $key . $closeDelimiter;
        }
        return str_replace($keys, $this->values, $template);
    }
}
