<?php

if (!function_exists('bt')) {
	/**
	 * Debug funcion for dumping contents and type of a variable
	 * @param mixed $var The variable to output for debug information
	 * @param bool $return Optional flag to have the result returned (without any escaping) instead of echoed
	 * @param depth The indentation level, used for recursive calls
	 */
	function bt($var, $return = false)
	{
	
		echo '<pre style="margin: 0;"><span style="color:#000038; font-size: 13px; line-height: 15px; background-color:#F4F4F4;">';
		debug_print_backtrace(2, 8);
		echo '</span></pre>';
	}
}
