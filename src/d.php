<?php

/**
 * @copyright (c) 2010-2018 Netsilik (http://netsilik.nl)
 * @license       MIT
 */

if (!function_exists('d')) {
	/**
	 * Debug funcion for dumping contents and type of a variable
	 *
	 * @param mixed $var    The variable to output for debug information
	 * @param bool  $return Optional flag to have the result returned (without any escaping) instead of echoed
	 * @param int   $depth  The indentation level, used for recursive calls
	 */
	function d($var, bool $return = false, int $depth = 0)
	{
		static $instances = [];
		
		if ($depth == 8) {
			return "*MAX DEPTH*\n";
		}
		
		$indent = 3;
		$types  = ['array'    => 'array',
				   'boolean'  => 'bool',
				   'double'   => 'float',
				   'integer'  => 'int',
				   'NULL'     => 'NULL',
				   'object'   => '',
				   'resource' => 'resource',
				   'string'   => 'string'
		];
		$type   = gettype($var);
		
		$out = $types[ $type ];
		if ($type == 'array') {
			$out .= '(' . count($var) . ") {\n";
			foreach ($var as $key => $value) {
				$out .= str_repeat(' ', $depth * $indent + $indent) . '[' . $key . '] => ';
				$out .= d($value, true, $depth + 1);
			}
			$out .= str_repeat(' ', $depth * $indent) . '}';
		} elseif ($type == 'object') {
			// recusion protection
			ob_start();
			var_dump($var);
			if (1 === preg_match('/#\d+/', substr(ob_get_clean(), 0, 256), $grep)) {
				if (in_array($grep[0], $instances)) {
					return "*RECURSION*\n";
				}
				$instances[] = $grep[0];
			}
			
			$rc  = new ReflectionClass($var);
			$out .= 'object(' . $rc->getName() . ") {\n";
			
			// defined properties
			$public = [];
			foreach ($rc->getProperties() as $property) {
				$out .= str_repeat(' ', $depth * $indent + $indent);
				
				if ($property::IS_PUBLIC == ($property->getModifiers() & $property::IS_PUBLIC)) {
					$public[] = $property->name;
					$out      .= 'public';
				} elseif ($property::IS_PROTECTED == ($property->getModifiers() & $property::IS_PROTECTED)) {
					$out .= 'protected';
				} else {
					$out .= 'private';
				}
				if ($property::IS_STATIC == ($property->getModifiers() & $property::IS_STATIC)) {
					$out .= ' static';
				}
				$out .= ': $' . $property->name . ' = ';
				
				$property->setAccessible(true);
				$out .= d($property->getValue($var), true, $depth + 1);
			}
			
			// dynamic properties
			foreach ($var as $name => $value) {
				if (!in_array($name, $public)) {
					$out .= str_repeat(' ', $depth * $indent + $indent);
					$out .= 'dynamic: $' . $name . ' = ';
					$out .= d($value, true, $depth + 1);
				}
			}
			$out .= str_repeat(' ', $depth * $indent) . '}';
			
		} else {
			if ($type == 'string') {
				$out .= '(' . strlen($var) . ") '" . $var . '\'';
			} elseif ($type == 'double' || $type == 'integer') {
				$out .= '(' . $var . ')';
			} elseif ($type == 'boolean') {
				$out .= '(' . ($var ? 'true' : 'false') . ')';
			} elseif ($type == 'resource') {
				$out .= '(' . intval($var) . ') of type (' . get_resource_type($var) . ')';
			}
		}
		$out .= "\n";
		
		if ($depth == 0) { // reset
			$instances = [];
		}
		
		if ($return) {
			return $out;
		}
		
		if (0 === $depth) {
			$bt         = debug_backtrace();
			$file       = isset($bt[0]['file']) ? $bt[0]['file'] : '?';
			$line       = isset($bt[0]['line']) ? $bt[0]['line'] : '?';
			$calledFrom = $file . ':' . $line;
		}
		
		if (PHP_SAPI === 'cli') { // output as text
			echo "\n" . $calledFrom . "\n" . $out;
		} else { // output as html
			echo '<pre style="margin: 0; padding: 2px 4px; background-color:#F4F4F4; line-height: 15px; display: inline-block;"><span style="color:#880000; font-size: 11px;">' . $calledFrom . '</span><br><span style="color:#000038; font-size: 13px;">' . htmlspecialchars($out, ENT_NOQUOTES | ENT_XHTML) . '</span></pre>';
		}
	}
}
