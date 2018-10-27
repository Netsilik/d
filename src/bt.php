<?php

/**
 * @copyright (c) 2010-2018 Netsilik (http://netsilik.nl)
 * @license       MIT
 */

if (!function_exists('bt')) {
	/**
	 * Debug funcion for dumping call backtrace
	 * @param int $limit The maximum number of backtrace steps to display
	 * @param bool $return Optional flag to have the result returned (without any escaping) instead of echoed
	 */
	function bt($limit, $return = false)
	{
		$bt = debug_backtrace();
		
		$lines = [];
		for ($i = 0; $i < min($limit, count($bt)); $i++) {
			$step = $bt[$i];
			
			$args = [];
			foreach ($step['args'] as $arg) {
				switch (gettype($arg)) {
					case 'object':
						$args[] = get_class($arg);
						break;
					case 'array':
						$args[] = '['.(count($arg)?count($arg).' elements':'').']';
						break;
					case 'resource':
						$args[] = get_resource_type($arg);
						break;
					case 'boolean':
						$args[] = $arg ? 'true' : 'false';
						break;
					case 'string':
						$args[] = "'".addcslashes($arg, "'\\")."'";
						break;
					default: // double, integer
						$args[] = (null === $arg) ? 'NULL' : $arg;
				}
			}
			
			
			$line = '#'.str_pad($i, 2).' ';
			if (isset($step['class'])) {
				$line .= $step['class'].$step['type'];
			}
			$line .= $step['function'].'('.implode(', ', $args).') called at ';
			
			if (isset($step['file'])) {
				$line .= '['.$step['file'].':'.$step['line'].']';
			} else {
				$line .= '[undefined]';
			}
			
			$lines[] = $line;
		}
		$out = implode("\n", $lines);
		
		if ($return) {
			return $out;
		}
		
		if (PHP_SAPI === 'cli') { // output as text
			echo "\n".$out;
		} else { // output as html
			echo '<pre style="margin: 0;"><span style="color:#000038; font-size: 13px; line-height: 15px; background-color:#F4F4F4;">'.htmlspecialchars($out, ENT_NOQUOTES | ENT_XHTML).'</span></pre>';
		}
	}
}
