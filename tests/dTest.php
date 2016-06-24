<?php
namespace Netsilik\Tests;

class dTest extends \PHPUnit_Framework_TestCase {
	
	/**
	 * Test string
	 */
	public function testString() {
		$this->expectOutputString("\nstring(5) 'Alice'\n");
		
		d('Alice');
	}
	
	/**
	 * Test integer
	 */
	public function testInteger() {
		$this->expectOutputString("\nint(123)\n");
		
		d(123);
	}
	
	/**
	 * Test boolean
	 */
	public function testBoolean() {
		$this->expectOutputString("\nbool(false)\n");
		
		d(false);
	}
	
	/**
	 * Test (stream) resource
	 */
	public function testResource() {	
		if (false === ($fp = @tmpfile())) {
			$this->fail('Could not create resource required for test case');
		}
		
		$this->expectOutputRegex("/\nresource\(\d+\) of type \(stream\)\n/");
		d($fp);
		@fclose($fp);
	}
	
	/**
	 * Test return
	 */
	public function testReturn() {	
		
		$this->assertEquals("string(3) 'Bob'\n", d('Bob', true));
	}
	
	/**
	 * Test Max Depth
	 */
	public function testMaxDepth() {	
		$this->assertEquals("*MAX DEPTH*\n", d('Carol', true, 8));
	}
	
	/**
	 * Test array (1)
	 */
	public function testArray01() {	
		$this->expectOutputString("\narray(1) {\n   [A] => string(1) 'B'\n}\n");
		d(array('A' => 'B'));
	}
	
	/**
	 * Test array (2)
	 */
	public function testArray02() {	
		$this->expectOutputString("\narray(1) {\n   [0] => NULL\n}\n");
		d(array(null));
	}
	
	/**
	 * Test array (3)
	 */
	public function testArray03() {	
		$this->expectOutputString("\narray(2) {\n   [1] => string(1) '2'\n   [2] => int(3)\n}\n");
		d(array('1' => '2', 3));
	}
	
	/**
	 * Test array (4) max depth
	 */
	public function testArray04() {
		$expected = "\narray(1) {\n";
		$expected .= "   [0] => array(1) {\n";
		$expected .= "      [0] => array(1) {\n";
		$expected .= "         [0] => array(1) {\n";
		$expected .= "            [0] => array(1) {\n";
		$expected .= "               [0] => array(1) {\n";
		$expected .= "                  [0] => array(1) {\n";
		$expected .= "                     [0] => array(1) {\n";
		$expected .= "                        [0] => *MAX DEPTH*\n";
		$expected .= "                     }\n";
		$expected .= "                  }\n";
		$expected .= "               }\n";
		$expected .= "            }\n";
		$expected .= "         }\n";
		$expected .= "      }\n";
		$expected .= "   }\n";
		$expected .= "}\n";
		$this->expectOutputString($expected);
		
		d(array(array(array(array(array(array(array(array(0)))))))));
	}
	
	/**
	 * Test object instance (1)
	 */
	public function testObjectInstance01() {	
		$this->expectOutputString("\nobject(Netsilik\Tests\TestObject) {\n   public: \$communal = string(6) 'nature'\n   protected static: \$governmental = string(4) 'park'\n   private: \$individual = string(6) 'garden'\n   dynamic: \$changeling = *RECURSION*\n}\n");
		
		d(new TestObject());
	}
}
